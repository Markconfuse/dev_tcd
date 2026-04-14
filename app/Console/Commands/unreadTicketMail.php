<?php

namespace App\Console\Commands;

use App\Assignment;
use App\CarbonCopy;
use App\Mail\MailTest;
use App\Mail\UnreadRequestNotif;
use App\Ticket;

use Carbon\Carbon;
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
            ->where('assign.date_assigned', '>=', Carbon::now()->subDays(30))
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

        if ($allTickets->isEmpty()) {
            $this->info('No unread tickets found.');
            return;
        }

        $ticketIds = $allTickets->pluck('ticket_id');
        $notifications = TicketNotification::getBulkNotifications($ticketIds);

        $ticketsToNotify = collect();

        foreach ($allTickets as $ticket) {
            $assignedAt = Carbon::parse($ticket->date_assigned);
            $hoursSinceAssigned = $assignedAt->diffInHours(Carbon::now());

            $notif = $notifications->get($ticket->ticket_id);

            if (!$notif) {
                // Stage 0: notify at/after 1st day if not yet sent.
                if ($hoursSinceAssigned >= 24) {
                    $ticket->notification_type = 'unread_warning_1_day';
                    $ticketsToNotify->push($ticket);
                }
            } else {
                $hoursSinceLastNotice = Carbon::parse($notif->sent_at)->diffInHours(Carbon::now());
                $currentType = $notif->type;

                // Stage 1: change type to day 3
                if ($currentType === 'unread_warning_1_day') {
                    if ($hoursSinceLastNotice >= 48) {
                        $ticket->notification_type = 'unread_warning_3_day';
                        $ticketsToNotify->push($ticket);
                    }
                }
                // Stage 2: change type to day 5
                elseif (in_array($currentType, ['unread_warning_3_day', 'unread_warning_daily'])) {
                    if ($hoursSinceLastNotice >= 48) {
                        $ticket->notification_type = 'unread_warning_5_day';
                        $ticketsToNotify->push($ticket);
                    }
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
            $warningStage = $this->resolveWarningStage($ticket->notification_type ?? null);
            $email_assignee = $ticket->assigned_to_email;
            $email_cc = CarbonCopy::getCCEmail($ticket->ticket_id);

            if (empty($email_assignee)) {
                \Log::warning("No assignee email found for ticket ID: {$ticket->ticket_id}");
                return;
            }

            $ccList = $this->transformCC($email_cc);
            if (in_array($warningStage, ['day3', 'day5'])) {
                $ccList = array_values(array_unique(array_merge($ccList, $this->escalationAdminEmails())));
            }

            if ($warningStage === 'day5') {
                $subject = '(TCD Portal Reminder - 5th Day) ' . $engineerName . ', You have Unread Tickets';
            } else if ($warningStage === 'day3') {
                $subject = '(TCD Portal Reminder - 3rd Day) ' . $engineerName . ', You have Unread Tickets';
            } else {
                $subject = '(TCD Portal Reminder - 1st Day) ' . $engineerName . ', You have Unread Tickets';
            }

            $viewData = [
                'ticket' => $ticket,
                'display_name' => $engineerName,
                'warning_stage' => $warningStage,
            ];

            \Log::info('Sending unread ticket mail', [
                'ticket_id' => $ticket->ticket_id,
                'to' => [$email_assignee],
                'cc' => $ccList,
                'bcc' => $this->transformBCC(),
            ]);

            Mail::to(trim($email_assignee))
                ->cc($ccList)
                ->bcc($this->transformBCC())
                ->send(new UnreadRequestNotif($subject, 'mail.unread_tickets', $viewData));

            TicketNotification::logNotification($ticket->ticket_id, $ticket->notification_type);

            \Log::info("✅ Unread single mail queued/sent to: {$email_assignee}");
        } catch (\Exception $e) {
            \Log::error('❌ Unread Single Mail error: ' . $e->getMessage());
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
                \Log::warning('No assignee email found for unread summary email.', [
                    'assignee_id' => $firstTicket->assigned_to_id ?? null,
                ]);
                return;
            }

            $allCCs = $this->collectCcEmails($tickets);
            $ccList = $this->transformCC($allCCs);
            if (in_array($warningStage, ['day3', 'day5'])) {
                $ccList = array_values(array_unique(array_merge($ccList, $this->escalationAdminEmails())));
            }

            if ($warningStage === 'day5') {
                $subject = '(TCD Portal Reminder - 5th Day) ' . $engineerName . ', You have Unread Tickets';
            } else if ($warningStage === 'day3') {
                $subject = '(TCD Portal Reminder - 3rd Day) ' . $engineerName . ', You have Unread Tickets';
            } else {
                $subject = '(TCD Portal Reminder - 1st Day) ' . $engineerName . ', You have Unread Tickets';
            }

            $viewData = [
                'tickets' => $tickets,
                'display_name' => $engineerName,
                'warning_stage' => $warningStage,
            ];

            \Log::info('Sending unread tickets summary', [
                'assignee_id' => $firstTicket->assigned_to_id ?? null,
                'ticket_count' => $tickets->count(),
                'to' => [$email_assignee],
                'cc' => $ccList,
                'bcc' => $this->transformBCC(),
            ]);

            Mail::to(trim($email_assignee))
                ->cc($ccList)
                ->bcc($this->transformBCC())
                ->send(new UnreadRequestNotif($subject, 'mail.unread_tickets_table', $viewData));

            foreach ($tickets as $t) {
                TicketNotification::logNotification($t->ticket_id, $t->notification_type);
            }

            \Log::info("✅ Unread table mail queued/sent to: {$email_assignee} (Count: {$tickets->count()})");

        } catch (\Exception $e) {
            \Log::error('❌ Unread Table Mail error: ' . $e->getMessage());
        }
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

    private function resolveWarningStage($notificationType)
    {
        if ($notificationType === 'unread_warning_5_day') {
            return 'day5';
        }

        if ($notificationType === 'unread_warning_3_day' || $notificationType === 'unread_warning_daily') {
            return 'day3';
        }

        return 'day1';
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
