@extends('emails/layout_plain')

@section('title')
{!! __('emails.target_messages.heading') !!}
@endsection

@section('content')
{!! __('emails.target_messages.lead') !!}

{!! __('emails.target_messages.target') !!} : {!! $targetName !!}

@include('emails/partials/target_messages_plain')
@endsection
