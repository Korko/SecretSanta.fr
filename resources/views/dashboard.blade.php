@extends('templates/layout')

@section('navbar-right')
    <li><a href="{{ route('form.view') }}">@lang('common.nav.go')</a></li>
    <li><a href="{{ route('faq') }}">@lang('common.nav.faq')</a></li>
@stop

@section('content')
    <dashboard />
@stop