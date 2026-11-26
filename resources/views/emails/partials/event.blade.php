{{-- "Le jour J": the date, then the optional place and budget, only when given --}}
<p style="padding-bottom:10px !important"><strong>{{ __('emails.event.heading') }}</strong><br/>
    {{ __('emails.event.date', ['date' => $eventDate]) }}
@if($location)
    <br/>{{ __('emails.event.location', ['location' => $location]) }}
@endif
@if($budget)
    <br/>{{ __('emails.event.budget', ['budget' => $budget]) }}
@endif
</p>
