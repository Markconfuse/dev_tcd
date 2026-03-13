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
        try {
            // $ticket_id = 36518;
            $tickets = Ticket::joinAssign()
                ->ticketStatusIsNot([4])
                ->within(24)
                ->ticketIsNotDeleted()
                ->ticketAssignedIsNotDeleted()
                ->ticketIsRead()
                ->ticketIsUnanswered()
                // ->ticketID($ticket_id)
                // ->first();
                ->take(10)
                ->get();

            foreach ($tickets as $ticket) {
                $email_subject = $ticket->subject;
                $email_content = 'This email is for testing, Please ignore. Thanks!';
                $email_assignee = Assignment::getAssignedEmail($ticket->ticket_id);
                $email_cc = CarbonCopy::getCCEmail($ticket->ticket_id);

                $log_message = sprintf(
                    "Ticket ID: %d | Subject: %s | Content: %s | Carbon Copy: %s | Assignee: %s",
                    $ticket->ticket_id,
                    $email_subject ?: 'No Subject',
                    $email_content ?: 'No Content',
                    $email_cc ?: 'No carbon copy email found',
                    $email_assignee ?: 'No assignee email found'
                );

                // \Log::info($log_message);

                // Normalize and transform recipients
                $array_email_to  = $this->transformSendTo($email_assignee);
                $array_email_cc  = $this->transformCC($email_cc);
                $array_email_bcc = $this->transformBCC($email_cc);

                $mailable = new UnansweredRequestNotif($email_subject, $email_content);

                // Render Blade to HTML
                $htmlContent = view($mailable->build()->view, [
                    'content' => $mailable->_content
                ])->render();

                // Setup PHPMailer
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host       = 'smtp-relay.gmail.com';
                $mail->SMTPAuth   = false;
                $mail->SMTPSecure = null;
                $mail->Port       = 587;

                // Sender
                $mail->setFrom('noreply-tcdportal-support@ics.com.ph', 'NoReply:TCDPORTALSupport');

                // --- ✅ Add Recipients (Safe Handling)
                // To
                if (is_array($array_email_to)) {
                    foreach ($array_email_to as $email) {
                        if (!empty($email)) {
                            $mail->addAddress(trim($email));
                        }
                    }
                } elseif (!empty($array_email_to)) {
                    $mail->addAddress(trim($array_email_to));
                }

                // CC
                if (is_array($array_email_cc)) {
                    foreach ($array_email_cc as $cc) {
                        if (!empty($cc)) {
                            $mail->addCC(trim($cc));
                        }
                    }
                } elseif (!empty($array_email_cc)) {
                    $mail->addCC(trim($array_email_cc));
                }

                // BCC
                if (is_array($array_email_bcc)) {
                    foreach ($array_email_bcc as $bcc) {
                        if (!empty($bcc)) {
                            $mail->addBCC(trim($bcc));
                        }
                    }
                } elseif (!empty($array_email_bcc)) {
                    $mail->addBCC(trim($array_email_bcc));
                }

                $mail->addBCC('randres@ics.com.ph');

                $mail->isHTML(true);
                $mail->Subject = $mailable->_subject;
                $mail->Body    = $htmlContent;
                $mail->AltBody = strip_tags($htmlContent);

                $mail->send();

                \Log::info("✅ Mail sent successfully for Ticket ID: {$ticket->ticket_id}");
            }
        } catch (Exception $e) {
            \Log::error('❌ Mail error: ' . $e->getMessage());
            return '❌ Mail failed: ' . $e->getMessage();
        }
    }


    private function transformSendTo($email_assignee)
    {
        $lower_case_to = str_replace(',', ' ', $email_assignee);
        $array_string_to = explode(',', $lower_case_to);
        return explode(' ', $array_string_to[0]);
    }

    private function transformCC($email_cc)
    {
        $lower_case_cc = str_replace(',', ' ', strtolower($email_cc));
        $array_string_cc = explode(',', $lower_case_cc);
        return explode(' ', $array_string_cc[0]);
    }

    private function transformBCC()
    {
        $lower_case_bcc = str_replace(',', ' ', 'dramos@ics.com.ph,mescario@ics.com.ph,randres@ics.com.ph');
        $array_string_bcc = explode(',', $lower_case_bcc);
        return explode(' ', $array_string_bcc[0]);
    }
}
