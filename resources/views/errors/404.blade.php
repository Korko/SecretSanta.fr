@extends('templates/layout')

@section('header')
    @parent

    @vite('resources/sass/404.scss')
@show

@section('content')
    <div class="polaroid-wrapper">

        <div class="item">
            <div class="polaroid">
                <div class="caption"></div>
            </div>
            <p class="wasted">Wasted</p>
        </div>

    </div>
    <div class="error-content">

        <h1 class="section-title text-center">@lang('404.title')</h1>
        <p class="lead text-center">@lang('404.subtitle')</p>

        <p class="back text-center"><a href="/" class="btn btn-primary">@lang('404.back')</a></p>

    </div>
@stop