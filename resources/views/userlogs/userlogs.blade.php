@extends('layouts.page')

@section('title', 'User Logs')

@section('content_header', 'User Logs')

@section('css')

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

 {{-- <div class="preloader"></div> --}}
  <div class="col-12">
    <div class="card">
      <div class="card-header">
        <h3 class="card-title">As of {{ \Carbon\Carbon::now()->format('M d, Y h:ia') }}: There are <b>{{ count($logs) }}</b> user logs. </h3>
      </div>
      <!-- /.card-header -->
      <div class="card-body" style="overflow-x:scroll">
        <table id="tblLogs" class="table table-bordered table-hover">
          <thead>
            <tr>
              <td hidden></td>
              <th style="width:134px;">User</th>
              <th style="width:410px">Module</th>
              <th>Activity</th>
              <th style="width:134px;">Date</th>
            </tr>
          </thead>
          <tbody style="font-size: 15px;">
            @foreach($logs as $log)
              <tr>
                <td hidden>{{ $log->id }}</td>
                <td><img src="{{ $log->GAvatar }}" class="name-avatar brand-image img-circle elevation-0" alt="User Image">{{ $log->NickName }}</td>
                <td>{{ $log->module }}</td>
                <td>{{ $log->activity }}</td>
                <td>{{ \Carbon\Carbon::parse($log->date_time)->format('m/d/y h:ia') }}</td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <!-- /.card-body -->
    </div>
  </div>
</div>

@stop

@section('js')

{{-- <script src="https://cdn.datatables.net/plug-ins/1.10.19/sorting/datetime-moment.js"></script> --}}
<script type="text/javascript">
  $(function() {
    $('#tblLogs').DataTable({
      order: [[ 0, "desc" ]]
    });
  })
</script>

@stop

