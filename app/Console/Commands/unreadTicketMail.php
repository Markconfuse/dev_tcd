<?php

namespace App\Console\Commands;

use App\Assignment;
use App\CarbonCopy;
use App\Mail\MailTest;
use App\Mail\UnreadRequestNotif;
use App\Ticket;

use PHPMailer\PHPMailer\PHPMailer;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use DB;

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
\Log::info('handle unread');
        try {
            // $ticket_id = 36518;
            $tickets = Ticket::joinAssign()
                ->ticketStatusIsNot([4])
                ->within(24)
                ->ticketIsNotDeleted()
                ->ticketAssignedIsNotDeleted()
                ->ticketIsUnread()
                ->ticketIsUnanswered()
                // ->ticketID($ticket_id)
                // ->first();
                ->take(4)->get();

            foreach ($tickets as $ticket) {
                // dd($ticket->subject);
                $email_subject = $ticket->subject;
                $email_content = 'This email is for testing, Please ignore. Thanks!';
                // $email_content = $ticket->ticket_content;
                $email_cc = 'mescario@ics.com.ph';
                // $email_assignee = Assignment::getAssignedEmail($ticket->ticket_id);
                $eTo = Assignment::getAssignedEmail($ticket->ticket_id);
                \Log::info($eTo);
                
                $email_assignee = 'mescario@ics.com.ph';
				\Log::info($email_assignee);

                // $email_cc = CarbonCopy::getCCEmail($ticket->ticket_id);

                $log_message = sprintf(
                    "Ticket ID: %d | Subject: %s | Content: %s | Carbon Copy: %s | Assignee: %s",
                    $ticket->ticket_id,
                    $email_subject ?: 'No Subject',
                    $email_content ?: 'No Content',
                    $email_cc ?: 'No carbon copy email found',
                    $email_assignee ?: 'No assignee email found'
                );


                // \Log::info($log_message);

                $array_email_to = $this->transformSendTo($email_assignee);
                $array_email_cc  = $this->transformCC($email_cc);
                $array_email_bcc  = $this->transformBCC($email_cc);


                $mailable = new UnreadRequestNotif($email_subject, $email_content);

                // Render Blade to HTML
                $htmlContent = view($mailable->build()->view, ['content' => $mailable->_content])->render();


                $mail = new PHPMailer(true);
                $mail->isSMTP();

                $mail->Host       = 'smtp-relay.gmail.com';
                $mail->SMTPAuth   = false;
                $mail->SMTPSecure = null;
                $mail->Port       = 587;

                // Sender
                $mail->setFrom('noreply-tcdportal-support@ics.com.ph', 'NoReply:TCDPORTALSupport');

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
            }
        } catch (\Exception $e) {
            return '❌ Failed: ' . $e->getMessage();
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
