@component('mail::message')
# {{ __('emails.participant_invitation.greeting', ['name' => $participantName]) }}

{{ __('emails.participant_invitation.body', ['organizer' => $organizerName, 'title' => $drawTitle]) }}

@if(isset($eventDate))
{{ __('emails.participant_invitation.date', ['date' => $eventDate]) }}
@endif

@if(isset($budget))
{{ __('emails.participant_invitation.budget', ['budget' => $budget]) }}
@endif

{{ __('emails.participant_invitation.instructions') }}

@component('mail::panel')
{{ __('emails.participant_invitation.access_key', ['key' => $key]) }}
@endcomponent

@component('mail::button', ['url' => $link])
{{ __('emails.participant_invitation.button') }}
@endcomponent

{{ __('emails.participant_invitation.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent
