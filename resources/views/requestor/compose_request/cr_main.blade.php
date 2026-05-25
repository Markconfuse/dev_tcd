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
      console.log("TCD Portal compose script tag loaded");

      $(function() {
          console.log("TCD Portal compose document ready fired");

          window.role = '<?php echo Session('userData')->role_name; ?>';         
          window.account_group = '<?php echo Session('userData')->AccountGroup; ?>';         

          var urlParams = new URLSearchParams(window.location.search);
          var payloadId = urlParams.get('payload_id');
          if (!payloadId) {
              $('#mdl_custval').modal('show');
          }

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

          function markdownToHtml(markdown) {
            if (!markdown) return '';
            let html = markdown
              .replace(/&/g, '&amp;')
              .replace(/</g, '&lt;')
              .replace(/>/g, '&gt;')
              .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
              .replace(/__(.*?)__/g, '<strong>$1</strong>')
              .replace(/^### (.*?)$/gm, '<h3>$1</h3>')
              .replace(/^## (.*?)$/gm, '<h2>$1</h2>')
              .replace(/^# (.*?)$/gm, '<h1>$1</h1>')
              .replace(/^\s*-\s+(.*?)$/gm, '<li>$1</li>')
              .replace(/^\s*\*\s+(.*?)$/gm, '<li>$1</li>')
              .replace(/(<li>[\s\S]*?<\/li>)/g, '<ul>$1</ul>')
              .replace(/<\/ul>\s*<ul>/g, '')
              .split(/\n{2,}/)
              .map(para => {
                para = para.trim();
                if (!para) return '';
                if (para.startsWith('<h') || para.startsWith('<ul') || para.startsWith('<li')) {
                  return para;
                }
                return '<p>' + para.replace(/\n/g, '<br>') + '</p>';
              })
              .join('\n');
            return html;
          }

          console.log("Checking payloadId in URL...", payloadId);
          if (payloadId) {
            console.log("AI draft detected. Hiding customer modal and sending AJAX request for payload:", payloadId);
            $('#mdl_custval').modal('hide');

            $.get('{{ url("/get-agentic-payload") }}', { payload_id: payloadId }, function(response) {
              console.log("Received response from get-agentic-payload:", response);
              if (response && response.success && response.data) {
                var payload = response.data;
                console.log("Payload data successfully fetched:", payload);

                // 1. Populate AO
                if (payload.ao) {
                  var targetAo = payload.ao.toLowerCase().trim();
                  $('#aoID option').each(function() {
                    var optText = $(this).text().toLowerCase().trim();
                    var optVal = $(this).val().toLowerCase().trim();
                    if (optText.indexOf(targetAo) !== -1 || targetAo.indexOf(optText) !== -1 || optVal === targetAo) {
                      $(this).prop('selected', true);
                      return false;
                    }
                  });
                  $('#aoID').trigger('change');
                }

                // 2. Populate Project Name
                if (payload.project_name) {
                  $('#projectName').val(payload.project_name).trigger('change');
                }

                // 3. Populate Request Type
                if (payload.request_type) {
                  var targetType = payload.request_type.toLowerCase().trim();
                  $('#requestTypeID option').each(function() {
                    var optText = $(this).text().toLowerCase().trim();
                    if (optText.indexOf(targetType) !== -1 || targetType.indexOf(optText) !== -1) {
                      $(this).prop('selected', true);
                      return false;
                    }
                  });
                  $('#requestTypeID').trigger('change');
                }

                // 4. Populate Brand
                if (payload.brand) {
                  var targetBrand = payload.brand.toLowerCase().trim();
                  var selectedBrands = [];
                  $('#brandID option').each(function() {
                    var optText = $(this).text().toLowerCase().trim();
                    if (targetBrand.indexOf(optText) !== -1 || optText.indexOf(targetBrand) !== -1) {
                      selectedBrands.push($(this).val());
                    }
                  });
                  if (selectedBrands.length > 0) {
                    $('#brandID').val(selectedBrands).trigger('change');
                  }
                }

                // 5. Populate Assign To (Engineer)
                if (payload.assign_to) {
                  var targetAssign = payload.assign_to.toLowerCase().trim();
                  var selectedEngineers = [];
                  $('#engrID option').each(function() {
                    var optText = $(this).text().toLowerCase().trim();
                    if (targetAssign.indexOf(optText) !== -1 || optText.indexOf(targetAssign) !== -1) {
                      selectedEngineers.push($(this).val());
                    }
                  });
                  if (selectedEngineers.length > 0) {
                    $('#engrID').val(selectedEngineers).trigger('change');
                  }
                }

                // 6. Populate Description (Content) into Summernote
                if (payload.body_content) {
                  var htmlContent = markdownToHtml(payload.body_content);
                  $('#requestContent').summernote('code', htmlContent);
                }

                // 7. Auto-populate Customer Name and request Customer ID from liveSearch API
                if (payload.customer_name) {
                  var custName = payload.customer_name;
                  $('#customerName').val(custName);

                  var searchUrl = 'https://ice-cream.ics.com.ph/api/liveSearch?key=' + btoa(unescape(encodeURIComponent(custName)));
                  $.get(searchUrl, function(data) {
                    try {
                      var qryRes = jQuery.parseJSON(JSON.stringify(data.data));
                      if (qryRes && qryRes.length > 0) {
                        var bestMatch = qryRes[0];
                        for (var i = 0; i < qryRes.length; i++) {
                          if (qryRes[i].CustomerName.toLowerCase().trim() === custName.toLowerCase().trim()) {
                            bestMatch = qryRes[i];
                            break;
                          }
                        }
                        $('#customerID').val(bestMatch.CustomerID);
                        $('#customerName').val(bestMatch.CustomerName);
                        console.log("Auto-selected Customer:", bestMatch.CustomerName, "ID:", bestMatch.CustomerID);
                        
                        if (typeof intelliSub === 'function') {
                          intelliSub();
                        }
                        if (payload.subject) {
                          $('#subject').val(payload.subject);
                        }
                      }
                    } catch (e) {
                      console.error("Error auto-selecting customer:", e);
                    }
                  });
                } else {
                  if (payload.subject) {
                    $('#subject').val(payload.subject);
                  }
                }
              } else {
                console.error("Failed to load agentic payload:", response ? response.message : "unknown error");
                $('#mdl_custval').modal('show');
              }
            }).fail(function(err) {
              console.error("Error loading agentic payload from API:", err);
              $('#mdl_custval').modal('show');
            });
          }

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

