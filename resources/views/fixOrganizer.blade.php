@extends('templates/layout')

@section('script')
    @parent

    @vite('resources/js/common.js')
    @vite('resources/js/fixOrganizer.js')
@stop

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
        <li><a href="/">@lang('faq.nav.go')</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
    </ul>
@stop

@section('content')
    <fix-organizer-form action="{{ route('fixOrganizer.handle') }}" />
@stop
