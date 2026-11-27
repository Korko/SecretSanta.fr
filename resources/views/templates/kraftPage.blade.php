{{-- Static content pages (FAQ, legal notice): banner title, then cards --}}
@extends('templates/kraft')

@section('skip')
    <a class="lien-evitement" href="#contenu">@lang('home.skip_content')</a>
@stop

@section('nav')
    <a class="kt-navtext" href="{{ route('faq') }}">@lang('home.nav.faq')</a>
    <span class="tag-wrap tag-wrap--kraft"><a class="tag-kraft" href="{{ url('/') }}#form" style="--hole: var(--color-creme-clair)">@lang('home.nav.go')</a></span>
@stop

@section('menu')
    <a href="{{ route('faq') }}">@lang('home.nav.faq')</a>
    <a href="{{ url('/') }}#form">@lang('home.nav.go')</a>
@stop

@section('body')
    <div class="pg-page">
        <section class="pg-banner">
            <div class="kt-veil" aria-hidden="true"></div>
            <div class="pg-banner-in">
                <div class="pg-banner-text">
                    <h1 class="pg-h1">@yield('title')</h1>
                </div>
            </div>
        </section>
        <div id="contenu" class="pg-main" tabindex="-1">
            <div class="pg-doc">
                @yield('content')
            </div>
        </div>
    </div>
@stop

@section('script')
    @parent
    @vite('resources/js/kraftMenu.js')
@stop
