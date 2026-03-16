@extends('layouts.mail')

@section('content')
    <h3 style="text-align: center; margin: 16px 0 32px 0; font-size: 16px;">TCD Portal: Unread Tickets Notification</h3>
    <p style="font-size: 14px;">Hi {{ $ticket->assigned_to }},</p>

    <p style="font-size: 14px;">
        You have an unread ticket that requires your attention. Please review it in the TCD Portal.
    </p>

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ url('/view-request/' . base64_encode($ticket->ticket_id)) }}" 
           style="background: linear-gradient(to left, rgba(10, 50, 30, 0.95), #0f1118); color: #a7f3d0; padding: 10px 22px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            View Ticket #{{ $ticket->ticket_id }}
        </a>
    </div>

    <div class="divider"></div>

    <p style="font-size: 14px;">
        <strong>Ticket Details:</strong><br>
        Subject: {{ $ticket->subject }}<br>
        Created: {{ $ticket->created_at }}
    </p>
@endsection
