{{-- Participant and organizer pages: their content comes from a signed URL (the secret is in location.hash) --}}
@extends('templates/kraft')

@section('skip')
    <a class="lien-evitement" href="#form">@lang('home.skip_content')</a>
@stop

@section('nav')
    <a class="kt-navtext" href="{{ route('faq') }}">@lang('home.nav.faq')</a>
    <span class="tag-wrap tag-wrap--kraft"><a class="tag-kraft" href="{{ url('/') }}" style="--hole: var(--color-creme-clair)">@lang('home.nav.another')</a></span>
@stop

@section('menu')
    <a href="{{ route('faq') }}">@lang('home.nav.faq')</a>
    <a href="{{ url('/') }}">@lang('home.nav.another')</a>
@stop

@section('body')
    <div id="form" class="pg-page" data-url="{{ $fetchUrl }}" tabindex="-1">
        <p class="k-attente">@lang('home.loading')</p>
    </div>
@stop
