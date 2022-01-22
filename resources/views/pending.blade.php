@extends('templates/layout')

@section('navbar-right')
    <li><a href="{{ route('form.index') }}">@lang('common.nav.go')</a></li>
    <li><a href="{{ route('dashboard') }}">@lang('common.nav.dashboard')</a></li>
@stop

@section('content')
    <pending :draw="{{ json_encode($draw) }}"></pending>
@stop