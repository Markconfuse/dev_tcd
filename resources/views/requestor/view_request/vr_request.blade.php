@if($_ticketReply->count() == 0) 
  @php $_show = ['collapsed', 'show']; @endphp
  @else 
  @php $_show = ['', '']; @endphp
@endif

<div id="accordion_{{$_ticketDetail[0]->ticket_id }}">
 <div class="card card-no-shadow">
    <div class="card-header card-thread {{ $_show[0] }}" data-toggle="collapse" data-parent="#accordion_{{$_ticketDetail[0]->ticket_id }}" href="#collapseCon_{{$_ticketDetail[0]->ticket_id }}" style="cursor:pointer">
      <h4 class="card-title">
        <div class="user-block">
          <img class="img-circle" src="{{ $_ticketDetail[0]->GAvatarReq }}" {{-- alt="{{ $_ticketDetail[0]->requestor_name }}" --}}>
          <span class="username">
            {{ $_ticketDetail[0]->requestor_name }}
          </span>
        </div>
      </h4>
      <small class="description" style="float:right">
        {{ Carbon\Carbon::parse($_ticketDetail[0]->date_created)->format('l, M d, h:i A') }}
        ({{ Carbon\Carbon::parse($_ticketDetail[0]->date_created)->diffForHumans() }})
      </small>
    </div>
    <div id="collapseCon_{{$_ticketDetail[0]->ticket_id }}" class="panel-collapse collapse in {{ $_show[1] }}">
      <div class="card-body" style="overflow-x:scroll;max-height:33em;">
        {!! $_ticketDetail[0]->ticket_content !!}
        @foreach($_attachment as $_file)
            @if($_file->reply_id == NULL)
              <a href="{{ route('viewFile', ['file_name' => base64_encode($_file->name)]) }}" class="btn-link text-secondary"><i class="{{ \Common::instance()->fileIcon($_file->file_type) }}"></i> {{ $_file->name }}</a><br>
            @endif
          @endforeach
      </div>
    </div>
  </div>
</div>