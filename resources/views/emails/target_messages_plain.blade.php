@extends('emails/layout_plain')

@section('title')
    {!! __('emails.target_messages.heading', ['name' => $targetName]) !!}
@endsection

@section('content')
    {!! __('emails.target_messages.intro', ['name' => $targetName]) !!}
@foreach ($messages as $targetMessage)

    {!! $targetMessage['date'] !!}
    > {!! $targetMessage['content'] !!}
@endforeach
@endsection
