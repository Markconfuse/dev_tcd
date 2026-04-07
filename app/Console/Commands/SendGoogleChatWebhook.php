<?php

namespace App\Console\Commands;

use GuzzleHttp\Client;
use Illuminate\Console\Command;
use App\Ticket;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

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
    protected $description = 'Sends unanswered ticket notifications to Google Chat via Webhook one by one';

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

        // Fetching Unanswered Tickets (Read but not answered)
        $unansweredTickets = Ticket::joinAssign()
            ->leftJoin('vw_crm_accounts as assign_acc', 'assign.owner_id', '=', 'assign_acc.AccountID')
            ->ticketStatusIsNot([4])
            ->ticketIsNotDeleted()
            ->ticketAssignedIsNotDeleted()
            ->whereBetween('assign.date_assigned', [Carbon::now()->subHours(48), Carbon::now()])
            ->ticketIsRead()
            ->ticketIsUnanswered()
            ->select([
                'ticket.ticket_id',
                'ticket.subject',
                'ticket.date_created',
                'assign_acc.AccountName as assigned_to'
            ])
            ->get();

        $unreadTickets = Ticket::joinAssign()
            ->leftJoin('vw_crm_accounts as assign_acc', 'assign.owner_id', '=', 'assign_acc.AccountID')
            ->ticketStatusIsNot([4])
            ->ticketIsNotDeleted()
            ->ticketAssignedIsNotDeleted()
            ->whereBetween('assign.date_assigned', [Carbon::now()->subHours(48), Carbon::now()])
            ->ticketIsUnread()
            ->ticketIsUnanswered()
            ->select([
                'ticket.ticket_id',
                'ticket.subject',
                'ticket.date_created',
                'assign_acc.AccountName as assigned_to'
            ])
            ->get();

        if ($unansweredTickets->isEmpty() && $unreadTickets->isEmpty()) {
            $this->info('No unanswered or unread tickets found to send.');
            return;
        }

        $this->info('Sending ' . $unansweredTickets->count() . ' Unanswered and ' . $unreadTickets->count() . ' Unread tickets to Google Chat...');

        // Unanswered
        foreach ($unansweredTickets as $ticket) {
            $message = "⏳ *Unanswered Ticket Reminder*\n";
            $message .= "*ID:* {$ticket->ticket_id}\n";
            $message .= "*Subject:* {$ticket->subject}\n";
            $message .= "*Assigned To:* " . ($ticket->assigned_to ?? 'Unassigned') . "\n";
            $message .= "*Date Created:* {$ticket->date_created}\n";
            $message .= "*Link:* " . url('/view-request/' . base64_encode($ticket->ticket_id));
            $this->sendToGoogleChat($webhookUrl, $message);
            $this->info("Sent Unanswered Ticket #{$ticket->ticket_id}");
            sleep(1);
        }

        // Unread
        foreach ($unreadTickets as $ticket) {
            $message = "⚠️ *Unread Ticket Reminder*\n";
            $message .= "*ID:* {$ticket->ticket_id}\n";
            $message .= "*Subject:* {$ticket->subject}\n";
            $message .= "*Assigned To:* " . ($ticket->assigned_to ?? 'Unassigned') . "\n";
            $message .= "*Date Created:* {$ticket->date_created}\n";
            $message .= "*Link:* " . url('/view-request/' . base64_encode($ticket->ticket_id));
            $this->sendToGoogleChat($webhookUrl, $message);
            $this->info("Sent Unread Ticket #{$ticket->ticket_id}");
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
