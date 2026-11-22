    <p style="padding-bottom:10px !important">{!! __('emails.organizer.panel', ['link' => '<a href="'.e($organizer['panelLink']).'">'.e($organizer['panelLink']).'</a>']) !!}</p>

    <p style="padding-bottom:10px !important">{{ __('emails.organizer.csv', ['app' => config('app.name')]) }}</p>

    @if($organizer['nextSolvable'])<p style="padding-bottom:10px !important">{{ __('emails.organizer.next', ['date' => $organizer['expirationDate']]) }}</p>@endif

    <p style="padding-bottom:10px !important">{{ __('emails.organizer.deletion', ['date' => $organizer['deletionDate']]) }}</p>

