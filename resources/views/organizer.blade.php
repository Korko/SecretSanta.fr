@extends('templates/layout')

@section('navbar-right')
    <li><a href="{{ route('form.index') }}">@lang('common.nav.go')</a></li>
    <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a></li>
@stop

@section('content')
    <vue-fetcher fetch="{{ URL::signedRoute('organizer.fetch', ['draw' => $draw]) }}">
        <template v-slot:default="slotProps">
            <organizer-form v-bind="slotProps.data" :routes="JSON.parse('{{ json_encode([
                'csvInitUrl' => URL::signedRoute('organizer.csvInit', ['draw' => $draw]),
                'csvFinalUrl' => URL::signedRoute('organizer.csvFinal', ['draw' => $draw]),
                'deleteUrl' => URL::temporarySignedRoute('organizer.delete', 3600, ['draw' => $draw])
            ]) }}')"/>
        </template>
    </vue-fetcher>
@stop