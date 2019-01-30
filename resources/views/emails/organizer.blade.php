@extends('emails/layout')

@section('main')
    {!! nl2br(htmlentities( trans('emails.organizer', ['link' => $panelLink]) )) !!}
@endsection
