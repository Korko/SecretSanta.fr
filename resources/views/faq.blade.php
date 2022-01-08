@extends('templates/layout')

@section('navbar-right')
    <li><a href="/">@lang('faq.nav.go')</a></li>
    <li><a href="mailto:{{ config('app.email') }}">@lang('faq.nav.contact')</a></li>
    <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a></li>
@stop

@section('content')
    <faq :questions="{{ Illuminate\Support\Js::from($questions) }}" />
@stop