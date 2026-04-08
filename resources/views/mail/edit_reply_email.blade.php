@extends('layouts.mail')

@section('content')
    {!! $content !!}

    <div style="text-align: center; margin: 30px 0;">
        <a href="{{ $info }}"
            style="background: linear-gradient(to left, rgba(10, 50, 30, 0.95), #0f1118); color: #a7f3d0; padding: 10px 22px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
            View More Information
        </a>
    </div>
@endsection