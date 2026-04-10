<?php

namespace App\Console\Commands;

use App\Assignment;
use App\CarbonCopy;
use App\Mail\MailTest;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use DB;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Mail\UnansweredRequestNotif;
use App\TicketNotification;

class unansweredTicketMail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:unanswered';

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
            ->whereNull('et.ticket_id') // Exclude escalated tickets
            ->ticketIsRead()
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
            $hoursSinceAssigned = $assignedAt->diffInHours(Carbon::now());

            $dayOneNotice = TicketNotification::where('ticket_id', $ticket->ticket_id)
                ->where('type', 'unanswered_warning_1_day')
                ->orderByDesc('sent_at')
                ->first();

            $hasDayOneNotice = !is_null($dayOneNotice);

            $threeDayNotice = TicketNotification::where('ticket_id', $ticket->ticket_id)
                ->whereIn('type', ['unanswered_warning_3_day', 'unanswered_warning_1_day'])
                ->orderByDesc('sent_at')
                ->first();

            $hasThreeDayNotice = !is_null($threeDayNotice);

            $hasFiveDayNotice = TicketNotification::where('ticket_id', $ticket->ticket_id)
                ->where('type', 'unanswered_warning_5_day')
                ->exists();

            // Stage 0: notify at/after 1st day if not yet sent.
            if ($hoursSinceAssigned >= 24 && !$hasDayOneNotice) {
                $ticket->notification_type = 'unanswered_warning_1_day';
                $ticketsToNotify->push($ticket);
                TicketNotification::create([
                    'ticket_id' => $ticket->ticket_id,
                    'type' => 'unanswered_warning_1_day',
                    'sent_at' => Carbon::now()
                ]);
                continue;
            }

            // Stage 1: notify at 3rd day, 48 hours after Day 1 notice.
            if (!$hasThreeDayNotice && $hasDayOneNotice) {
                $hoursSinceDayOneNotice = Carbon::parse($dayOneNotice->sent_at)->diffInHours(Carbon::now());
                if ($hoursSinceDayOneNotice >= 48) {
                    $ticket->notification_type = 'unanswered_warning_3_day';
                    $ticketsToNotify->push($ticket);
                    TicketNotification::create([
                        'ticket_id' => $ticket->ticket_id,
                        'type' => 'unanswered_warning_3_day',
                        'sent_at' => Carbon::now()
                    ]);
                    continue;
                }
            }

            // Stage 2: notify once 48 hours after 3-day notice to handle weekend/catch-up delays.
            if ($hasThreeDayNotice && !$hasFiveDayNotice) {
                $hoursSinceThreeDayNotice = Carbon::parse($threeDayNotice->sent_at)->diffInHours(Carbon::now());

                if ($hoursSinceThreeDayNotice >= 48) {
                    $ticket->notification_type = 'unanswered_warning_5_day';
                    $ticketsToNotify->push($ticket);
                    TicketNotification::create([
                        'ticket_id' => $ticket->ticket_id,
                        'type' => 'unanswered_warning_5_day',
                        'sent_at' => Carbon::now()
                    ]);
                }
            }
        }

        if ($ticketsToNotify->isEmpty()) {
            $this->info('No unanswered tickets required notification.');
            return;
        }

        $groupedByEmployee = $ticketsToNotify->groupBy(function ($ticket) {
            return ($ticket->assigned_to_id ?? 'unknown') . '|' . ($ticket->notification_type ?? 'unanswered_warning_1_day');
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
            $warningStage = $this->resolveWarningStage($ticket->notification_type ?? null);
            $email_assignee = $ticket->assigned_to_email;
            $email_cc = CarbonCopy::getCCEmail($ticket->ticket_id);

            if (empty($email_assignee)) {
                \Log::warning("No assignee email found for ticket ID: {$ticket->ticket_id}");
                return;
            }

            $mail = $this->setupPHPMailer();
            $mail->addAddress(trim($email_assignee));

            foreach ($this->transformCC($email_cc) as $cc) {
                $mail->addCC($cc);
            }

            $htmlContent = view('mail.unanswered_tickets', [
                'ticket' => $ticket,
                'display_name' => $engineerName,
                'warning_stage' => $warningStage,
            ])->render();

            if ($warningStage === 'day5') {
                $mail->Subject = '(TCD Portal Reminder - 5th Day) ' . $engineerName . ', You have Unanswered Tickets';
            } else if ($warningStage === 'day3') {
                $mail->Subject = '(TCD Portal Reminder - 3rd Day) ' . $engineerName . ', You have Unanswered Tickets';
            } else {
                $mail->Subject = '(TCD Portal Reminder - 1st Day) ' . $engineerName . ', You have Unanswered Tickets';
            }
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);

            \Log::info('Sending unanswered ticket mail', [
                'ticket_id' => $ticket->ticket_id,
                'to' => [$email_assignee],
                'cc' => $this->transformCC($email_cc),
                'bcc' => $this->transformBCC(),
            ]);

            $mail->send();
            sleep(10);

            \Log::info("✅ Single mail sent to: {$email_assignee}");
        } catch (Exception $e) {
            \Log::error('❌ Single Mail error: ' . $e->getMessage());
        }
    }

    private function sendEmailTableForUser($tickets)
    {
        try {
            $firstTicket = $tickets->first();
            $engineerName = $this->displayName($firstTicket->assigned_nickname ?? null, $firstTicket->assigned_to ?? null);
            $warningStage = $this->resolveWarningStage($firstTicket->notification_type ?? null);
            $email_assignee = $firstTicket->assigned_to_email;

            if (empty($email_assignee)) {
                \Log::warning('No assignee email found for summary email.', [
                    'assignee_id' => $firstTicket->assigned_to_id ?? null,
                ]);
                return;
            }

            $mail = $this->setupPHPMailer();
            $mail->addAddress(trim($email_assignee));

            $allCCs = $this->collectCcEmails($tickets);
            foreach ($this->transformCC($allCCs) as $cc) {
                $mail->addCC($cc);
            }

            $htmlContent = view('mail.unanswered_tickets_table', [
                'tickets' => $tickets,
                'display_name' => $engineerName,
                'warning_stage' => $warningStage,
            ])->render();

            if ($warningStage === 'day5') {
                $mail->Subject = '(TCD Portal Reminder - 5th Day) ' . $engineerName . ', You have ' . $tickets->count() . ' Unanswered Tickets.';
            } else if ($warningStage === 'day3') {
                $mail->Subject = '(TCD Portal Reminder - 3rd Day) ' . $engineerName . ', You have ' . $tickets->count() . ' Unanswered Tickets.';
            } else {
                $mail->Subject = '(TCD Portal Reminder - 1st Day) ' . $engineerName . ', You have ' . $tickets->count() . ' Unanswered Tickets.';
            }
            $mail->Body = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);

            \Log::info('Sending unanswered tickets summary', [
                'assignee_id' => $firstTicket->assigned_to_id ?? null,
                'ticket_count' => $tickets->count(),
                'to' => [$email_assignee],
                'cc' => $this->transformCC($allCCs),
                'bcc' => $this->transformBCC(),
            ]);

            $mail->send();
            sleep(10);

            \Log::info("✅ Table mail sent to: {$email_assignee} (Count: {$tickets->count()})");

        } catch (Exception $e) {
            \Log::error('❌ Table Mail error: ' . $e->getMessage());
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

    private function resolveWarningStage($notificationType)
    {
        if ($notificationType === 'unanswered_warning_5_day') {
            return 'day5';
        }

        if ($notificationType === 'unanswered_warning_3_day') {
            return 'day3';
        }

        return 'day1';
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
}
