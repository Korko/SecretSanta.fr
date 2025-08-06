@component('mail::message')
# {{ __('emails.message_notification.greeting', ['name' => $recipientName]) }}

{{ __('emails.message_notification.body', ['title' => $drawTitle]) }}

@if($messageType === 'question')
{{ __('emails.message_notification.question_received') }}
@elseif($messageType === 'thank_you')
{{ __('emails.message_notification.thank_you_received') }}
@else
{{ __('emails.message_notification.message_received') }}
@endif

@component('mail::button', ['url' => $participantLink])
{{ __('emails.message_notification.button') }}
@endcomponent

{{ __('emails.message_notification.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent