@php $_attachment = $_attachment->groupBy('reply_id'); @endphp

@foreach($_ticketReply as $key => $ticketReply)
	@if($key == array_key_first($_ticketReply->toArray()))
    @php $_show = ['collapsed', 'show']; @endphp
    @else 
    @php $_show = ['', '']; @endphp
 @endif

<div id="accordionRep_{{ $ticketReply->reply_id }}" data-r-id="{{$ticketReply->reply_id}}" onclick="sus(this)">
	<div class="card card-no-shadow">
		<div class="card-header card-thread collapsed {{ $_show[0] }}" data-toggle="collapse" data-parent="#accordionRep_{{ $ticketReply->reply_id }}" href="#collapseRep_{{ $ticketReply->reply_id }}" style="cursor:pointer">
			<h4 class="card-title">
				<div class="user-block">
					<img class="img-circle" src="{{ $ticketReply->GAvatar }}">
					<span class="username">
					{{ $ticketReply->AccountName }} 
				</span>
				</div>
			</h4>
        <small class="description" style="float:right">
			{{ Carbon\Carbon::parse($ticketReply->date_replied)->format('l, M d, h:i A') }}
			({{ Carbon\Carbon::parse($ticketReply->date_replied)->diffForHumans() }})
			@if ($ticketReply->date_updated != '') <br>
		<small class="description" style="margin-top: 15px;">
			<span><b>Edited</b> on 
			{{ Carbon\Carbon::parse($ticketReply->date_updated)->format('l, M d, h:i A') }}
			({{ Carbon\Carbon::parse($ticketReply->date_updated)->diffForHumans() }})
			</span>
        </small>
		@endif
        </small>
		
    </div>
    <div id="collapseRep_{{ $ticketReply->reply_id }}" class="panel-collapse collapse in {{ $_show[1] }}">
        <div class="card-body" style="overflow-x:auto;max-height:33em;">
			{!! $ticketReply->reply !!}
			
			@if(Session('userData')->account_id == 57610 && $ticketReply->AccountID == 57610 || Session('userData')->account_id == '57615' && $ticketReply->AccountID == 57615) 
				<button data-ticket-id="{{ $_ticketDetail[0]->ticket_id }}" data-content="{{ $ticketReply->reply }}" 
				data-reply-id="{{ $ticketReply->reply_id }}" type="button" onclick="openEditReply(this)" 
				class="btn btn-secondary btn-xs mb-3 float-right btnx x{{ $ticketReply->reply_id }}" 
				data-toggle="modal" data-target="#eReply" id="btnReplyModal"><i class="fas fa-edit"></i> Edit Reply
				</button>
			@endif	
        </div>
        @if($_attachment->has($ticketReply->reply_id))
			<div class="card-footer" style="border: dashed 1px">
				@foreach($_attachment[$ticketReply->reply_id] as $_file)
				@if($ticketReply->reply_id == $_file->reply_id)
                <a href="{{ route('viewFile', ['file_name' => base64_encode($_file->name)]) }}" class="btn-link text-secondary"><i class="{{ \Common::instance()->fileIcon($_file->file_type) }}"></i> {{ \Str::after($_file->name, '_') }}</a><br>
				@endif
				@endforeach
			</div>
			@endif
		</div>
    </div>
</div>
@endforeach

<div class="modal fade editReply1" id="eReply" role="dialog">
	<div class="modal-dialog modal-xl"> 
		<!-- Modal content-->
			<div class="modal-content">
				<div class="alert-m"></div>
				<div class="modal-header">
				<b>Edit Reply</b>
			<button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>

        <div class="modal-body">
			<small class="description"><span style="float:left;">Reference No:{{ sprintf('%04d', $_ticketDetail[0]->ticket_id) }}</span></small>
        <br>
        <div class="form-group" style="margin-top: 10px;">
			<span class="fMessage"></span>
				<div class="col-md-12 thisTA">
				<textarea class="replyTextArea" id="editRContent" rows="5"></textarea>
        </div>
		<!-- <br> -->
		<!-- <div class="form-group" style="margin-top:-10px">
				<form method="post" action="{{ route('dropzone')}}" enctype="multipart/form-data" class="dropzone" id="dropzone">
					<div class="dz-message" data-dz-message><span>Drop or Select files here to upload</span></div>
					<input type="hidden" id="unique" name="unique" value="{{ Str::random(10).\Carbon\Carbon::now()->format('mdyHis') }}">
				@csrf
      		</form> 
    	 </div> -->
		</div>
        <div class="row">
			<div class="col-md-3">
				<button type="button" onclick="submitEditedReply()" class="btn btn-success btnC">Save</button>
				</div>
			</div>
        </div>
			<div class="modal-footer">
				<button type="button" class="btn btn-default " data-dismiss="modal">Close</button>
			</div>
		</div>
	</div>
</div>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/basic.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>

	var rtxVal = '';
	var rx = '';

	function sus(button){
		rx = button.getAttribute('data-r-id');
	rtxVal = $('.val'+rx+'').val();
	$.ajax({
    url: '{{ url("/get_reply_info") }}/' + rx,
    method: 'GET',
		cache: false,
		success: function(response){
        if(response.replies == 1) { 
			$('.x'+rx+'').hide()
        } else if (response.replies == rx) {
			$('.x'+rx+'').show()
				}
			}
		}); 
	}

	var rIdx = '';
	var ticketId = '';
	var replyContent = '';
	
	function openEditReply(button){
    var replyId = button.getAttribute('data-reply-id');
    ticketId = button.getAttribute('data-ticket-id');
    rIdx = replyId;
    $.ajax({
    url: '{{ url("/get_single_reply") }}/' + replyId,
    method: 'GET',
    cache: false,
		success: function(response){
        console.log('r: ', response);
        if (response.replies !== '1'){
			$('.thisTA').show();
			$('.btnC').show();
			$('.fMessage').hide();

        rIdx = response.replies.reply_id;
        replyContent = response.replies.reply;
  
        var replyText = $(replyContent);
        var them = replyText.text();
        
		var xz = $('#editRContent').summernote('code', replyContent)
        
        } else {
			$('.thisTA').hide();
			$('.btnC').hide();
			$('.fMessage').show();
			$('.fMessage').text('You can no longer edit this reply');
			}
		}
    });  

    summernote('#editRContent');
    // $('#dvReply').removeAttr('hidden');
    // $('#btnReply').hide();
    $('.editReply1').modal('show');
  
    console.log('idx: ', ticketId);
    $('.rId').text(replyId);
  
    var value = $('#value').val()
  }

	var tX = '';
    function submitEditedReply(){
		// rValue = $('.replyTextarea').val();
		t = $('.replyTextArea').summernote('code');
    	rValue = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html><body>'+ t +'</body></html>';
		$.ajax({
			url: "{{ url('/submit_edited_reply') }}",
			method: 'POST',
			data: { 
			_token: function() {
			return "{{ csrf_token() }}"
		},
		rIdx,
		ticketId,
		rValue
    },
    cache: false,
		success:function(response){
			rMessage = response;
			//console.log('response message : ', rMessage);
			//$('.alert-m').html('<div class="container xlt"><div class="col-md-12 alert-margin" style="margin-top: 15px;"><div class="alert alert-info"><div class="fa fa-spinner fa-spin"></div> Reply was successfully updated.</div></div></div>'); 
			showSuccessAlert()
			}
		});
    function removeAlert(){
        window.location.reload();
        setTimeout(function() { 
		$('.xlt').hide();
		}, 2000);
	}
	
	function showSuccessAlert() {
		Swal.fire({
		icon: 'success',
		type: 'success',
		title: 'Edit Reply',
		text: 'Your reply was edited successfully.',
		confirmButtonText: false,
		timer: 1000
		}).then((result) => {
		window.location.reload();   
		});
	}
	
}

</script>

