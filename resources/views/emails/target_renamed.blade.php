@extends('emails/layout')

@section('title', __('emails.target_renamed.heading'))

@section('content')
    <x-mail.p>{{ __('emails.hello', ['name' => $santaName]) }}</x-mail.p>
    <x-mail.p :top="8">{{ __('emails.target_renamed.body', ['old' => $oldName, 'new' => $newName]) }}</x-mail.p>
    <x-mail.gift :label="__('emails.gift.for')" :name="$newName" />
    <x-mail.p>{{ __('emails.target_renamed.same') }}</x-mail.p>
@endsection
