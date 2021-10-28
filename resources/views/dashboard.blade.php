@extends('templates/layout')

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
        <li><a href="/">@lang('common.nav.go')</a></li>
        <li><a class="d-md-none" href="{{ route('faq') }}" target="_blank">@lang('common.nav.faq')</a></li>
    </ul>
@stop

@section('content')
    <dashboard />
@stop

@section('script')
    @parent

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/app.js') }}"></script>
@stop