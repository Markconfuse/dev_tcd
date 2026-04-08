<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use App\TicketNotification;

class SendGoogleChatWebhook extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'webhook:send-chat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends unassigned ticket notifications to Google Chat via Webhook one by one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $webhookUrl = env('GOOGLE_WEBHOOK_URL');

        if (empty($webhookUrl)) {
            $this->error('GOOGLE_WEBHOOK_URL is not set in the .env file.');
            return;
        }

        // Fetching Unassigned Tickets (Status ID = 1) older than 1 hour
        $unassignedTickets = Ticket::leftJoin('vw_crm_accounts as esrid', 'ticket.requestor_id', '=', 'esrid.AccountID')
            ->ticketIsNotDeleted()
            ->statusID(1)
            ->ExcludeAppsdev()
            ->where('ticket.date_created', '<=', Carbon::now()->subHour())
            ->where('ticket.date_created', '>=', Carbon::now()->subDays(3)) // Cap at 3 days
            ->select([
                'ticket.ticket_id',
                'ticket.subject',
                'ticket.date_created',
                'esrid.AccountName as requestor_name'
            ])
            ->get();

        $ticketsToNotify = collect();

        foreach ($unassignedTickets as $ticket) {
            $notified = TicketNotification::where('ticket_id', $ticket->ticket_id)
                ->where('type', 'unassigned_1_hour')
                ->exists();

            if (!$notified) {
                $ticketsToNotify->push($ticket);
                TicketNotification::create([
                    'ticket_id' => $ticket->ticket_id,
                    'type' => 'unassigned_1_hour',
                    'sent_at' => Carbon::now()
                ]);
            }
        }

        if ($ticketsToNotify->isEmpty()) {
            $this->info('No unassigned tickets reached 1 hour or they were already notified.');
            return;
        }

        $this->info('Sending ' . $ticketsToNotify->count() . ' Unassigned tickets to Google Chat...');

        foreach ($ticketsToNotify as $ticket) {
            $message = "⚠️ *Unassigned Ticket Notification*\n";
            $message .= "*ID:* {$ticket->ticket_id}\n";
            $message .= "*Subject:* {$ticket->subject}\n";
            $message .= "*Requestor:* " . ($ticket->requestor_name ?? 'Unknown') . "\n";
            $message .= "*Date Created:* {$ticket->date_created}\n";
            $message .= "*Link:* " . url('/view-request/' . base64_encode($ticket->ticket_id));

            $this->sendToGoogleChat($webhookUrl, $message);
            $this->info("Sent Unassigned Ticket #{$ticket->ticket_id}");
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
