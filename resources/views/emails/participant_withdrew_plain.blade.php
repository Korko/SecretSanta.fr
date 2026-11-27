@extends('emails/layout_plain')

@section('title')
{!! __('emails.participant_withdrew.heading', ['name' => $name]) !!}
@endsection

@section('content')
{!! __('emails.hello', ['name' => $organizerName]) !!}

{!! __('emails.participant_withdrew.body', ['name' => $name, 'app' => config('app.name')]) !!}

{!! __('emails.participant_withdrew.santa', ['name' => $name]) !!}

{!! __('emails.participant_withdrew.restore', ['name' => $name]) !!}
@if($panelLink)

{!! __('emails.organizer.button') !!} : {!! $panelLink !!}
@endif
@endsection
