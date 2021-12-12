@extends('templates/layout')

@section('script')
    @parent

    @javascript([
        'pusher' => [
            'key' => Arr::get(config('websockets.apps'), '0.key'),
            'host' => Arr::get(config('websockets.apps'), '0.host'),
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
    <vue-fetcher fetch="{{ URL::signedRoute('dearSanta.fetch', ['participant' => $participant]) }}">
        <template v-slot:default="slotProps">
            <dear-santa-form v-bind="slotProps.data" :routes="JSON.parse('{{ json_encode([ 'contactUrl' => URL::signedRoute('dearSanta.contact', ['participant' => $participant]), 'fetchStateUrl' => URL::signedRoute('dearSanta.fetchState', ['participant' => $participant]), 'subUrl' => URL::signedRoute('participant.sub', ['participant' => $participant]), 'unsubUrl' => URL::signedRoute('participant.unsub', ['participant' => $participant]) ]) }}')"/>
        </template>
    </vue-fetcher>
@stop