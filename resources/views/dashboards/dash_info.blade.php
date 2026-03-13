<div class="row">

  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-success"><i class="far fa-plus"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">New Tickets Today</span>
        <span class="info-box-number">{{ $ticket_today->count }}</span>
      </div>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-danger"><i class="fas fa-file-import"></i></span>
      <div class="info-box-content">
        @if(Session('userData')->role_name == 'engineer')
          <span class="info-box-text">{{ $dash_label }} Pending Tickets</span>
        @else
          <span class="info-box-text">{{ $dash_label }} Unassigned Tickets</span>
        @endif
        <span class="info-box-number">{{ $ticket_pending->count }}</span>
      </div>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-warning"><i class="far fa-lock-open"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ $dash_label }} Open Tickets</span>
        <span class="info-box-number">{{ $ticket_open->count }}</span>
      </div>
    </div>
  </div>

  <div class="col-md-3 col-sm-6 col-12">
    <div class="info-box">
      <span class="info-box-icon bg-info"><i class="far fa-lock"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">{{ $dash_label }} Closed Tickets</span>
        <span class="info-box-number">{{ $ticket_closed->count }}</span>
      </div>
    </div>
  </div>

</div>
