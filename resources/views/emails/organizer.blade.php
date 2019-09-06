@extends('emails/layout')

@section('main')
    {!! nl2br( trans('emails.organizer.content', ['link' => "<a href=\"$panelLink\">$panelLink</a>", 'expiration' => $draw->expires_at]) ) !!}
@endsection
