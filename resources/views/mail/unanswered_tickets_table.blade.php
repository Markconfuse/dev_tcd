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

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; margin-top: 20px;">
            <thead>
                <tr>
                    <th style="border-bottom: 2px solid #ebedef; text-align: left; padding: 10px; font-size: 14px; white-space: nowrap;">Ticket ID</th>
                    <th style="border-bottom: 2px solid #ebedef; text-align: left; padding: 10px; font-size: 14px;">Subject</th>
                    <th style="border-bottom: 2px solid #ebedef; text-align: left; padding: 10px; font-size: 14px;">Created</th>
                    <th style="border-bottom: 2px solid #ebedef; text-align: left; padding: 10px; font-size: 14px;"></th>
                </tr>
            </thead>
            <tbody>
                @foreach($tickets as $ticket)
                    <tr>
                        <td style="border-bottom: 1px solid #ebedef; padding: 10px; font-size: 13px;">{{ $ticket->ticket_id }}</td>
                        <td style="border-bottom: 1px solid #ebedef; padding: 10px; font-size: 13px;">{{ $ticket->subject }}</td>
                        <td style="border-bottom: 1px solid #ebedef; padding: 10px; font-size: 13px;">{{ $ticket->created_at }}</td>
                        <td style="border-bottom: 1px solid #ebedef; padding: 10px; font-size: 13px; text-align: center;">
                            <a href="{{ url('/view-request/' . base64_encode($ticket->ticket_id)) }}" style="text-decoration: none;" aria-label="Open ticket">
                                <img src="{{ url('https://img.icons8.com/windows/32/external-link.png') }}" alt="Open" style="width: 18px; height: 18px; vertical-align: middle;">
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="text-align: center; margin-top: 30px;">
        <a href="{{ url('/') }}" 
           style="background: linear-gradient(to left, rgba(10, 50, 30, 0.95), #0f1118); color: #a7f3d0; padding: 10px 22px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            Go to Portal
        </a>
    </div>
@endsection