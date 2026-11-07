@extends('templates/layout')

@section('content')
    <style>
        .maintenance-santa { display: block; max-width: 200px; margin: 2rem auto; animation: santa-nap 3s ease-in-out infinite; transform-origin: bottom center; }
        @keyframes santa-nap { 50% { transform: rotate(-4deg) translateY(4px); } }
        @media (prefers-reduced-motion: reduce) { .maintenance-santa { animation: none; } }
    </style>
    <div class="error-content">
        <img class="maintenance-santa" src="/images/santaclaus.png" alt="" width="200">
        <h1 class="section-title text-center">@lang('503.title')</h1>
        <p class="lead text-center">@lang('503.subtitle')</p>
    </div>
@stop
