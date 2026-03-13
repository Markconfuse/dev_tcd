<div class="col-12 col-sm-12 mb-2 mt-3">
  <h5 class="m-0 text-dark">{{ $_ticketDetail[0]->subject }}</h5>
</div>

<div class="col-12 col-sm-6">
  <div class="info-box ">
    <div class="info-box-content">
      <span class="info-box-number text-center text-muted">Reference No:{{ sprintf('%04d', $_ticketDetail[0]->ticket_id) }}</span>
      @if($_ticketDetail[0]->requestor_id == $_ticketDetail[0]->account_owner_id)
        <span class="info-box-text text-center text-muted" style="white-space:normal">Created {{ Carbon\Carbon::parse($_ticketDetail[0]->date_created)->format('M d, Y h:i:A') }}
          - {{ $_ticketDetail[0]->requestor_name }}
        </span>
      @else
        <span class="info-box-text text-center text-muted" style="white-space:normal">{{ $_ticketDetail[0]->requestor_name.' created ('.Carbon\Carbon::parse($_ticketDetail[0]->date_created)->format('M d, Y h:i:A').') for '.$_ticketDetail[0]->ao_name }}
        </span>
      @endif
    </div>
  </div>
</div>

<div class="col-12 col-sm-3">
  <div class="info-box ">
    <div class="info-box-content">
      <span class="info-box-text text-center text-muted">Assignee</span>
      <span class="info-box-number text-center text-muted mb-0">
        @forelse($_ticketAssignment as $engr)
          <img style="cursor:pointer;" 
               class="circular-portrait {{ \Common::instance()->checkIcon($engr->is_answered, $engr->is_read) }}" 
               alt="{{ $engr->AccountName }}" title="{{ $engr->AccountName }}" src="{{ $engr->GAvatar }}"/>
          @empty
            <span class="info-box-number text-center text-muted mb-0">No Assignee yet</span>
        @endforelse
      </span>
    </div>
  </div>
</div>

<div class="col-12 col-sm-3">
  <div class="info-box ">
    <div class="info-box-content">
      <span class="info-box-text text-center text-muted">Status</span>
      <span class="info-box-number text-center text-muted mb-0">
        <span class="{{ \Common::instance()->checkStatus($_ticketDetail[0]->status_description) }}">{{ $_ticketDetail[0]->status_description }}</span>
      </span>
    </div>
  </div>
</div>