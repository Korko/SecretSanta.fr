    {{ __('emails.organizer.panel', ['link' => $organizer['panelLink']]) }}

    {{ __('emails.organizer.csv_plain', ['app' => config('app.name')]) }}
@if($organizer['nextSolvable'])

    {{ __('emails.organizer.next_plain', ['date' => $organizer['expirationDate']]) }}
@endif

    {{ __('emails.organizer.deletion', ['date' => $organizer['deletionDate']]) }}

