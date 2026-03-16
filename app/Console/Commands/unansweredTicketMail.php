<?php

namespace App\Console\Commands;

use App\Assignment;
use App\CarbonCopy;
use App\Mail\MailTest;
use App\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use DB;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
use App\Mail\UnansweredRequestNotif;

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
            ->ticketStatusIsNot([4])
            // add condiition for only 24 hours old tickets
            ->ticketIsNotDeleted()
            ->ticketAssignedIsNotDeleted()
            ->ticketIsRead()
            ->ticketIsUnanswered()
            ->select([
                'ticket.*',
                'ticket.date_created as created_at',
                'assign_acc.AccountName as assigned_to',
                'assign.owner_id as assigned_to_id',
                'assign_acc.Email as assigned_to_email',
            ])
            ->get();

        if ($allTickets->isEmpty()) {
            $this->info('No unanswered tickets found.');
            return;
        }

        $groupedByEmployee = $allTickets->groupBy('assigned_to_id');

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
            $email_subject = $ticket->subject;
            $email_content = 'You have 1 unanswered ticket requiring attention.';
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
                'ticket' => $ticket
            ])->render();

            $mail->Subject = 'Action Required: ' . $email_subject;
            $mail->Body    = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);

            \Log::info('Sending unanswered ticket mail', [
                'ticket_id' => $ticket->ticket_id,
                'to' => [$email_assignee],
                'cc' => $this->transformCC($email_cc),
                'bcc' => $this->transformBCC(),
            ]);

            $mail->send();

            \Log::info("✅ Single mail sent to: {$email_assignee}");
        } catch (Exception $e) {
            \Log::error('❌ Single Mail error: ' . $e->getMessage());
        }
    }

    private function sendEmailTableForUser($tickets)
    {
        try {
            $firstTicket = $tickets->first();
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
                'assigned_to' => $firstTicket->assigned_to,
            ])->render();

            $mail->Subject = 'Summary: ' . $tickets->count() . ' Unanswered Tickets - ' . date('Y-m-d');
            $mail->Body    = $htmlContent;
            $mail->AltBody = strip_tags($htmlContent);

            \Log::info('Sending unanswered tickets summary', [
                'assignee_id' => $firstTicket->assigned_to_id ?? null,
                'ticket_count' => $tickets->count(),
                'to' => [$email_assignee],
                'cc' => $this->transformCC($allCCs),
                'bcc' => $this->transformBCC(),
            ]);

            $mail->send();

            \Log::info("✅ Table mail sent to: {$email_assignee} (Count: {$tickets->count()})");

        } catch (Exception $e) {
            \Log::error('❌ Table Mail error: ' . $e->getMessage());
        }
    }

    private function setupPHPMailer()
    {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host       = config('mail.host');
        $mail->SMTPAuth   = true;
        $mail->Username   = config('mail.username');
        $mail->Password   = config('mail.password');
        $mail->SMTPSecure = config('mail.encryption');
        $mail->Port       = config('mail.port');
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
}
