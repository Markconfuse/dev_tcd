@extends('layouts.page')

@section('title', 'Compose Request')

@section('content_header', 'Compose Request')

@section('css')
@stop

@section('content')

@include('modals.mdl_custval')

<form id="formRequest" action="{{ url('post-request') }}" method="post" accept-charset="utf-8" enctype="multipart/form-data" role="form">
  <div class="card card-default">

    @include('layouts.components.preloader-cat', ['content' => 'Sending request'])
    @include('layouts.components.preloader-wspin')

    <div class="card-body">
      <div class="row">    

        <div class="col-md-6">
          <div class="form-group">
            <label>AO:</label>
            <select class="form-control select2 select2-hidden-accessible" name="aoID" id="aoID" style="width: 100%;">
              <option disabled selected>Please choose AO</option>
              @foreach($_ao as $accountOwner)
                <option value="{{ $accountOwner->account_id }}">{{ $accountOwner->AccountName }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            @if(Session('userData')->role_name == 'requestor')
              <label>CC: <small style="color:red">(Your BU HEAD is automatically copied upon request.)</small></label>
            @else 
              <label>CC: <small style="color:red">(Your chosen AO's BU HEAD is automatically copied upon request.)</small></label>
            @endif
            <select class="form-control select2 select2-hidden-accessible" name="ccID[]" id="ccID" style="width: 100%;" multiple>
              @foreach($_cc as $carbonCopy)
                <option value="{{ $carbonCopy->account_id }}">{{ $carbonCopy->AccountName }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-6">
          <label>Customer Name:</label>
          <div class="input-group mb-3">
            <input type="text" class="form-control" name="customerName" id="customerName" placeholder="Customer Name" autocomplete="off" readonly>
            <input type="hidden" class="form-control" name="customerID" id="customerID">
            <div class="input-group-append" id="btnSearchCustomer" title="Search Customer">
              <span class="input-group-text adon"><i class="fa fa-search"></i></span>
            </div>
          </div>
        </div>

        <div class="col-md-6">
          <div class="form-group">
            <label>Project Name:</label>
            <input type="text" name="projectName" id="projectName" placeholder="Project Name" class="form-control" autocomplete="off">
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label>Request Type:</label>
            <select class="form-control select2 select2-hidden-accessible" name="requestTypeID" id="requestTypeID" style="width: 100%;">
              <option disabled selected>Please choose request type</option>
              @foreach($_requestType as $requestType)
                <option value="{{ $requestType->request_type_id }}">{{ $requestType->request_type }}</option>
              @endforeach
            </select>
          </div>
        </div>

        <div class="col-md-3">
          <div class="form-group">
            <label>Brand:</label>
              <select class="form-control select2 select2-hidden-accessible" name="brandID[]" id="brandID" style="width: 100%;" multiple>
                @foreach($_brand as $brand)
                  <option value="{{ $brand->brand_id }}">{{ $brand->brand }}</option>
                @endforeach
              </select>
          </div>
        </div>

        @if(Session('userData')->role_id == '2' || Session('userData')->role_id == '3' || Session('userData')->AccountGroup == 'CE01')
        <div class="col-md-6">
          <div class="form-group">
            <label>Assign To: </label>
            <select class="form-control select2 select2-hidden-accessible" name="engrID[]" id="engrID" style="width: 100%;" multiple>
              @foreach($_engineer as $engr)
                @if(Session('userData')->account_id == $engr->account_id)
                  <option value="{{ $engr->account_id }}" selected>{{ $engr->AccountName }}</option>
                @else
                  <option value="{{ $engr->account_id }}">{{ $engr->AccountName }}</option>
                @endif
              @endforeach
            </select>
          </div>
        </div>
        @endif

        <div class="col-md-12">
          <div class="form-group">
            <label>Subject:</label>
            <input type="text" name="subject" id="subject" class="form-control" placeholder="Subject" autocomplete="off">
          </div>
        </div>

        <!-- /.col -->
        <div class="col-md-12">
          <div class="form-group">
              <textarea name="requestContent" id="requestContent" rows="10" cols="80"></textarea>
          </div>
        </div>
        <input type="hidden" id="unique1" name="unique" value="{{ Str::random(10).\Carbon\Carbon::now()->format('mdyHis') }}">
        @csrf
        </form>

        <div class="col-md-12">
          <label>Attachment</label>
          <form method="post" action="{{ route('dropzone')}}" enctype="multipart/form-data" class="dropzone" id="dropzone">
            <div class="dz-message" data-dz-message><span>Drop or Select files here to upload</span></div>
            <input type="hidden" id="unique" name="unique" value="{{ Str::random(10).\Carbon\Carbon::now()->format('mdyHis') }}">
            @csrf
          </form>   
        </div>

        <div class="col-md-6">
          <button type="button" id="btnSend" style="width:50%" class="btn btn-sm btn-primary"><i class="fas fa-paper-plane"></i> SEND</button>
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->
    </div>
  </div>



@stop

@section('js')
    <script type="text/javascript">

      $(function() {

          window.role = '<?php echo Session('userData')->role_name; ?>';         
          window.account_group = '<?php echo Session('userData')->AccountGroup; ?>';         

          $('#mdl_custval').modal('show');

          $('#unique1').val($('#unique').val());

          // $('#requestContent').summernote({
          //   height: '250px',
          //   onPaste: function (e) {
          //     if (navigator.userAgent.indexOf("Firefox") > 0) {
          //       var clipboardData = e.originalEvent.clipboardData;
          //       if (clipboardData && clipboardData.items && clipboardData.items.length) {
          //         var item = clipboardData.items[0];
          //         if (item.kind === 'file' && item.type.indexOf('image/') !== -1) {
          //           e.preventDefault();
          //         }
          //       }
          //     }
          //   }
          // }).on("summernote.enter", function(we, e) {
          //   $(this).summernote("pasteHTML", "<br><br>");
          //   e.preventDefault();
          // });

          $('#requestContent').summernote({
            height: '250px',
          }).on("summernote.enter", function(we, e) {
            $(this).summernote("pasteHTML", "<br><br>");
            e.preventDefault();
          });

          $('#preloader-wspin').attr('hidden', 'hidden');

          if(role == 'requestor') {
            if(!$('#aoID option[value="' + '<?php echo Session('userData')->account_id; ?>' + '"]').prop("selected", true).length){
               $('#aoID').prop('selectedIndex', 0);
            } else {
               $("#aoID option:contains(" + '<?php echo Session('userData')->account_id; ?>' + ")").attr('selected', 'selected');
            }            
          }


          $('.select2').select2({
            theme: 'bootstrap4'
          });

          $('#cvalCustName').keypress(function(e) {
            var key = e.which;
            var keyword = $('#cvalCustName').val();

            if (key == 13 ) 
            {
              createTable('https://ice-cream.ics.com.ph/api/liveSearch?key='+btoa(unescape(encodeURIComponent(keyword))))
            }
          })

          $('#btnCustomerName').on('click', function() {
            var keyword = $('#cvalCustName').val();

            createTable('https://ice-cream.ics.com.ph/api/liveSearch?key='+btoa(unescape(encodeURIComponent(keyword))))
          })

          $('#tblCustomerList').on('click', '.btnSelect', function() {
            temp = atob($(this).attr('id')).split('|');
            $('#customerID').val(temp[0]);
            $('#customerName').val(temp[1]);
            $('#mdl_custval').modal('hide');
            intelliSub();
          })

      })
    </script>
    <script src="{{ asset('/js/compose_request/cr_validation.js') }}"></script>
    <script src="{{ asset('/js/compose_request/cr_custval.js') }}"></script>

    <script type="text/javascript">
      Dropzone.options.dropzone =
         {
            maxFilesize: 20,
            acceptedFiles: ".ods,application/vnd.oasis.opendocument.spreadsheet,.jpeg,.jpg,.png,.gif,.txt,.xlsx,.xls,.csv,.word,.doc,.docs,.docx,.pdf,.xml,.eml,.sdd,.zip,.7z,.rar,.exe",
            addRemoveLinks: true,
            timeout: 50000,
            renameFile: function(file) {
                var dt = new Date();
                var time = dt.getTime();
                return time+'_'+file.name;
            },
            removedfile: function(file) 
            {
                var name = file.upload.filename;
                var unique = $('#unique').val();
                $.ajax({
                    type: 'POST',
                    url: '{{ url('attachment-delete') }}',
                    data: {filename: name, unique: unique, "_token": "{{ csrf_token() }}"},
                    success: function (data){
                        console.log("File has been successfully removed!!");
                    },
                    error: function(e) {
                        console.log(e);
                    }
              });
                    var fileRef;
                    return (fileRef = file.previewElement) != null ? 
                    fileRef.parentNode.removeChild(file.previewElement) : void 0;
            },
            success: function(file, response) 
            {
              // obj = JSON.parse(response);
              // console.log();
              // file.previewElement.id = obj.filename;
            },
            error: function(file, message) {
              console.log(message);
                $(file.previewElement).addClass("dz-error").find('.dz-error-message').text(message);
            }
      };
    </script>

@stop

