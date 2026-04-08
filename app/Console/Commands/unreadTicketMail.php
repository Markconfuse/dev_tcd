<?php

namespace App\Console\Commands;

use App\Assignment;
use App\CarbonCopy;
use App\Mail\MailTest;
use App\Mail\UnreadRequestNotif;
use App\Ticket;

use Carbon\Carbon;
use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use DB;
use App\TicketNotification;

class unreadTicketMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:unread';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send a test email to verify mail setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $allTickets = Ticket::joinAssign()
            ->leftJoin('vw_crm_accounts as assign_acc', 'assign.owner_id', '=', 'assign_acc.AccountID')
            ->leftJoin('escalated_tickets as et', 'ticket.ticket_id', '=', 'et.ticket_id')
            ->ticketStatusIsNot([4])
            ->ticketIsNotDeleted()
            ->ticketAssignedIsNotDeleted()
            ->where('assign.date_assigned', '<=', Carbon::now()->subHours(24))
            ->where('ticket.date_created', '>=', Carbon::now()->subDays(3))
            ->whereNull('et.ticket_id') // Exclude escalated tickets
            ->ticketIsUnread()
            ->ticketIsUnanswered()
            ->select([
                'ticket.*',
                'ticket.date_created as created_at',
                'assign.date_assigned',
                'assign_acc.AccountName as assigned_to',
                'assign_acc.NickName as assigned_nickname',
                'assign.owner_id as assigned_to_id',
                'assign_acc.Email as assigned_to_email',
            ])
            ->get();

        $ticketsToNotify = collect();

        foreach ($allTickets as $ticket) {
            $assignedAt = Carbon::parse($ticket->date_assigned);
            $hasFirstNotice = TicketNotification::where('ticket_id', $ticket->ticket_id)
                ->where('type', 'unread_warning_1_day')
                ->exists();
            
            // Weekend-safe catch-up: send the first notice anytime within the 3-day cap if missing.
            if (!$hasFirstNotice) {
                $ticket->notification_type = 'unread_warning_1_day';
                $ticketsToNotify->push($ticket);
                TicketNotification::create([
                    'ticket_id' => $ticket->ticket_id,
                    'type' => 'unread_warning_1_day',
                    'sent_at' => Carbon::now()
                ]);
                continue;
            }

            if ($assignedAt->diffInHours(Carbon::now()) >= 72) {
                $recentNotice = TicketNotification::where('ticket_id', $ticket->ticket_id)
                    ->where('type', 'unread_warning_daily')
                    ->where('sent_at', '>=', Carbon::now()->subHours(24))
                    ->exists();

                if (!$recentNotice) {
                    $ticket->notification_type = 'unread_warning_daily';
                    $ticketsToNotify->push($ticket);
                    TicketNotification::create([
                        'ticket_id' => $ticket->ticket_id,
                        'type' => 'unread_warning_daily',
                        'sent_at' => Carbon::now()
                    ]);
                }
            }
        }

        if ($ticketsToNotify->isEmpty()) {
            $this->info('No unread tickets required notification.');
            return;
        }

        $groupedByEmployee = $ticketsToNotify->groupBy(function ($ticket) {
            return ($ticket->assigned_to_id ?? 'unknown') . '|' . ($ticket->notification_type ?? 'unread_warning_1_day');
        });

        foreach ($groupedByEmployee as $assigneeId => $tickets) {
            if ($tickets->count() > 1) {
                $this->sendEmailTableForUser($tickets);
            } else {
                $this->sendEmailSingle($tickets->first());
            }
        }
    }

    private function sendEmailSingle($ticket)
    {
        try {
            $engineerName = $this->displayName($ticket->assigned_nickname ?? null, $ticket->assigned_to ?? null);
            $isReminder = ($ticket->notification_type ?? '') === 'unread_warning_daily';
            $email_assignee = $ticket->assigned_to_email;
            $email_cc = CarbonCopy::getCCEmail($ticket->ticket_id);

            if (empty($email_assignee)) {
                \Log::warning("No assignee email found for ticket ID: {$ticket->ticket_id}");
                return;
            }

            $mail = $this->setupPHPMailer();
            $mail->addAddress(trim($email_assignee));

            $ccList = $this->transformCC($email_cc);
            if ($isReminder) {
                $ccList = array_values(array_unique(array_merge($ccList, $this->escalationAdminEmails())));
            }

            foreach ($ccList as $cc) {
                $mail->addCC($cc);
            }

            $htmlContent = view('mail.unread_tickets', [
                'ticket' => $ticket,
                'display_name' => $engineerName,
                'is_reminder' => $isReminder,
            ])->render();

            $mail->Subject = $isReminder
                ? '(TCD Portal Reminder) ' . $engineerName . ', You have Unread Tickets'
                : '(TCD Portal) ' . $engineerName . ', You have Unread Tickets';
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);

            \Log::info('Sending unread ticket mail', [
                'ticket_id' => $ticket->ticket_id,
                'to' => [$email_assignee],
                'cc' => $ccList,
                'bcc' => $this->transformBCC(),
            ]);

            $mail->send();
            sleep(10);

            \Log::info("✅ Unread single mail sent to: {$email_assignee}");
        } catch (\Exception $e) {
            \Log::error('❌ Unread Single Mail error: ' . $e->getMessage());
        }
    }

    private function sendEmailTableForUser($tickets)
    {
        try {
            $firstTicket = $tickets->first();
            $engineerName = $this->displayName($firstTicket->assigned_nickname ?? null, $firstTicket->assigned_to ?? null);
            $isReminder = ($firstTicket->notification_type ?? '') === 'unread_warning_daily';
            $email_assignee = $firstTicket->assigned_to_email;

            if (empty($email_assignee)) {
                \Log::warning('No assignee email found for unread summary email.', [
                    'assignee_id' => $firstTicket->assigned_to_id ?? null,
                ]);
                return;
            }

            $mail = $this->setupPHPMailer();
            $mail->addAddress(trim($email_assignee));

            $allCCs = $this->collectCcEmails($tickets);
            $ccList = $this->transformCC($allCCs);
            if ($isReminder) {
                $ccList = array_values(array_unique(array_merge($ccList, $this->escalationAdminEmails())));
            }

            foreach ($ccList as $cc) {
                $mail->addCC($cc);
            }

            $htmlContent = view('mail.unread_tickets_table', [
                'tickets' => $tickets,
                'display_name' => $engineerName,
                'is_reminder' => $isReminder,
            ])->render();

            $mail->Subject = $isReminder
                ? '(TCD Portal Reminder) ' . $engineerName . ', You have Unread Tickets'
                : '(TCD Portal) ' . $engineerName . ', You have Unread Tickets';
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);

            \Log::info('Sending unread tickets summary', [
                'assignee_id' => $firstTicket->assigned_to_id ?? null,
                'ticket_count' => $tickets->count(),
                'to' => [$email_assignee],
                'cc' => $ccList,
                'bcc' => $this->transformBCC(),
            ]);

            $mail->send();
            sleep(10);

            \Log::info("✅ Unread table mail sent to: {$email_assignee} (Count: {$tickets->count()})");

        } catch (\Exception $e) {
            \Log::error('❌ Unread Table Mail error: ' . $e->getMessage());
        }
    }

    private function setupPHPMailer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = config('mail.host');
        $mail->SMTPAuth = true;
        $mail->Username = config('mail.username');
        $mail->Password = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port = config('mail.port');
        $mail->isHTML(true);
        $mail->setFrom('noreply-tcdportal-support@ics.com.ph', 'NoReply:TCDPORTALSupport');

        foreach ($this->transformBCC() as $bcc) {
            $mail->addBCC($bcc);
        }

        return $mail;
    }

    private function transformSendTo($email_assignee)
    {
        return $this->normalizeEmails($email_assignee);
    }

    private function transformCC($email_cc)
    {
        return $this->normalizeEmails($email_cc);
    }

    private function transformBCC()
    {
        return $this->normalizeEmails('dramos@ics.com.ph,mescario@ics.com.ph,randres@ics.com.ph');
    }

    private function collectCcEmails($tickets)
    {
        $cc_emails = [];

        foreach ($tickets as $ticket) {
            $cc_emails[] = CarbonCopy::getCCEmail($ticket->ticket_id);
        }

        return implode(',', array_filter($cc_emails));
    }

    private function normalizeEmails($emails)
    {
        if (empty($emails)) {
            return [];
        }

        $normalized = preg_replace('/[\s;]+/', ',', strtolower($emails));
        $candidates = array_filter(array_map('trim', explode(',', $normalized)));

        return array_values(array_filter($candidates, function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL);
        }));
    }

    private function displayName($nickname, $fullName)
    {
        $nickname = trim((string) $nickname);
        if ($nickname !== '') {
            return $nickname;
        }

        $firstName = trim((string) strtok(trim((string) $fullName), ' '));
        return $firstName !== '' ? $firstName : 'Engineer';
    }

    private function escalationAdminEmails()
    {
        return [
            'npacheco@ics.com.ph',
            'jwong@ics.com.ph',
            'macosta@ics.com.ph',
        ];
    }
}
