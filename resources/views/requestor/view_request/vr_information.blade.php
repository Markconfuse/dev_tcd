
<div id="dvTransHistory">
  <button type="button" id="btnViewTransaction" class="btn btn-primary btn-block mb-3">View Transaction History</button>
</div>

<div id="dvDetails">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Ticket Information</h3>

      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
        </button>
      </div>
    </div>

    <div class="card-body p-0 text-muted" style="display: block;">
      <ul class="nav nav-pills flex-column">
        <li class="nav-item">
          <p class="text-sm" style="padding:.5rem 0.5rem 0rem 0.5rem;">Company Name
            <b class="d-block">{{ $_ticketDetail[0]->customer_name }}</b>
          </p>
        </li>
        <li class="nav-item">
          <p class="text-sm" style="padding:.5rem 0.5rem 0rem 0.5rem;">Project Name
            <b class="d-block">{{ $_ticketDetail[0]->project_name }}</b>
          </p>
        </li>
        <li class="nav-item">
          <p class="text-sm" style="padding:.5rem 0.5rem 0rem 0.5rem;">Request Type
            <b class="d-block">{{ $_ticketDetail[0]->request_type }}</b>
          </p>
        </li>
        <li class="nav-item">
          <p class="text-sm" style="padding:.5rem 0.5rem 0rem 0.5rem;">Brand
            <b class="d-block"></b>
            @if(!empty($_ticketBrand))
              <b class="d-block">{{ $_ticketBrand->pluck('brand')->implode(', ') }}</b>
            @else 
              <b class="d-block">No brand selected.</b>
            @endif
          </p>
        </li>
        <li class="nav-item">
          <p class="text-sm" style="padding:.5rem 0.5rem 0rem 0.5rem;">Carbon Copy
            <b class="d-block"></b>
            @forelse($_ticketCC as $cc)
              <img style="cursor:pointer;" class="circular-portrait" alt="{{ $cc->AccountName }}" title="{{ $cc->AccountName }}" 
               src="@if(!empty($cc->GAvatar))
                {{ $cc->GAvatar }}
              @else
                {{ "https://adminlte.io/themes/dev/AdminLTE/dist/img/avatar2.png" }}
              @endif"/>
            @empty
              <b class="d-block">No data available.</b>
            @endforelse
          </p>
        </li>
      </ul>
    </div>
  </div>
</div>

<div id="dvAttachment">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Attachments</h3>

      <div class="card-tools">
        <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i>
        </button>
      </div>
    </div>
    <!-- /.card-header -->
    <div class="card-body p-0">
      <ul class="list-unstyled" style="max-height: 370px;overflow-y: scroll;">
        @forelse($_attachment as $_file)
        <li>
          <a href="{{ route('viewFile', ['file_name' => base64_encode($_file->name)]) }}" title="{{ $_file->name }}" class="btn-link text-secondary nav-link"><i class="{{ \Common::instance()->fileIcon($_file->file_type) }}"></i> {{ \Str::limit(\Str::after($_file->name, '_'), 32) }}</a>
        </li>
        @empty
          <p class="text-sm"><b class="d-block" style="padding:.5rem 0.5rem 0rem 0.5rem;">No attachment available.</b></p>
        @endforelse
      </ul>
    </div>
  </div>
</div>