@if($organizer['samePage'] ?? false)
    <p style="padding-bottom:10px !important">{{ __('emails.organizer.panel_same_page') }}</p>
@else
    <p style="padding-bottom:10px !important">{!! __('emails.organizer.panel', ['link' => '<a href="'.e($organizer['panelLink']).'">'.e($organizer['panelLink']).'</a>']) !!}</p>
@endif

    <p style="padding-bottom:10px !important">{{ __('emails.organizer.csv', ['app' => config('app.name')]) }}</p>

    @if($organizer['nextSolvable'])<p style="padding-bottom:10px !important">{{ __('emails.organizer.next', ['date' => $organizer['expirationDate']]) }}</p>@endif

    <p style="padding-bottom:10px !important">{{ __('emails.organizer.deletion', ['date' => $organizer['deletionDate']]) }}</p>

    {{-- Someone else may have used this address to send a draw, as spam: what the participants' report line is for them --}}
    <p style="padding-bottom:10px !important"><small>{!! __('emails.organizer.abuse', ['button' => e(__('organizer.purge.button')), 'email' => '<a href="mailto:'.e(config('app.email')).'">'.e(config('app.email')).'</a>']) !!}</small></p>
