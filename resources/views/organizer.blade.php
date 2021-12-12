@extends('templates/layout')

@section('script')
    @parent

    @javascript([
        'pusher' => [
            'key' => Arr::get(config('websockets.apps'), '0.key'),
            'host' => Arr::get(config('websockets.apps'), '0.host'),
            'port' => substr(config('app.url'), 0, 5) === 'https' ? 443 : 80
        ]
    ])

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
@stop

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
        <li><a href="/">@lang('common.nav.go')</a></li>
        <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a></li>
    </ul>
@stop

@section('content')
    <vue-fetcher fetch="{{ URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]) }}">
        <template v-slot:default="slotProps">
            <organizer-form v-bind="slotProps.data" :routes="JSON.parse('{{ json_encode(['csvInitUrl' => URL::signedRoute('organizerPanel.csvInit', ['draw' => $draw]), 'csvFinalUrl' => URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]), 'deleteUrl' => URL::temporarySignedRoute('organizerPanel.delete', 3600, ['draw' => $draw]), 'fetchStateUrl' => URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]) ]) }}')"/>
        </template>
    </vue-fetcher>
@stop