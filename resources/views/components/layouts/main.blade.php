<!DOCTYPE html>
<html lang="{{ App::getLocale() }}">
    <head>
        <link rel="canonical" href="{{ url('/') }}">

        <!-- meta -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta http-equiv="content-language" content="{{ App::getLocale() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
        <meta name="description" content="@lang('headers.description')">
        <meta name="keywords" content="@lang('headers.keywords')">
        <meta name="author" content="Korko <webmaster@secretsanta.fr>">
        <meta name=”robots” content="index, follow">
        <meta name="rating" content="safe for kids">

        <!-- opengraph/facebook -->
        <meta property="og:title" content="SecretSanta.fr">
        <meta property="og:site_name" content="SecretSanta.fr">
        <meta property="og:url" content="{{ url('/') }}">
        <meta property="og:image" content="{{ url('/images/opengraph_banner_19.1_1.png') }}">
        <meta property="og:image:url" content="{{ url('/images/opengraph_banner_19.1_1.png') }}">
        <meta property="og:image:alt" content="{{ url('/images/santaclaus.png') }}">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:description" content="@lang('headers.description')">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="{{ App::getLocale() }}">

        <!-- twitter -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:site" content="@korkof">
        <meta name="twitter:creator" content="@korkof">
        <meta name="twitter:title" content="SecretSanta.fr">
        <meta name="twitter:description" content="@lang('headers.description')">
        <meta name="twitter:image" content="{{ url('/images/opengraph_banner_16_9.png') }}">
        <meta name="twitter:image" content="@lang('headers.logo_alt')">
        <meta name="twitter:url" content="{{ url('/') }}">

        <!-- facebook image -->
        <link rel="image_src" href="{{ url('/images/opengraph_banner_19.1_1.png') }}" />

        @routes

        @vite('resources/sass/app.scss')
    </head>
    <body>
        <div id="content">
            <div>
                {{ $slot }}
            </div>
        </div>

        <noscript>
            {{ trans('app.nojs') }}
        </noscript>

        @vite('resources/js/app.js')

        @javascript([
            'pusher' => [
                'key' => Arr::get(config('websockets.apps'), '0.key'),
                'host' => Arr::get(config('websockets.apps'), '0.host'),
                'port' => intval(Arr::get(config('websockets.apps'), '0.port', 443)),
            ]
        ])
    </body>
</html>
