@extends('templates/layout')

@section('navbar-right')
    <li><a href="{{ route('form.index') }}">@lang('common.nav.go')</a></li>
    <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a></li>
@stop

@section('content')
    <vue-fetcher fetch="{{ URL::signedRoute('santa.fetch', ['participant' => $participant]) }}">
        <template v-slot:default="slotProps">
            <dear-santa-form v-bind="slotProps.data" :routes="JSON.parse('{{ json_encode([
                'contactUrl' => URL::signedRoute('santa.contact', ['participant' => $participant]),
                'subUrl' => URL::signedRoute('santa.sub', ['participant' => $participant]),
                'unsubUrl' => URL::signedRoute('santa.unsub', ['participant' => $participant])
            ]) }}')"/>
        </template>
    </vue-fetcher>
@stop