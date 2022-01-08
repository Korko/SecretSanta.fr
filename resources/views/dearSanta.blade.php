@extends('templates/layout')

@section('navbar-right')
    <li><a href="/">@lang('common.nav.go')</a></li>
    <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a></li>
@stop

@section('content')
    <vue-fetcher fetch="{{ URL::signedRoute('dearSanta.fetch', ['participant' => $participant]) }}">
        <template v-slot:default="slotProps">
            <dear-santa-form v-bind="slotProps.data" :routes="JSON.parse('{{ json_encode([ 'contactUrl' => URL::signedRoute('dearSanta.contact', ['participant' => $participant]), 'fetchStateUrl' => URL::signedRoute('dearSanta.fetchState', ['participant' => $participant]), 'subUrl' => URL::signedRoute('participant.sub', ['participant' => $participant]), 'unsubUrl' => URL::signedRoute('participant.unsub', ['participant' => $participant]) ]) }}')"/>
        </template>
    </vue-fetcher>
@stop