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
use App\Mail\UnansweredRequestNotif;
use App\TicketNotification;
use App\Helpers\MailHelper;

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
            ->where('assign.date_assigned', '<=', Carbon::now()->subDay())
            ->where('assign.date_assigned', '>=', Carbon::now()->subDays(7))
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

        if ($allTickets->isEmpty()) {
            $this->info('No unanswered tickets found.');
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
                if ($hoursSinceAssigned >= 1) {
                    $ticket->notification_type = 'unanswered_warning_1_day';
                    $ticketsToNotify->push($ticket);
                }
            } else {
                $hoursSinceLastNotice = Carbon::parse($notif->sent_at)->diffInHours(Carbon::now());
                $currentType = $notif->type;

                if ($currentType === 'unanswered_warning_1_day') {
                    if ($hoursSinceLastNotice >= 48) {
                        $ticket->notification_type = 'unanswered_warning_3_day';
                        $ticketsToNotify->push($ticket);
                    }
                }
                elseif ($currentType === 'unanswered_warning_3_day') {
                    if ($hoursSinceLastNotice >= 48) {
                        $ticket->notification_type = 'unanswered_warning_5_day';
                        $ticketsToNotify->push($ticket);
                    }
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
            $engineerName = MailHelper::displayName($ticket->assigned_nickname ?? null, $ticket->assigned_to ?? null);
            $warningStage = MailHelper::resolveWarningStage($ticket->notification_type ?? null);
            $email_assignee = $ticket->assigned_to_email;
            $email_cc = MailHelper::getEscalationEmails($warningStage, 'unanswered');

            if (empty($email_assignee)) {
                \Log::warning("No assignee email found for ticket ID: {$ticket->ticket_id}");
                return;
            }

            $ccList = MailHelper::normalizeEmails($email_cc);

            if ($warningStage === 'day5') {
                $subject = '(TCD Portal Reminder - 5th Day) ' . $engineerName . ', You have Unanswered Tickets';
            } else if ($warningStage === 'day3') {
                $subject = '(TCD Portal Reminder - 3rd Day) ' . $engineerName . ', You have Unanswered Tickets';
            } else {
                $subject = '(TCD Portal Reminder)' . $engineerName . ', You have Unanswered Tickets';
            }

            $viewData = [
                'ticket' => $ticket,
                'display_name' => $engineerName,
                'warning_stage' => $warningStage,
            ];

            \Log::info('Sending unanswered ticket mail', [
                'ticket_id' => $ticket->ticket_id,
                'to' => [$email_assignee],
                'cc' => $ccList,
                'bcc' => MailHelper::getDefaultBCC(),
            ]);

            Mail::to(trim($email_assignee))
                ->cc($ccList)
                ->bcc(MailHelper::getDefaultBCC())
                ->send(new UnansweredRequestNotif($subject, 'mail.unanswered_tickets', $viewData));

            TicketNotification::logNotification($ticket->ticket_id, $ticket->notification_type);

            \Log::info("✅ Single mail queued/sent to: {$email_assignee}");
        } catch (\Exception $e) {
            \Log::error('❌ Single Mail error: ' . $e->getMessage());
        }
    }

    private function sendEmailTableForUser($tickets)
    {
        try {
            $firstTicket = $tickets->first();
            $engineerName = MailHelper::displayName($firstTicket->assigned_nickname ?? null, $firstTicket->assigned_to ?? null);
            $warningStage = MailHelper::resolveWarningStage($firstTicket->notification_type ?? null);
            $email_assignee = $firstTicket->assigned_to_email;
            $email_cc = MailHelper::getEscalationEmails($warningStage, 'unanswered');

            if (empty($email_assignee)) {
                \Log::warning('No assignee email found for summary email.', [
                    'assignee_id' => $firstTicket->assigned_to_id ?? null,
                ]);
                return;
            }

            $ccList = MailHelper::normalizeEmails($email_cc);

            if ($warningStage === 'day5') {
                $subject = '(TCD Portal Reminder - 5th Day) ' . $engineerName . ', You have ' . $tickets->count() . ' Unanswered Tickets.';
            } else if ($warningStage === 'day3') {
                $subject = '(TCD Portal Reminder - 3rd Day) ' . $engineerName . ', You have ' . $tickets->count() . ' Unanswered Tickets.';
            } else {
                $subject = '(TCD Portal Reminder) ' . $engineerName . ', You have ' . $tickets->count() . ' Unanswered Tickets.';
            }

            $viewData = [
                'tickets' => $tickets,
                'display_name' => $engineerName,
                'warning_stage' => $warningStage,
            ];

            \Log::info('Sending unanswered tickets summary', [
                'assignee_id' => $firstTicket->assigned_to_id ?? null,
                'ticket_count' => $tickets->count(),
                'to' => [$email_assignee],
                'cc' => $ccList,
                'bcc' => MailHelper::getDefaultBCC(),
            ]);

            Mail::to(trim($email_assignee))
                ->cc($ccList)
                ->bcc(MailHelper::getDefaultBCC())
                ->send(new UnansweredRequestNotif($subject, 'mail.unanswered_tickets_table', $viewData));

            foreach ($tickets as $t) {
                TicketNotification::logNotification($t->ticket_id, $t->notification_type);
            }

            \Log::info("✅ Table mail queued/sent to: {$email_assignee} (Count: {$tickets->count()})");

        } catch (\Exception $e) {
            \Log::error('❌ Table Mail error: ' . $e->getMessage());
        }
    }
}
