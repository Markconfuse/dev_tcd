<?php
namespace App\Console\Commands;
use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Ticket;
use Carbon\Carbon;
use App\TicketNotification;
class SendGoogleChatEscalationWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:send-chat-escalation';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends escalated ticket notifications to Google Chat via Webhook';
    /**
     * Execute the console command.
     */
    public function handle()
    {
        $webhookUrl = env('GOOGLE_ESCALATION_WEBHOOK_URL', 'https://chat.googleapis.com/v1/spaces/AAQA9Rc88ms/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=qlRBJG8lQXUQxUK6_wDQjX0uXHASF3vveg5fYs9-xTQ');
        if (empty($webhookUrl)) {
            $this->error('GOOGLE_ESCALATION_WEBHOOK_URL is not set.');
            return;
        }

        $escalatedTickets = Ticket::join('escalated_tickets', 'ticket.ticket_id', '=', 'escalated_tickets.ticket_id')
            ->leftJoin('vw_crm_accounts as esrid', 'ticket.requestor_id', '=', 'esrid.AccountID')
            ->ticketIsNotDeleted()
            ->where('escalated_tickets.is_checked', 0)
            ->where('escalated_tickets.is_approved', 0)
            ->select([
                'ticket.ticket_id',
                'ticket.subject',
                'escalated_tickets.escalation_date',
                'escalated_tickets.escalated_reply',
                'esrid.AccountName as requestor_name'
            ])
            ->get();
        $ticketsToNotify = collect();
        $now = Carbon::now();
        $baseUrl = rtrim(env('APP_URL', config('app.url')), '/');
        foreach ($escalatedTickets as $ticket) {
            $escalationDate = Carbon::parse($ticket->escalation_date);
            $alreadyNotified = TicketNotification::where('ticket_id', $ticket->ticket_id)
                ->where('type', 'escalation_webhook')
                ->where('sent_at', '>=', $escalationDate)
                ->exists();
            if (!$alreadyNotified) {
                $ticketsToNotify->push($ticket);
                TicketNotification::create([
                    'ticket_id' => $ticket->ticket_id,
                    'type' => 'escalation_webhook',
                    'sent_at' => $now,
                ]);
            }
        }
        if ($ticketsToNotify->isEmpty()) {
            $this->info('No new escalated tickets to notify.');
            return;
        }
        $this->info('Sending ' . $ticketsToNotify->count() . ' Escalated tickets to Google Chat...');
        foreach ($ticketsToNotify as $ticket) {
            $message = "🚨 *Escalation Requested*\n";
            $message .= "*ID:* {$ticket->ticket_id}\n";
            $message .= "*Subject:* {$ticket->subject}\n";
            $message .= "*Requestor:* " . ($ticket->requestor_name ?? 'Unknown') . "\n";
            $message .= "*Date Escalated:* {$ticket->escalation_date}\n";
            if (!empty($ticket->escalated_reply)) {
                $message .= "*Escalation Reply:* " . strip_tags($ticket->escalated_reply) . "\n";
            }
            $message .= "*Link:* " . $baseUrl . '/view-request/' . base64_encode($ticket->ticket_id);
            $this->sendToGoogleChat($webhookUrl, $message);
            $this->info("Sent Escalation Ticket #{$ticket->ticket_id}");
            sleep(1);
        }
        $this->info('All webhook messages sent successfully!');
    }
    /**
     * Helper to fire the CURL request to Google Chat Webhook
     */
    private function sendToGoogleChat($webhookUrl, $message)
    {
        $client = new Client();
        $client->post($webhookUrl, [
            'headers' => [
                'Content-Type' => 'application/json; charset=UTF-8',
            ],
            'body' => json_encode([
                'text' => $message,
            ]),
        ]);
    }
}
