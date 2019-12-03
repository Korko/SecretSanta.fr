@extends('templates/layout', ['styles' => '/css/randomForm.css'])

@section('header')
    @parent

    <!-- config js -->
    @javascript([
        'maxSms' => config('sms.max'),
        'now'    => time(),
        'lang'   => App::getLocale(),
        'alert'  => session('message', '')
    ])
@stop

@section('navbar')
    <ul class="nav navbar-nav navbar-nav-left">
        <li><a href="#what">@lang('form.nav.what')</a></li>
        <li><a href="#how">@lang('form.nav.how')</a></li>
    </ul>
    <ul class="nav navbar-nav navbar-nav-right">
        <li><a href="#form">@lang('form.nav.go')</a></li>
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
                <ul class="media-list">
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
                <div class="card bg-light card-body mb-3 well-lg">{!! nl2br(trans('form.section.what.notice', ['button' => str_replace("\n", '', view('partials.paypalButton'))])) !!}</div>
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
                <ul class="media-list">
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
                </ul>
                <div class="card bg-light card-body mb-3 well-lg">{!! nl2br(trans('form.section.how.notice', ['link' => '<a href="https://github.com/Korko/SecretSanta">GitHub</a>'])) !!}</div>
            </div>
        </div><!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!--/#how-->

    <div class="parallax parallax2">
        <div class="container inner"></div>
    </div>

    @include('templates/participant')
    @include('templates/csv')
    <div id="form" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">@lang('form.section.go.title')</h2>
            <p class="lead main text-center">@lang('form.section.go.subtitle')</p>
            <div class="row text-center form" v-cloak>
                <div id="success-wrapper" class="alert alert-success" v-show="sent">
                    @lang('form.success')
                </div>

                <div id="errors-wrapper" class="alert alert-danger" v-show="errors.length && !sent">
                    <ul id="errors">
                        <li v-for="error in errors">@{{ error }}</li>
                    </ul>
                </div>

                <form id="randomForm" action="/" @submit.prevent="submit" method="post" autocomplete="off">
                    @csrf
                    {{ Form::hidden('thisisatest') }}
                    <fieldset :disabled="sending || sent">
                        <fieldset>
                            <legend>@lang('form.participants')</legend>
                            <div class="table-responsive form-group">
                                <table id="participants" class="table table-hover table-striped table-numbered">
                                    <thead>
                                        <tr>
                                            <th class="col-xl-3" scope="col">@lang('form.participant.name')</th>
                                            <th class="col-xl-3" scope="col">@lang('form.participant.email')</th>
                                            <th class="col-xl-2" scope="col">@lang('form.participant.exclusions')</th>
                                            <th class="col-xl-1" scope="col"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {{-- Default is three empty rows to have three entries at any time --}}
                                        <tr is="participant" v-for="(participant, idx) in participants"
                                            :key="participant.id"
                                            :name="participant.name"
                                            :email="participant.email"
                                            :phone="participant.phone"

                                            @if(!env('SMS_ENABLED')):smsdisabled="true"@endif

                                            :participants="participants"
                                            :dearsanta="dearsanta"
                                            :idx="idx"
                                            @changename="participant.name = $event"
                                            @changeemail="participant.email = $event"
                                            @changephone="participant.phone = $event"
                                            @delete="participants.splice(idx, 1)">
                                        </tr>
                                    </tbody>
                                </table>
                                <button type="button" class="btn btn-success participant-add" @click="addParticipant()"><i class="fas fa-plus"></i> @lang('form.participant.add')</button>
                                <button type="button" class="btn btn-warning participants-import" @click="showModal = true" :disabled="importing">
                                    <span v-if="importing"><i class="fas fa-spinner"></i> @lang('form.participants.importing')</span>
                                    <span v-else><i class="fas fa-list-alt"></i> @lang('form.participants.import')</span>
                                </button>
                            </div>
                        </fieldset>

                        <fieldset>
                            <legend>Messages</legend>
                            <div id="contact">
                                <fieldset id="form-mail-group">
                                    <div class="form-group">
                                        <label for="mailTitle">@lang('form.mail.title')</label>
                                        <input id="mailTitle" type="text" name="title" :required="this.emailUsed" placeholder="@lang('form.mail.title.placeholder')" value="" class="form-control" />
                                    </div>
                                    <div class="form-group">
                                        <label for="mailContent">@lang('form.mail.content')</label>
                                        <textarea id="mailContent" name="content-email" :required="this.emailUsed" placeholder="@lang('form.mail.content.placeholder')" class="form-control" rows="3" v-autosize></textarea>
                                        <textarea id="mailPost" class="form-control" read-only disabled v-if="!dearsanta">@lang('form.mail.post')</textarea>
                                        <textarea id="mailPost" class="form-control extended" read-only disabled v-else>@lang('form.mail.post2')</textarea>

                                        <p class="form-text">@lang('form.mail.content.tip1')</p>
                                        <p class="form-text">@lang('form.mail.content.tip2')</p>
                                    </div>
                                </fieldset>
                            </div>
                        </fieldset>
                        <fieldset>
                            <legend>Options</legend>
                            <div id="form-options" class="form-group">
                                <label><input type="checkbox" name="dearsanta" v-model="dearsanta" value="1" />@lang('form.dearsanta')</label>
                                <p class="tip" role="alert">
                                    <i class="fas fa-exclamation-triangle" aria-hidden="true"></i>
                                    @lang('form.dearsanta.warning')
                                </p>
                                <label><input type="date" name="data-expiration" :min="date | moment(1, 'day')" :max="date | moment(1, 'year')" />@lang('form.data-expiration')</label>
                            </div>
                        </fieldset>
                        <fieldset>
                            <div class="form-group btn">
                                {!! NoCaptcha::display(['data-theme' => 'light']) !!}
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg">
                                <span v-if="sending"><i class="fas fa-spinner"></i> @lang('form.sending')</span>
                                <span v-else-if="sent"><i class="fas fa-check-circle"></i> @lang('form.sent')</span>
                                <span v-else>@lang('form.submit')</span>
                            </button>
                        </fieldset>
                    </fieldset>
                </form>
            </div><!-- /.services -->

            <div id="errors-wrapper" class="alert alert-danger v-rcloak">@lang('form.waiting')</div>
            <csv v-if="showModal" @import="importParticipants" @close="showModal = false"></csv>
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

    {!! NoCaptcha::renderJs(App::getLocale()) !!}
@stop
