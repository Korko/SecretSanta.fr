@extends('emails/layout_plain')

@section('title')
    {!! __('emails.target_changed.heading') !!}
@endsection

@section('content')
    {!! __('emails.hello', ['name' => $santaName]) !!}
    {!! __('emails.target_withdrawn.body') !!}
    {!! __('emails.target_withdrawn.reassured', ['app' => config('app.name')]) !!}
    {!! __('emails.target.new', ['name' => $targetName]) !!}
    {!! __('emails.target.messages') !!}
@endsection
