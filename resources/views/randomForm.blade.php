@extends('templates/layout', ['styles' => '/css/randomForm.css'])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'alert' => session('message', ''),
        'api'   => config('captcha.sitekey'),
    ])
@stop

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
        <li><a href="#what">@lang('form.nav.what')</a></li>
        <li><a href="#how">@lang('form.nav.how')</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
        <li><a href="#form">@lang('form.nav.go')</a></li>
        <li><a class="d-md-none" href="{{ route('faq') }}" target="_blank">@lang('form.nav.faq')</a></li>
    </ul>
@stop

@section('body')
    <div id="header">
        <div class="bg-overlay"></div>
        <div class="center text-center">
            <div class="banner">
                <h1>@lang('form.title')</h1>
            </div>
            <div class="subtitle">
                <h4>@lang('form.subtitle')</h4>
            </div>
        </div>
        <div class="bottom text-center">
            <a id="scrollDownArrow" href="#"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>

    <div id="what" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">@lang('form.section.what.title')</h2>
            <p class="lead main text-center">@lang('form.section.what.subtitle')</p>
            <div class="row text-center what">
                <ul class="media-list w-100">
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="/images/calendar-icon.png" />
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.what.heading1')</h4>
                            <p>{!! nl2br(trans('form.section.what.content1')) !!}</p>
                        </div>
                    </li>
                </ul>
                <div class="card w-100">
                    <div class="bg-light card-body">
                        <h6 class="card-title">@lang('form.fyi')</h6>
                        <p class="card-text">{!! nl2br(trans('form.section.what.notice', ['button' => str_replace("\n", '', view('partials.paypalButton'))])) !!}</p>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!-- #what -->

    <div class="parallax">
        <div class="container inner"></div>
    </div>

    <div id="how" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">@lang('form.section.how.title')</h2>
            <p class="lead main text-center">@lang('form.section.how.subtitle')</p>
            <div class="row text-center how">
                <ul class="media-list w-100">
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="/images/user-icon.png" />
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading1')</h4>
                            <p>{!! nl2br(trans('form.section.how.content1')) !!}</p>
                        </div>
                    </li>
                    <li class="media">
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading2')</h4>
                            <p>{!! nl2br(trans('form.section.how.content2')) !!}</p>
                        </div>
                        <div class="media-right media-middle">
                            <img class="media-object" src="/images/paper-icon.png" />
                        </div>
                    </li>
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="/images/mail-icon.png" />
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading3')</h4>
                            <p>{!! nl2br(trans('form.section.how.content3')) !!}</p>
                        </div>
                    </li>
                    <li class="media">
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading4')</h4>
                            <p>{!! nl2br(trans('form.section.how.content4')) !!}</p>
                        </div>
                        <div class="media-right media-middle">
                            <img class="media-object" src="/images/clock-icon.png" />
                        </div>
                    </li>
                </ul>
                <div class="card w-100">
                    <div class="bg-light card-body">
                        <h6 class="card-title">@lang('form.fyi')</h6>
                        <p class="card-text">{!! nl2br(trans('form.section.how.notice', ['link' => '<a href="https://github.com/Korko/SecretSanta">GitHub</a>'])) !!}</p>
                    </div>
                </div>
            </div>
        </div><!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!--/#how-->

    <div class="parallax parallax2">
        <div class="container inner"></div>
    </div>

    <div id="form" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">@lang('form.section.go.title')</h2>
            <p class="lead main text-center">@lang('form.section.go.subtitle')</p>
            <random-form id="randomForm" action="/"></random-form>
        </div><!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div>
@stop

@section('copyright')
    @parent

    <br />{!! trans('footer.icons', ['author' => '<a class="themeBy" href="https://www.iconfinder.com/iconsets/doublejdesign-free-icon-handy_color">Double-J Designs</a>']) !!}
@stop

@section('script')
    @parent

    <script type="text/javascript" src="{{ mix('/js/common.js') }}"></script>
    <script type="text/javascript" src="{{ mix('/js/randomForm.js') }}"></script>
@stop
