@if($organizer['samePage'] ?? false)
    {!! __('emails.organizer.panel_same_page') !!}
@else
    {!! __('emails.organizer.panel', ['link' => $organizer['panelLink']]) !!}
@endif

    {!! __('emails.organizer.csv', ['app' => config('app.name')]) !!}
@if($organizer['nextSolvable'])

    {!! __('emails.organizer.next', ['date' => $organizer['expirationDate']]) !!}
@endif

    {!! __('emails.organizer.deletion', ['date' => $organizer['deletionDate']]) !!}

    {!! __('emails.organizer.abuse', ['button' => __('organizer.purge.button'), 'email' => config('app.email')]) !!}
