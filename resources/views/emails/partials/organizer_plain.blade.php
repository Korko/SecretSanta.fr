    {!! __('emails.organizer.panel', ['link' => $organizer['panelLink']]) !!}

    {!! __('emails.organizer.csv', ['app' => config('app.name')]) !!}
@if($organizer['nextSolvable'])

    {!! __('emails.organizer.next', ['date' => $organizer['expirationDate']]) !!}
@endif

    {!! __('emails.organizer.deletion', ['date' => $organizer['deletionDate']]) !!}

