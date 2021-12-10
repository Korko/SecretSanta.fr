@extends('templates/layout')

@section('header')
    @parent

    @javascript([
        'alert' => session('message', ''),
    ])
@stop

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
        <li><a href="#what">@lang('form.nav.what')</a></li>
        <li><a href="#how">@lang('form.nav.how')</a></li>
        <li><a href="#form">@lang('form.nav.go')</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
        <li><a href="{{ route('dashboard') }}" target="_blank">@lang('common.nav.dashboard')</a></li>
        <li><a class="d-md-none" href="{{ route('faq') }}" target="_blank">@lang('common.nav.faq')</a></li>
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
                    <li class="media media-icon-left media-calendar-icon">
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.what.heading1')</h4>
                            <p>{!! nl2br(trans('form.section.what.content1')) !!}</p>
                        </div>
                    </li>
                </ul>
                <div class="card w-100">
                    <div class="bg-light card-body">
                        <h6 class="card-title">@lang('form.fyi')</h6>
                        <p class="card-text">{!! nl2br(trans('form.section.what.notice', ['button' => str_replace("\n", '', view('partials.bmcButton'))])) !!}</p>
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
                    <li class="media media-icon-left media-user-icon">
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading1')</h4>
                            <p>{!! nl2br(trans('form.section.how.content1')) !!}</p>
                        </div>
                    </li>
                    <li class="media media-icon-right media-paper-icon">
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading2')</h4>
                            <p>{!! nl2br(trans('form.section.how.content2')) !!}</p>
                        </div>
                    </li>
                    <li class="media media-icon-left media-mail-icon">
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading3')</h4>
                            <p>{!! nl2br(trans('form.section.how.content3')) !!}</p>
                        </div>
                    </li>
                    <li class="media media-icon-right media-clock-icon">
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading4')</h4>
                            <p>{!! nl2br(trans('form.section.how.content4')) !!}</p>
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