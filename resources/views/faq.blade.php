@extends('templates/layout')

@section('script')
    @parent

    @vite('resources/js/common.js')
    @vite('resources/js/faq.js')
@stop

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
        <li><a href="/">@lang('faq.nav.go')</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
        <li><a href="mailto:{{ config('app.email') }}">@lang('faq.nav.contact')</a></li>
    </ul>
@stop

@section('content')
    <faq :questions="{{ Illuminate\Support\Js::from($questions) }}" />
@stop
