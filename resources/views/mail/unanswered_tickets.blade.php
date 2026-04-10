@extends('layouts.mail')

@section('content')
    <h3 style="text-align: center; margin: 16px 0 32px 0; font-size: 16px;">TCD Portal: Unanswered Tickets Summary</h3>
    <p style="font-size: 14px;">Hi {{ $display_name ?? 'Engineer' }},</p>

    @if(($warning_stage ?? 'day1') === 'day5')
        <p style="font-size: 14px;"><strong>Not Yet Marked as answered – 5th Day</strong></p>
        <p style="font-size: 14px;">
            There are TCD Portal tickets that you still have not marked as answered yet, kindly provide the necessary action URGENTLY to avoid escalation.
        </p>
    @elseif(($warning_stage ?? 'day1') === 'day3')
        <p style="font-size: 14px;"><strong>Not Yet Marked as answered – 3 Days</strong></p>
        <p style="font-size: 14px;">
            There are TCD Portal tickets that you have not marked as answered yet, kindly check or provide a progress update if you need more time to avoid further delays.
        </p>
    @else
        <p style="font-size: 14px;"><strong>Not Yet Marked as answered – 1st Day</strong></p>
        <p style="font-size: 14px;">
            There are TCD Portal tickets that you have not marked as answered yet, kindly check and provide an update as needed.
        </p>
    @endif

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