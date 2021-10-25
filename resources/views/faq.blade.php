@extends('templates/layout')

@section('script')
    @parent

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/faq.js') }}"></script>
@stop

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
        <li><a href="/">@lang('faq.nav.go')</a></li>
        <li><a href="mailto:{{ config('app.email') }}">@lang('faq.nav.contact')</a></li>
        <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a>>@lang('faq.nav.contact')</a></li>
    </ul>
@stop

@section('content')
    <faq :questions="{{ json_encode($questions) }}" />
@stop
