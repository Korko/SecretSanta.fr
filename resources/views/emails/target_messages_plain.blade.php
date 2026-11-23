@extends('emails/layout_plain')

@section('title')
    {!! __('emails.target_messages.heading') !!}
@endsection

@section('content')
    @include('emails/partials/target_messages_plain')
@endsection
