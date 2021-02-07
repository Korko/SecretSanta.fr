@extends('templates/layout', ['styles' => ['/css/404.css']])

@section('content')
    <div class="polaroid-wrapper">

        <div class="item">
            <div class="polaroid">
                <img src="/images/404-1.jpg">
                <div class="caption"></div>
            </div>
            <p class="wasted">Wasted</p>
        </div>

    </div>
    <div class="error-content">

        <h1 class="section-title text-center">@lang('404.title')</h1>
        @hasSection('subtitle')<p class="lead text-center">@yield('subtitle')</p>@endif

        <p class="back text-center"><a href="/" class="btn btn-primary">Revenir sur la page d'accueil</a></p>

    </div>
@stop