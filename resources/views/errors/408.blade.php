@extends('layouts.page')

@section('title', 'Ticket Not Found')

@section('content_header', 'Ticket Not Found')

@section('content')

  <div class="error-page">
    <h2 class="headline text-warning"> ??</h2>

    <div class="error-content">
      <h3><i class="fas fa-exclamation-triangle text-warning"></i> Oops! Ticket Not Found.</h3>

      <p>
        We could not find the ticket you were looking for. <br>
        Make sure that you input the correct Reference/Ticket No.
      </p>
    </div>
    <!-- /.error-content -->
  </div>

@stop

