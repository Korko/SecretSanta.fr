@component('mail::message')
# {{ __('emails.admin_alert.greeting') }}

{{ __('emails.admin_alert.level', ['level' => strtoupper($level)]) }}

@component('mail::panel')
{{ $message }}
@endcomponent

@if(count($context) > 0)
{{ __('emails.admin_alert.context') }}

@component('mail::table')
| {{ __('emails.admin_alert.key') }} | {{ __('emails.admin_alert.value') }} |
| :--- | :--- |
@foreach($context as $key => $value)
| {{ $key }} | {{ is_array($value) ? json_encode($value) : $value }} |
@endforeach
@endcomponent
@endif

{{ __('emails.admin_alert.timestamp') }} {{ now()->format('Y-m-d H:i:s') }}

{{ __('emails.common.regards') }},<br>
{{ config('app.name') }}
@endcomponent