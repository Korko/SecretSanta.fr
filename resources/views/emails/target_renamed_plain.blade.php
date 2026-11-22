@extends('emails/layout_plain')

@section('title')
    {!! __('emails.target_renamed.heading') !!}
@endsection

@section('content')
    {!! __('emails.hello', ['name' => $santaName]) !!}
    {!! __('emails.target_renamed.body', ['old' => $oldName, 'new' => $newName]) !!}
    {!! __('emails.target_renamed.same') !!}
@endsection
