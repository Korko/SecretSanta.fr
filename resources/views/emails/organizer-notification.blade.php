@component('mail::message')
# {{ __('emails.organizer_notification.greeting', ['name' => $organizerName]) }}

{{ __('emails.organizer_notification.body', ['title' => $drawTitle]) }}

@if($notificationType === 'participant_joined')
{{ __('emails.organizer_notification.participant_joined') }}
@elseif($notificationType === 'message_sent')
{{ __('emails.organizer_notification.message_sent') }}
@elseif($notificationType === 'draw_completed')
{{ __('emails.organizer_notification.draw_completed') }}
@else
{{ __('emails.organizer_notification.general_update') }}
@endif

@component('mail::panel')
{{ $message }}
@endcomponent

@component('mail::button', ['url' => $organizerLink])
{{ __('emails.organizer_notification.button') }}
@endcomponent

{{ __('emails.organizer_notification.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent