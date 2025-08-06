@component('mail::message')
# {{ __('emails.draw_completed.greeting', ['name' => $organizerName]) }}

{{ __('emails.draw_completed.body', ['title' => $drawTitle]) }}

{{ __('emails.draw_completed.participants_count', ['count' => $participantsCount]) }}

{{ __('emails.draw_completed.next_steps') }}

- {{ __('emails.draw_completed.step1') }}
- {{ __('emails.draw_completed.step2') }}
- {{ __('emails.draw_completed.step3') }}

@component('mail::button', ['url' => $organizerLink])
{{ __('emails.draw_completed.button') }}
@endcomponent

{{ __('emails.draw_completed.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent