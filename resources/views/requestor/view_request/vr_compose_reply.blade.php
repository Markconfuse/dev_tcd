<div class="card card-outline" id="dvReply" hidden>
  @include('layouts.components.preloader-cat', ['content' => 'Posting reply'])
  <div class="card-header">
    <h3 class="card-title">Compose New Message</h3>
  </div>
  <!-- /.card-header -->
  <div class="card-body" style="margin-bottom:-40px">
    <form id="formReply" action="{{ url('post-reply') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data" role="form">
      <div class="form-group">
        <select class="form-control select2" name="ccID[]" id="ccID" style="width: 100%;" multiple>
          @foreach($_cc as $carbonCopy)
            <option value="{{ $carbonCopy->account_id }}">{{ $carbonCopy->AccountName }}</option>
          @endforeach
        </select>
      </div>
      @if(\App\Assignment::validEngr($_ticketDetail[0]->ticket_id)->count() > 0)
        @if(App\Assignment::ownDetail($_ticketDetail[0]->ticket_id)->pluck('is_answered')[0] == 0)
        <div class="form-group" style="margin-left:4px"> 
          <input type="checkbox" style="margin-right:5px" name="is_answered" checked> Mark self as answered.
        </div>
        @endif
      @endif
      <div class="form-group">
        <textarea name="replyContent" id="replyContent" rows="10" cols="80"></textarea>
      </div>
      <input type="hidden" id="unique1" name="unique" value="{{ Str::random(10).\Carbon\Carbon::now()->format('mdyHis') }}">
      <input type="hidden" id="replyType" name="replyType" value="1">
      <input type="hidden" id="engrResetList" name="engrResetList">
      @csrf
    </form>
    <div class="form-group" style="margin-top:-10px">
      <form method="post" action="{{ route('dropzone')}}" enctype="multipart/form-data" class="dropzone" id="dropzone">
        <div class="dz-message" data-dz-message><span>Drop or Select files here to upload</span></div>
        <input type="hidden" id="unique" name="unique" value="{{ Str::random(10).\Carbon\Carbon::now()->format('mdyHis') }}">
        @csrf
      </form>  
    </div>
  </div>
  <div class="card-footer">
    <div class="float-right">
      <button type="button" class="btn btn-default" id="btnCancel"><i class="fas fa-times"></i> Discard</button>
    </div>

    <div class="btn-group">
@if(Session('userData')->getOriginal('role_name') == 'engineer')
    	<button type="button"
    class="btn btn-primary"
    id="btnPostReply"
    data-role="{{ Session('userData')->role_name }}">
    Send Engineer
</button>


     		<button type="button" class="btn btn-primary btnPostReply dropdown-toggle dropdown-icon" style="border-left: 1px solid #185abc;" title="More send options" data-toggle="dropdown" aria-expanded="false">
        		<span class="sr-only">Toggle Dropdown</span>
        			<div class="dropdown-menu" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-1px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
						<a class="dropdown-item" id="btnSendClose" title="Send reply and close this ticket.">Send and Close</a>
						<a class="dropdown-item" id="btnSendReset" title="Send reply and reset engineer's answered status to pending.">Send and Reset</a>
						<a class="dropdown-item" id="btnSendReassign" title="Send reply and ask for reassignment">Send and Reassign</a>
						@if(Session('userData')->role_name == 'requestor' || Session('userData')->account_id == '57732')

						<a class="dropdown-item" id="btnSendEscalate" title="Send reply and ask for escalate">Send and Escalate</a>
						@endif
						@if(in_array(Session('userData')->account_id, ['57732', '57625', '57610', '57627', '758']))
						
						<a class="dropdown-item" id="btnApprovedEscalate" title="Send reply and approved escalation">Approved Escalation</a>
						
						<a class="dropdown-item" id="btnDeclinedEscalate" title="Send reply and declined escalation">Declined Escalation</a>
					@endif
        		</div>
      		</button>
    	@else	
    
      <button type="button" class="btn btn-primary btnPostReply" id="btnPostReply" title="Just a normal reply."> Send</button>
      <button type="button" class="btn btn-primary btnPostReply dropdown-toggle dropdown-icon" style="border-left: 1px solid #185abc;" title="More send options" data-toggle="dropdown" aria-expanded="false">
        <span class="sr-only">Toggle Dropdown</span>
        <div class="dropdown-menu" role="menu" x-placement="bottom-start" style="position: absolute; transform: translate3d(-1px, 37px, 0px); top: 0px; left: 0px; will-change: transform;">
          <a class="dropdown-item" id="btnSendClose" title="Send reply and close this ticket.">Send and Close</a>
          <a class="dropdown-item" id="btnSendReset" title="Send reply and reset engineer's answered status to pending.">Send and Reset</a>
          <a class="dropdown-item" id="btnSendReassign" title="Send reply and ask for reassignment">Send and Reassign</a>

		      @if(Session('userData')->role_name == 'requestor')
			 		  <a class="dropdown-item" id="btnSendEscalate" title="Send reply and ask for escalate">Send and Escalate</a>
					@endif

					@if(in_array(Session('userData')->account_id, ['56395','57625', '57610', '57627', '758', '57615']))

            @if($_ticketDetail[0]->escalation_id != NULL)
              
              @if($_ticketDetail[0]->is_approved == 0 && $_ticketDetail[0]->is_checked == 0)
							 <a class="dropdown-item" id="btnApprovedEscalate" title="Send reply and approved escalation">Acknowledge Escalation</a>
							 <a class="dropdown-item" id="btnDeclinedEscalate" title="Send reply and declined escalation">Decline Escalation</a>
              @elseif($_ticketDetail[0]->is_approved == 1 && $_ticketDetail[0]->is_checked == 1)
                <a class="dropdown-item" id="btnDeclinedEscalate" title="Send reply and declined escalation">Decline Escalation</a>
              @elseif($_ticketDetail[0]->is_approved == 0 && $_ticketDetail[0]->is_checked == 1)
                <a class="dropdown-item" id="btnApprovedEscalate" title="Send reply and approved escalation">Acknowledge Escalation</a>
              @endif

            @endif

					@endif
				
				</div>
			</button>
      @endif
		</div>
	</div>
</div>


<div class="modal fade" id="engineerReviewModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header bg-warning">
                <h5 class="modal-title">Review Before Sending</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body">
                <p><strong>Engineer Reminder:</strong></p>
                <ul>
                    <li>Please double-check your technical response.</li>
                    <li>Verify ticket status before sending.</li>
                </ul>
                <p class="text-danger mb-0">
                    Are you sure you want to send this reply?
                </p>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    Cancel
                </button>
                <button type="button" class="btn btn-primary" id="confirmEngineerSend">
                    Yes, Send Reply
                </button>
            </div>

        </div>
    </div>
</div>