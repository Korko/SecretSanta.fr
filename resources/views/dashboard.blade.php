@extends('templates/layout')

@section('navbar-right')
    <li><a href="/">@lang('common.nav.go')</a></li>
    <li><a class="d-md-none" href="{{ route('faq') }}" target="_blank">@lang('common.nav.faq')</a></li>
@stop

@section('content')
    <dashboard />
@stop