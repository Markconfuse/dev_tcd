<table id="modalTicketsTable" class="table table-bordered table-sm">
    <thead>
        <tr>
            <th>ID</th>
            <th>Subject</th>
            <th>Status</th>
            <th>Date Created</th>
            <th>Last Updated</th>
        </tr>
    </thead>
    <tbody>
        @forelse($tickets as $t)
            <tr>
                <td>{{ $t->ticket_id }}</td>
                <td>{{ $t->subject }}</td>
                <td>
                @switch($t->status_id)
                    @case(1) Unassigned @break
                    @case(2) Assigned @break
                    @case(3) Answered @break
                    @case(4) Closed @break
                    @case(5) Escalation Requested @break
                    @case(6) Escalated @break
                    @default Unknown
                @endswitch
            </td>
                <td>{{ \Carbon\Carbon::parse($t->date_created)->format('F d, Y g:i:s A') }}</td>
                <td>{{ \Carbon\Carbon::parse($t->last_updated)->format('F d, Y g:i:s A') }}</td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-center">No tickets found.</td>
            </tr>
        @endforelse
    </tbody>
</table>
