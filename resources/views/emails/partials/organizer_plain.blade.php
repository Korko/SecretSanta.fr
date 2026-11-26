{!! __('emails.organizer.eyebrow') !!} - {!! __('emails.organizer.title') !!}

@if($organizer['samePage'] ?? false)
{!! __('emails.organizer.panel_same_page') !!}
@else
{!! __('emails.organizer.button') !!} : {!! $organizer['panelLink'] !!}
@endif

{!! __('emails.organizer.from') !!}
- {!! __('emails.organizer.check') !!}
- {!! __('emails.organizer.csv', ['attached' => __('emails.organizer.attached')]) !!}
- {!! __('emails.organizer.next', ['date' => $organizer['expirationDate']]) !!}

{!! __('emails.organizer.event_date') !!} : {!! $organizer['expirationDate'] !!}
{!! __('emails.organizer.deletion_date') !!} : {!! $organizer['deletionDate'] !!}

{!! __('emails.organizer.do_not_forward_title') !!} {!! __('emails.organizer.do_not_forward') !!}

{!! __('emails.organizer.abuse', ['button' => __('organizer.purge.button'), 'email' => config('app.email')]) !!}
