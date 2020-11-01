@extends('templates/layout', ['styles' => ['/css/404.css']])

@section('content')
    <div class="polaroid-wrapper">

        <div class="item">
            <div class="polaroid">
                <img src="/images/404-1.jpg">
                <div class="caption">@lang('404.photo1')</div>
            </div>
            <p class="wasted">Wasted</p>
        </div>

    </div>
    <div class="error-content">

        <h1 class="section-title text-center">Oups, cette page n'existe pas</h1>
        <p class="lead main text-center">Votre évènement SecretSanta est probablement terminé</p>

    </div>
@stop

