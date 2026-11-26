{!! __('emails.event.heading') !!}
    {!! __('emails.event.date', ['date' => $eventDate]) !!}
@if($location)
    {!! __('emails.event.location', ['location' => $location]) !!}
@endif
@if($budget)
    {!! __('emails.event.budget', ['budget' => $budget]) !!}
@endif
