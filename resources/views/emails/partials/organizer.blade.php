{{--
    The organizer's part: within their own target email (TargetDrawn, in a panel) or as the whole recap (OrganizerRecap).
    $organizer: OrganizerRecap::viewData(), with samePage when their participant link above already opens the panel (a single link).
--}}
@php($inPanel = $inPanel ?? true)
@php($dashboard = [
    e(__('emails.organizer.check')),
    __('emails.organizer.csv', ['attached' => '<strong style="color: #34454f; font-weight: 700; white-space: nowrap;">'.e(__('emails.organizer.attached')).'</strong>']),
    e(__('emails.organizer.next', ['date' => $organizer['expirationDate']])),
])
@if($inPanel)
<div style="background-color: #f6ece3; border-radius: 16px; margin-top: 32px; padding: 24px;">
    <p style="letter-spacing: .06em; text-transform: uppercase; margin: 0; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 13px; font-weight: 700; line-height: 18px; color: #5e1618;">{{ __('emails.organizer.eyebrow') }}</p>
    <x-mail.h2 :top="4">{{ __('emails.organizer.title') }}</x-mail.h2>
    <x-mail.p :top="8">{{ __('emails.organizer.thanks', ['app' => config('app.name'), 'count' => $organizer['participantsCount'] ?? '']) }}</x-mail.p>
@endif
@if($organizer['samePage'] ?? false)
    <x-mail.p>{{ __('emails.organizer.panel_same_page') }}</x-mail.p>
@else
    <x-mail.button :href="$organizer['panelLink']" :variant="$inPanel ? 'kraft-panneau' : 'sapin'">{{ __('emails.organizer.button') }}</x-mail.button>
@endif
    <p style="color: #34454f; font-family: Aileron, 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 15px; font-weight: 700; line-height: 22px; margin: 24px 0 0;">{{ __('emails.organizer.from') }}</p>
    <x-mail.bullets :items="$dashboard" />
    <x-mail.rows :panel="!$inPanel" :rows="[
        __('emails.organizer.event_date') => $organizer['expirationDate'],
        __('emails.organizer.deletion_date') => $organizer['deletionDate'],
    ]" />
    <x-mail.p muted small><strong style="color: #34454f;">{{ __('emails.organizer.do_not_forward_title') }}</strong> {{ __('emails.organizer.do_not_forward') }}</x-mail.p>
    {{-- Someone else may have used this address to send a draw, as spam: what the participants' report line is for them --}}
    <x-mail.p muted small :top="8">{!! __('emails.organizer.abuse', ['button' => e(__('organizer.purge.button')), 'email' => '<a href="mailto:'.e(config('app.email')).'" style="color: #66757d; text-decoration: underline;">'.e(config('app.email')).'</a>']) !!}</x-mail.p>
@if($inPanel)
</div>
@endif
