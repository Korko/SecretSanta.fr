@component('mail::message')
# {{ __('emails.participant_draw_ready.greeting', ['name' => $participantName]) }}

{{ __('emails.participant_draw_ready.body', ['title' => $drawTitle]) }}

{{ __('emails.participant_draw_ready.target_intro') }}

**{{ $targetName }}**

@if($exclusionsCount > 0)
{{ __('emails.participant_draw_ready.exclusions_notice', ['count' => $exclusionsCount]) }}
@endif

@component('mail::button', ['url' => $participantLink])
{{ __('emails.participant_draw_ready.button') }}
@endcomponent

{{ __('emails.participant_draw_ready.footer') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent