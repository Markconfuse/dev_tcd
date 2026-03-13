@extends('layouts.master')


@section('body_class', 'login-page')

@section('adminlte_css')

@stop

@section('body')

<div class="login-box">
  <div class="login-logo">
    <a style="cursor:pointer" id="btnTCD" title="TCD Portal"><b>TCD</b> Portal</a>
  </div>
  <!-- /.login-logo -->
  <div class="card">
    @include('layouts.components.preloader-round')
    <div class="card-body login-card-body">
      <p class="login-box-msg">Sign in to start your session</p>

      <div class="social-auth-links text-center mb-3">
        <button id="gauth" class="btn btn-block btn-danger">
          <i class="fab fa-google-plus mr-2"></i> Sign in using ICS Gmail Account
        </button>
      </div>
      <!-- /.social-auth-links -->
    </div>
    <!-- /.login-card-body -->
  </div>
{{--   <div id="preloader-milktea" class="overlay" style="background: url(https://i.giphy.com/media/3oKIPv4pMwu3NQtKhO/giphy.webp) center no-repeat #ffffffc7;background-size: 300px 230px;" hidden>
  </div> --}}
</div>

@stop
