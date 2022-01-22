@extends('templates/layout')

@section('navbar-left')
    <li><a href="#what">@lang('form.nav.what')</a></li>
    <li><a href="#how">@lang('form.nav.how')</a></li>
    <li><a href="#form">@lang('form.nav.go')</a></li>
@stop
@section('navbar-right')
    <li><a href="{{ route('dashboard') }}" target="_blank">@lang('common.nav.dashboard')</a></li>
    <li><a class="d-md-none" href="{{ route('faq') }}" target="_blank">@lang('common.nav.faq')</a></li>
@stop

@section('body')
    <page-random bmc="{{ config('app.bmc') }}" process="{{ route('form.process') }}"></page-random>
@stop