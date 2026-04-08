@extends('layouts.mail')

@section('content')
    {!! $email_content !!}

    @if(!empty($email_link))
        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ $email_link }}"
                style="background: linear-gradient(to left, rgba(10, 50, 30, 0.95), #0f1118); color: #a7f3d0; padding: 10px 22px; text-decoration: none; border-radius: 5px; font-weight: bold; display: inline-block;">
                View More Information
            </a>
        </div>
    @endif
@endsection