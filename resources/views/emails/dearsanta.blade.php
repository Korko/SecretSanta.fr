@extends('emails/layout')

@section('main')
    <p>{{ $content }}</p>

    {!! nl2br(htmlentities( trans('form.mail.post') )) !!}
@endsection
