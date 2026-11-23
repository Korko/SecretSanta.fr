@extends('emails/layout')

@section('title')
    {{ __('emails.target_messages.heading') }}
@endsection

@section('content')
    @include('emails/partials/target_messages')
@endsection
