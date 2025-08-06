@component('mail::message')
# {{ __('emails.registration_rejected.greeting', ['name' => $participantName]) }}

{{ __('emails.registration_rejected.body', ['title' => $drawTitle]) }}

@if($reason)
{{ __('emails.registration_rejected.reason_intro') }}

@component('mail::panel')
{{ $reason }}
@endcomponent
@endif

{{ __('emails.registration_rejected.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent