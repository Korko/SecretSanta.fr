@component('mail::message')
# {{ __('emails.registration_request.greeting', ['name' => $organizerName]) }}

{{ __('emails.registration_request.body', ['participant' => $participantName, 'title' => $drawTitle]) }}

{{ __('emails.registration_request.participant_info') }}

@component('mail::table')
| {{ __('emails.registration_request.field') }} | {{ __('emails.registration_request.value') }} |
| :--- | :--- |
| {{ __('emails.registration_request.name') }} | {{ $participantName }} |
| {{ __('emails.registration_request.email') }} | {{ $participantEmail }} |
| {{ __('emails.registration_request.date') }} | {{ now()->format('d/m/Y H:i') }} |
@endcomponent

{{ __('emails.registration_request.action_required') }}

@component('mail::button', ['url' => $organizerLink])
{{ __('emails.registration_request.button') }}
@endcomponent

{{ __('emails.registration_request.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent