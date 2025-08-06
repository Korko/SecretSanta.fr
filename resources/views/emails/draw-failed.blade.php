@component('mail::message')
# {{ __('emails.draw_failed.greeting', ['name' => $organizerName]) }}

{{ __('emails.draw_failed.body', ['title' => $drawTitle]) }}

{{ __('emails.draw_failed.error_message') }}

@component('mail::panel')
{{ $errorMessage }}
@endcomponent

{{ __('emails.draw_failed.suggestion') }}

@component('mail::button', ['url' => $organizerLink])
{{ __('emails.draw_failed.button') }}
@endcomponent

{{ __('emails.draw_failed.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent