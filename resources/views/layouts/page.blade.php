@extends('layouts.master')

@section('adminlte_css')
    @stack('css')
    @yield('css')
@stop

@section('body')

<div class="wrapper" style="background-color: #f4f6f9">
  <!-- Navbar -->
    @include('layouts.components.navbar')
  <!-- /.navbar -->

  <!-- Main Sidebar Container -->
    @include('layouts.components.sidebar')


  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper" >
    @include('layouts.components.loader')

    <div class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1 class="m-0 text-dark">@yield('content_header')</h1>
          </div><!-- /.col -->
        </div><!-- /.row -->
      </div><!-- /.container-fluid -->
    </div>

    <!-- Main content -->
    <div class="content">
      <div class="container-fluid">

        @yield('content')

        <!-- /.row -->
      </div>
      <!-- /.container-fluid -->
    </div>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->

  <!-- Control Sidebar -->
  <footer class="main-footer" style="background-color: #f4f6f9">
 {{--    <strong>Copyright © 2014-2019 <a href="http://adminlte.io">AdminLTE.io</a>.</strong>
    All rights reserved. --}}
{{--     <div class="float-right d-none d-sm-inline-block">
      <b>Version</b> 1.1
    </div> --}}
  </footer>

  <aside class="control-sidebar control-sidebar-dark">
    <!-- Control sidebar content goes here -->
  </aside>
  <!-- /.control-sidebar -->

</div>
@stop

@section('adminlte_js')
  @stack('css')
  @yield('js')
@stop