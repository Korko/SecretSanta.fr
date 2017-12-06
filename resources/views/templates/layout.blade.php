<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="{{ App::getLocale() }}"> <!--<![endif]-->
<head>
    @section('header')
        <title>@lang('headers.title')</title>

        <link rel="canonical" href="{{ URL::to('/') }}">

        <!-- meta -->
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
        <meta name="description" content="@lang('headers.description')">
        <meta name="keywords" content="@lang('headers.keywords')">
        <meta name="author" content="Korko <webmaster@secretsanta.fr>">

        <!-- opengraph/facebook -->
        <meta property="og:title" content="SecretSanta">
        <meta property="og:site_name" content="SecretSanta">
        <meta property="og:url" content="{{ URL::to('/') }}">
        <meta property="og:image" content="{{ URL::asset('/assets/images/logo_black.png') }}">
        <meta property="og:description" content="@lang('headers.description')">
        <meta property="og:type" content="website">
        <meta property="og:locale" content="{{ App::getLocale() }}">

        <!-- twitter -->
        <meta name="twitter:card" content="summary">
        <meta name="twitter:site" content="@korkof">
        <meta name="twitter:creator" content="Korko">
        <meta name="twitter:title" content="SecretSanta">
        <meta name="twitter:description" content="@lang('headers.description')">
        <meta name="twitter:image" content="{{ URL::asset('/assets/images/logo_black.png') }}">

        <!-- facebook image -->
        <link rel="image_src" href="{{ URL::asset('/assets/images/logo_black.png') }}" />

        <!-- css -->
        @php
            $styles = (array) (isset($styles) ? $styles : '/assets/layout.css');
        @endphp
        @foreach($styles as $style)
            <link rel="stylesheet" href="{{ URL::asset($style) }}" />
        @endforeach
    @show
</head>
<body data-spy="scroll" data-target="#navbar" data-offset="120">
    <div id="wrap">
    <div id="main">
        <div id="menu" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
            <div class="container">
                <nav id="navbar">
                    <div id="logo">
                        <a href="#header">
                            <img src="{{ URL::asset('/assets/images/logo.png') }}" alt="" />
                        </a>
                    </div>
                    <ul class="nav navbar-nav hidden-xs">
                        @yield('navbar')

                        <!--fix for scroll spy active menu element-->
                        <li style="display:none;"><a href="#header"></a></li>
                    </ul>
                    <ul class="visible-xs list-inline">
                        @foreach(config('app.domains') as $locale => $domain)
                            <li><a href="https://{{ $domain }}"><span class="lang-lg" lang="{{ $locale }}"></span></a></li>
                        @endforeach
                    </ul>
                </nav><!--/.navbar-collapse -->
            </div><!-- container -->
        </div><!-- menu -->

        @section('body')
            <div>
                @yield('content')
            </div>
        @show
    </div>
    </div>

    <footer id="footer" class="dark-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <div class="row">
                <div class="col-sm-6">
                    @section('copyright')
                        &copy; Copyright SecretSanta.fr 2015
                        <br/>{!! trans('footer.project', ['author' => '<a class="themeBy" href="https://www.korko.fr">Korko</a>']) !!}
                        <br/>{!! trans('footer.theme', ['author' => '<a class="themeBy" href="https://www.themewagon.com">ThemeWagon</a>']) !!}
                    @show
                </div>
            </div>
        </div>
        <!-- /.container -->
    </footer>

    <span id="forkongithub" class="hidden-sm"><a href="https://framagit.org/Korko/SecretSanta" title="Fork me on GitLab">Fork me on GitLab<br/>Current version {{ $version }}</a></span>

    @yield('script')
</body>
</html>

