<!DOCTYPE html>
<html lang="en">
<head>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta http-equiv="x-ua-compatible" content="ie=edge">
  <meta name="robots" content="noindex">

  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <meta name="author" content="appsdev">
  <link rel="icon" href="{{ asset('img/assets/tcd-icon_trns_v2.ico') }}">
  <title>
    @yield('title', config('appsdev_conf.title', 'Appsdev Team'))
    @yield('title_postfix', config('appsdev_conf.title_postfix', ''))
  </title>

  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/bootstrap/css/bootstrap.min.css') }}">
  <link rel="stylesheet" href="{{ asset('css/fontawesome/css/all.min.css') }}">

  <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.css" rel="stylesheet">
  <!-- <link href="{{ asset('public/adminlte/cdnjs-local/summernote-bs4.min.css') }}" rel="stylesheet"> -->

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.1.3/css/bootstrap.css">
  <!-- <link href="{{ asset('public/adminlte/cdnjs-local/bootstrap.css') }}" rel="stylesheet"> -->

  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
  <!-- <link href="{{ asset('public/adminlte/cdnjs-local/dataTables.bootstrap4.min.css') }}" rel="stylesheet"> -->

  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.3/css/responsive.bootstrap4.min.css">
  <!-- <link href="{{ asset('public/adminlte/cdnjs-local/responsive.bootstrap4.min.css') }}" rel="stylesheet"> -->

  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.5.1/basic.css">
  <!-- <link href="{{ asset('public/adminlte/cdnjs-local/basic.css') }}" rel="stylesheet"> -->

  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/min/dropzone.min.css">
  <!-- <link href="{{ asset('public/adminlte/cdnjs-local/dropzone.min.css') }}" rel="stylesheet"> -->

  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('adminlte/dist/css/adminlte.min.css') }}">
  
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/select2.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/select2/select2.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/toastr/toastr.min.css') }}">
  <link rel="stylesheet" href="{{ asset('adminlte/plugins/sweetalert2/sweetalert.min.css') }}">

  <link rel="stylesheet" href="{{ asset('css/appsdev.css') }}">
  <!-- Google Font: Source Sans Pro -->
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">

  @yield('adminlte_css')  
</head>

<body class="hold-transition sidebar-mini @yield('body_class')">

  @yield('body')

<!-- ./wrapper -->

@include('layouts.components.announcement_modal')

<!-- REQUIRED SCRIPTS -->

<!-- jQuery -->


{{-- <script src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script> --}}
<script src="{{ asset('adminlte/plugins/jquery/jquery.min.js') }}"></script>

<!-- Bootstrap -->
<script src="{{ asset('adminlte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>

<script src="https://cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/jquery.dataTables.min.js') }}"></script> -->

<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dataTables.bootstrap4.min.js') }}"></script> -->

<script src="https://cdn.datatables.net/responsive/2.2.3/js/dataTables.responsive.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dataTables.responsive.min.js') }}"></script> -->

<script src="https://cdn.datatables.net/responsive/2.2.3/js/responsive.bootstrap4.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/responsive.bootstrap4.min.js') }}"></script> -->

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.4.0/dropzone.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/dropzone.js') }}"></script> -->

<!-- SlimScroll -->
<script src="{{ asset('adminlte/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.11.2/moment.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/moment.min.js') }}"></script> -->
@yield('adminlte_js')


<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/summernote-bs4.min.js') }}"></script> -->

<script src="{{ asset('adminlte/plugins/select2/select2.full.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/toastr/toastr.min.js') }}"></script>
<script src="{{ asset('adminlte/plugins/sweetalert2/sweetalert.min.js') }}"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.18/summernote-bs4.min.js"></script>



<script src="{{ asset('adminlte/dist/js/adminlte.min.js') }}"></script>
<script src="{{ asset('js/timeago.js') }}"></script>
{{-- <script src="https://adminlte.io/themes/dev/AdminLTE/dist/js/adminlte.min.js"></script> --}}

<!-- OPTIONAL SCRIPTS -->
{{-- <script src="{{ asset('adminlte/plugins/chart.js/Chart.min.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/demo.js') }}"></script>
<script src="{{ asset('adminlte/dist/js/pages/dashboard3.js') }}"></script> --}}
<script type="text/javascript">

  $(function() {

    if('<?php echo Session::has('message') ?>') {

      message = '<?php echo Session::get('message') ?>';
      type = '<?php echo Session::get('status') ?>';

      Swal.fire({
        type: type,
        title: message,
        width: '370px',
        showConfirmButton: false,
        timer: 2000
      })
    }

    $('#btnTCD').on('click', function() {
      Swal.fire({
        title: 'TCD Portal',
        width: '360px',
        showConfirmButton: false,
        timer: 2000
      })
    })

    $('#gauth').on('click', function() {
      // $('.preloader-round').removeAttr('hidden', '');
      $('.preloader-round').removeAttr('hidden', '');
      window.location.href = '{{ route('googleRedirect') }}';
    })
    
    /** add active class and stay opened when selected */
    var url = window.location;

    // for sidebar menu entirely but not cover treeview
    $('ul.nav a').filter(function() {
       return this.href == url;
    }).addClass('active');

    $('ul.nav-treeview a').filter(function() {
      return this.href == url;
    }).parentsUntil(".nav-sidebar > .nav-treeview").addClass('menu-open').prev('a').addClass('active');
  })


  $('.setAcc').on('click', function() {
    Swal.fire({
      title: 'Configuring your account. . . please wait.',
      width: '400px',
      showConfirmButton: false
    })
  })

  function formatDate(date) {
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var ampm = hours >= 12 ? 'pm' : 'am';
    hours = hours % 12;
    hours = hours ? hours : 12; // the hour '0' should be '12'
    minutes = minutes < 10 ? '0'+minutes : minutes;
    var strTime = hours + ':' + minutes + ' ' + ampm;

    var monthname = ['Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
            // var d = new Date(value.DateCreated);
    // var formatted = monthname[d.getMonth()]+" "+d.getDate()+", "+d.getFullYear();

    return monthname[date.getMonth()]+ " " + date.getDate() + ", " + date.getFullYear() + "  " + strTime;
  }

</script>

<script src="https://js.pusher.com/5.0/pusher.min.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/pusher.min.js') }}"></script> -->
<script>

  // Enable pusher logging - don't include this in production
  // Pusher.logToConsole = true;
  var auth = {{ auth()->check() ? 'true' : 'false' }};

  if (auth) {
    // refreshNotif();
    setTimeout(function() {
      refreshSideCount();
    }, 500)

    var pusher = new Pusher('71e7b574be362438042a', {
      cluster: 'ap1',
      forceTLS: true
    });

    var channel = pusher.subscribe('my-channel');
    channel.bind('form-submitted', function(data) {
      
      counter = JSON.stringify(data['new_notif']);

      if(counter > 0) {
        // refreshNotif();
        refreshSideCount();
        $('#tickets').DataTable().ajax.reload();
      } else {
        alert('Null');
      }

    });
  }

  function refreshNotif() {
    notif = "<span class='dropdown-item dropdown-header markReadNav'>Mark all as read</span>";
    $('#dvNotif').html('');
    $('.preloader-ring').removeAttr('hidden', '');

    $.get('{{ url('countUnread') }}').done(function(data) {
       $('#spnBadgeCounter').text(data['count']);
        if(data['count'] > 0) {
        $('#spnHeaderCounter').text(data['count']+' Notifications');
      } else {
        $('#spnHeaderCounter').text(data['count']+' Notification');
      }
    }).fail(function(data) {
      refreshNotif();
    })

    $.get('{{ url('unreadNotification') }}').done(function(data) {
      $.each(data.data, function(key, val) {
          notif += "<div class='dropdown-divider'></div>";
          notif += "<a id="+val['ticket_id']+" class='dropdown-item list-notif pointer'>";
          notif += "<div class='media'>";
          notif += "<img src='"+val['CGAvatar']+"' alt='User Avatar' class='img-size-50 img-circle mr-3'>";
          notif += "<div class='media-body'><h3 class='dropdown-item-title'>"+val['CName'];
          notif += "<span class='float-right text-sm text-muted'><i class='far fa-circle'></i></span></h3>";
          notif += "<p class='text-sm'>"+val['notification']+"</p>";
          notif += "<p class='text-sm text-muted'><i class='far fa-clock mr-1'></i>"+$.timeago(val['created_date'])+"</p>";
          notif += "</div></div></a>";
          notif += "<div class='dropdown-divider'></div>";
          $('#dvNotif').html(notif);
      })
      $('.preloader-ring').attr('hidden', 'hidden');
    }).fail(function(data) {
      refreshNotif();
    })

  }

  function refreshSideCount() {
    $.get('{{ url('getSideCount') }}').done(function(data) {

      if(data.user  == 'engineer' && (parseInt(data.account_id) != 57610 && parseInt(data.account_id) != 57615)) {
        $('#pendingctr').text(data.pendingctr.count);
        $('#answeredctr').text(data.answeredctr.count);
        $('#closedctr').text(data.closedctr.count);
        $('#reassignedctr').text(data.reassignedctr.count);
  		  $('#escalatedctr').text(data.escalatedctr.count);
		  
      } else {
        $('#unassignedctr').text(data.unassignedctr.count);
        $('#assignedctr').text(data.assignedctr.count);
        $('#answeredctr').text(data.answeredctr.count);
        $('#closedctr').text(data.closedctr.count);
		
      }

      if(data.user == 'admin' || data.user == 'super_user' || data.account_id == 57610 || data.account_id == 57615) {

        $('#reassignedctr').text(data.reassignedctr.count);
        $('#cebuctr').text(data.cebuctr);
    		$('#escalatedctr').text(data.escalatedctr.count);
      }

      if(parseInt(data.account_id) == 57615) {
        $('#pendingctr').text(data.pendingctr.count);
      }

    }).fail(function(data) {
      // refreshSideCount();
    })
  }

    $(document).on('click', '.list-notif', function() {
    var ticketID = btoa($(this).attr('id'));

    window.open('{{ url('view-request') }}/'+ticketID, '_self');
  });

  $(document).ready(function() {
    var activeAnnouncements = [];
    var currentIndex = 0;

    function showAnnouncement(index) {
        if(index >= activeAnnouncements.length) {
            $('#announcementModal').modal('hide');
            return;
        }
        var ann = activeAnnouncements[index];
        $('#annTitle').text(ann.title);
        
        var d = new Date(ann.released_date || ann.created_at);
        var monthname = ['Jan', 'Feb', 'March', 'April', 'May', 'June', 'July', 'Aug', 'Sept', 'Oct', 'Nov', 'Dec'];
        $('#annDate').text(monthname[d.getMonth()]+ " " + d.getDate() + ", " + d.getFullYear());
        
        $('#annContent').html(ann.content);
        $('#btnUnderstand').attr('data-id', ann.id);

        if (ann.assets && ann.assets.length > 0) {
            var filesHtml = '';
            ann.assets.forEach(function(asset) {
                var sizeKB = Math.round(asset.file_size / 1024);
                var fileUrl = asset.fille_url || asset.file_url || asset.file_path || '#';
                var iconClass = 'fa-file-alt text-secondary';
                
                if(asset.mime_type) {
                    if(asset.mime_type.indexOf('pdf') !== -1) iconClass = 'fa-file-pdf text-danger';
                    else if(asset.mime_type.indexOf('image') !== -1) iconClass = 'fa-file-image text-info';
                    else if(asset.mime_type.indexOf('word') !== -1) iconClass = 'fa-file-word text-primary';
                    else if(asset.mime_type.indexOf('excel') !== -1 || asset.mime_type.indexOf('spreadsheet') !== -1) iconClass = 'fa-file-excel text-success';
                }
                
                filesHtml += '<a href="'+fileUrl+'" target="_blank" class="text-decoration-none d-block mb-2">' +
                             '<div class="d-flex align-items-center p-2 border rounded bg-light" style="transition: all 0.2s; box-shadow: 0 1px 2px rgba(0,0,0,0.05);" onmouseover="this.style.boxShadow=\'0 2px 5px rgba(0,0,0,0.1)\';" onmouseout="this.style.boxShadow=\'0 1px 2px rgba(0,0,0,0.05)\';">' +
                             '<div class="mr-3"><i class="fas '+iconClass+' fa-2x"></i></div>' +
                             '<div class="flex-grow-1 text-dark">' +
                             '<div class="font-weight-bold text-truncate" style="font-size: 12px; line-height: 1.2; max-width: 200px;" title="'+asset.file_name+'">'+asset.file_name+'</div>' +
                             '<div class="text-muted small">'+sizeKB+' KB</div>' +
                             '</div>' +
                             '<div class="text-primary"><i class="fas fa-download"></i></div>' +
                             '</div></a>';
            });
            $('#annAttachmentsContainer').html(filesHtml).show();
        } else {
            $('#annAttachmentsContainer').hide();
        }

        if(ann.thumbnail_url) {
            var imgPath = ann.thumbnail_url;
            $('#annImageContainer').html('<img src="' + imgPath + '" alt="Announcement Preview" style="position: absolute; top: 0; left: 0; width: 100%; height: 100%; object-fit: cover;" />');
        } else {
            var ph = '<div class="d-flex flex-column align-items-center justify-content-center text-muted h-100 w-100 p-4 text-center bg-light border-left">';
            ph += '<div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mb-3" style="width: 64px; height: 64px;">';
            ph += '<i class="far fa-image fa-2x text-white"></i></div>';
            ph += '<p class="font-weight-bold mb-1">No Image</p><p class="small">No thumbnail provided for this announcement.</p></div>';
            $('#annImageContainer').html(ph);
        }

        $('#announcementModal').modal('show');
    }

    if (auth) {
        $.ajax({
            url: "{{ url('api/announcements/active') }}",
            type: "GET",
            success: function(response) {
                if(response.status === 'success' && response.data.length > 0) {
                    activeAnnouncements = response.data;
                    currentIndex = 0;
                    showAnnouncement(currentIndex);
                }
            }
        });
    }

    $('#btnUnderstand').on('click', function() {
        var id = $(this).attr('data-id');
        var btn = $(this);
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');
        
        $.ajax({
            url: "{{ url('api/announcements/acknowledge') }}",
            type: "POST",
            data: {
                announcement_id: id,
                _token: "{{ csrf_token() }}"
            },
            success: function() {
                btn.prop('disabled', false).text('I Understand');
                currentIndex++;
                showAnnouncement(currentIndex);
            },
            error: function() {
                btn.prop('disabled', false).text('I Understand');
                currentIndex++;
                showAnnouncement(currentIndex);
            }
        });
    });

    $('.announcement-close').on('click', function() {
       // Only allowed to close visually? But next ones if they don't click "I understand" won't show.
       // The modal 'data-keyboard=false' prevents escape closing. They must understand.
       // So clicking close could mean "skip this one" visually for testing? 
       // I'll leave standard behavior.
    });
  });

</script>


</body>
</html>
