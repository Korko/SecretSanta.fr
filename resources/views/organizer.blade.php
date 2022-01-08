@extends('templates/layout')

@section('navbar-right')
    <li><a href="/">@lang('common.nav.go')</a></li>
    <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a></li>
@stop

@section('content')
    <vue-fetcher fetch="{{ URL::signedRoute('organizerPanel.fetch', ['draw' => $draw]) }}">
        <template v-slot:default="slotProps">
            <organizer-form v-bind="slotProps.data" :routes="JSON.parse('{{ json_encode(['csvInitUrl' => URL::signedRoute('organizerPanel.csvInit', ['draw' => $draw]), 'csvFinalUrl' => URL::signedRoute('organizerPanel.csvFinal', ['draw' => $draw]), 'deleteUrl' => URL::temporarySignedRoute('organizerPanel.delete', 3600, ['draw' => $draw]), 'fetchStateUrl' => URL::signedRoute('organizerPanel.fetchState', ['draw' => $draw]) ]) }}')"/>
        </template>
    </vue-fetcher>
@stop