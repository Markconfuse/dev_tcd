@extends('layouts.page')

@section('title', $_details['_status'].' Request')

@section('content_header', $_details['_status'].' Request')

@section('css')

<link rel="stylesheet" href="{{ asset('adminlte/plugins/iCheck/all.css') }}">

<style type="text/css">
  .preloader {
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   /*z-index: 9999;*/
   background-image: url('https://digitalsynopsis.com/wp-content/uploads/2016/06/loading-animations-preloader-gifs-ui-ux-effects-11.gif');
   background-repeat: no-repeat; 
   background-color: #FFF;
   background-position: center;
}
</style>
@stop

@section('content')

<div class="row">
    <div class="col-sm-4">
    {{-- <h5 style="background-color: white;padding: 15px;font-style:italic;">Please (Hold SHIFT + F5) to get the latest version of the Portal</h5> --}}
    {{-- <h5 style="background-color: white;padding: 15px;font-style:italic;">To get the latest version of the Portal (Hold SHIFT + F5)</h5> --}}
    {{-- <h5 style="background-color: white;padding: 15px">The <b>Search</b> feature now includes searching for everything on the request, e.g. replies, request type, requestor, buyers, even combined (Cost Inquiry Cata 42 inch).</h5> --}}

   <div class="form-group clearfix">
      <div class="icheck-primary d-inline">
        <h5 style="background-color:white;padding:15px;font-size: 17px!important">
        <input type="checkbox" name="on_new_tab" value="is_checked" checked>
        <label>Open request in new tab</label>
        </h5>
      </div>
    </div>

  </div>
</div>

<div class="row">
  <div class="col-12">
    <div class="card">
      @include('layouts.components.preloader-round')
      <div class="card-header">
        <h3 class="card-title">{{ $_details['_status'] }} Request</h3>
        @include('requestor.status_request.sr_legend')
      </div>
      <!-- /.card-header -->

      <div class="card-body" style="overflow-x:scroll">
        <table id="tickets" class="table table-bordered table-hover">
          <thead>
            <tr style="font-size:14px !important">
              <th style="min-width:1px"></th>
              <th style="width:80px;text-align:center">Reference No</th>
              <th style="width:410px;text-align:center">Subject</th>
              <th style="text-align:center">Requestor</th>
              <th hidden>content,reply,ao_name,requestor,OwnerName</th>
              <th style="width:98px;text-align:center">Assignee</th>
              <th style="text-align:center">Replies</th>
              <th style="text-align:center">Last Updated</th>
              <th class="none">Date Created</th>
              <th class="none">Customer Name</th>
              <th class="none">Request Type</th>
              <th class="none">Project Name</th>
              <th class="none">Status</th>
              <th class="none">Last Transaction</th>
            </tr>
          </thead>
          <tbody style="font-size:15px">

          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
  </div>
</div>

@stop

@section('js')
<script src="{{ asset('adminlte/plugins/iCheck/icheck.min.js') }}"></script>
<script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script>
<!-- <script src="{{ asset('public/adminlte/cdnjs-local/datetime-moment.js') }}"></script> -->
<script src="{{ asset('js/render_ticket_table.js') }}"></script>
<script type="text/javascript">
  $(function () {

    $("input[name='on_new_tab']").iCheck({
      checkboxClass: 'icheckbox_flat',
      increaseArea: '20%' 
    });

    setTimeout(function() {
      // console.log('{{ route('getTicket') }}?sid='+'{{ $_details['_statusID'] }}');

          table = renderDtable('{{ route('getTicket') }}?sid='+'{{ $_details['_statusID'] }}', 'tickets');
          
    }, 500)

    $('#tickets').on('click', 'td.tdClick', function() {

      if(table.row(this).data() !== undefined) {
        var url = '{{ route("view-request", ":slug") }}';

        url = url.replace(':slug', btoa(table.row(this).data()['ticket_id']));

        is_new_tab = $("input[name='on_new_tab']").iCheck('update')[0].checked
        if(is_new_tab) {
          window.open(url, '_blank');
        } else {
          $('.preloader-round').removeAttr('hidden', 'hidden');
          window.location.href = url;
        }
      }

    })
  });
</script>

@stop

