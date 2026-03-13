@php
  if ($_ticketDetail[0]->ticket_id == 10793) {
    // dd((Session('userData')->account_id == $_ticketDetail[0]->account_owner_id || 
      // Session('userData')->account_id == $_ticketDetail[0]->requestor_id),$_ticketDetail[0],Session('userData'));
  }

@endphp


@if($_ticketDetail[0]->ao_group == 'BU6' && Session('userData')->AccountGroup == 'BU6')
  <button type="button" class="btn btn-primary btn-sm mb-3" id="btnReply"><i class="fas fa-reply"></i> Reply</button>
@else
  @if($_isvalid && $_ticketDetail[0]->status_id !== '4' || Session('userData')->role_name == 'super_user' || in_array(Session('userData')->account_id, ['57625', '57610', '57615']))
    <button type="button" class="btn btn-primary btn-sm mb-3" id="btnReply"><i class="fas fa-reply"></i> Reply</button>
  @endif
@endif

<button type="button" class="btn btn-secondary btn-sm mb-3 btnExpand" title="Expand thread"><i class="fas fa-expand-arrows"></i> </button>


 
@if(Session('userData')->role_name == 'admin' || Session('userData')->role_name == 'super_user' || in_array(Session('userData')->account_id, ['57610', '57615']))
  <button title="Update Assignment" type="button" id="btnAssign" class="btn btn-olive btn-sm mb-3">
    <i class="fas fa-user-edit"></i> 
  </button>
@endif

@if($_ticketDetail[0]->status_id == '1')
  @if(Session('userData')->account_id == 57627 || Session('userData')->account_id == 57610 || Session('userData')->account_id == 57615)
    <button title="Close request" type="button" id="btnClose" title="Close request." class="btn btn-sm btn-warning mb-3">
      <i class="fas fa-times-circle"></i> 
    </button>
  @endif
@endif


@if($_ticketDetail[0]->ao_group == 'BU6' && Session('userData')->AccountGroup == 'BU6' || $_ticketDetail[0]->ao_group == 'IT' && Session('userData')->AccountGroup == 'IT' || Session('userData')->AccountType == 'AO')
  
    <button title="Close request" type="button" id="btnClose" title="Close request." class="btn btn-sm btn-warning mb-3">
      <i class="fas fa-times-circle"></i> 
    </button>
  @if($_ticketDetail[0]->status_id == '4')
    <button title="Reopen request" type="button" id="btnReopen" title="Reopen request." class="btn btn-sm btn-warning mb-3">
      <i class="fas fa-envelope-open"></i> 
    </button>
  @endif
@else 
  @if(Session('userData')->account_id == $_ticketDetail[0]->account_owner_id || 
      Session('userData')->account_id == $_ticketDetail[0]->requestor_id)
      @if($_ticketDetail[0]->status_id == '3')
        <button title="Close request" type="button" id="btnClose" title="Close request." class="btn btn-sm btn-warning mb-3">
          <i class="fas fa-times-circle"></i> 
        </button>
      @elseif($_ticketDetail[0]->status_id == '4')
        <button title="Reopen request" type="button" id="btnReopen" title="Reopen request." class="btn btn-sm btn-warning mb-3">
          <i class="fas fa-envelope-open"></i> 
        </button>
      @endif
  @endif
@endif

