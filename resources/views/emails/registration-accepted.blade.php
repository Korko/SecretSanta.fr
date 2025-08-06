@component('mail::message')
# {{ __('emails.registration_accepted.greeting', ['name' => $participantName]) }}

{{ __('emails.registration_accepted.body', ['title' => $drawTitle, 'organizer' => $organizerName]) }}

{{ __('emails.registration_accepted.next_steps') }}

- {{ __('emails.registration_accepted.step1') }}
- {{ __('emails.registration_accepted.step2') }}
- {{ __('emails.registration_accepted.step3') }}

{{ __('emails.registration_accepted.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent