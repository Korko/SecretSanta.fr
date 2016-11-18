<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="{{ App::getLocale() }}"> <!--<![endif]-->
<head>
    <title>Secret Santa</title>

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
    <meta property="og:image" content="{{ URL::asset('img/logo_black.png') }}">
    <meta property="og:description" content="@lang('headers.description')">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ App::getLocale() }}">

    <!-- twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@korkof">
    <meta name="twitter:creator" content="Korko">
    <meta name="twitter:title" content="SecretSanta">
    <meta name="twitter:description" content="@lang('headers.description')">
    <meta name="twitter:image" content="{{ URL::asset('img/logo_black.png') }}">

    <!-- facebook image -->
    <link rel="image_src" href="{{ URL::asset('img/logo_black.png') }}" />

    <!-- css -->
    <link rel="stylesheet" href="css/app.css" />

    <!-- google font -->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Kreon:300,400,700' />
</head>
<body data-spy="scroll" data-target="#navbar" data-offset="120">
    <div id="menu" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
        <nav id="navbar">
            <div id="logo">
                <a href="#header">
                    <img src="img/logo.png" alt="" />
                </a>
            </div>
            <ul class="nav navbar-nav hidden-xs">
                <li><a href="#what">@lang('form.nav.what')</a></li>
                <li><a href="#how">@lang('form.nav.how')</a></li>
                <li><a href="#form">@lang('form.nav.go')</a></li>

                <li class="btn-group btn-group-xs dropdown">
                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                        <span class="lang-xs lang-lbl" lang="{{ App::getLocale() }}"></span> <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu" role="menu">
                        @foreach(array_diff_key(config('app.domains'), [App::getLocale() => '']) as $locale => $domain)
                            <li><a href="https://{{ $domain }}"><span class="lang-sm lang-lbl" lang="{{ $locale }}"></span></a></li>
                        @endforeach
                    </ul>
                </li>

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

    <div id="header">
        <div class="bg-overlay"></div>
        <div class="center text-center">
            <div class="banner">
                <h1 class="">Secret Santa</h1>
            </div>
            <div class="subtitle"><h4>@lang('form.subtitle')</h4></div>
        </div>
        <div class="bottom text-center">
            <a id="scrollDownArrow" href="#"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>
    <!-- /#header -->

    <div id="what" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">@lang('form.section.what.title')</h2>
            <p class="lead main text-center">@lang('form.section.what.subtitle')</p>
            <div class="row text-center what">
                <ul class="media-list">
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="img/calendar-icon.png" />
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.what.heading1')</h4>
                            <p>{!! nl2br(trans('form.section.what.content1')) !!}</p>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
        <!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!-- #what -->

    <div class="parallax">
        <div class="container inner">

        </div>
        <!-- /.container -->
    </div><!-- #spacer1 -->


    <div id="how" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">@lang('form.section.how.title')</h2>
            <p class="lead main text-center">@lang('form.section.how.subtitle')</p>
            <div class="row text-center how">
                <ul class="media-list">
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="img/user-icon.png" />
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
                            <img class="media-object" src="img/paper-icon.png" />
                        </div>
                    </li>
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="img/mail-icon.png" />
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">@lang('form.section.how.heading3')</h4>
                            <p>{!! nl2br(trans('form.section.how.content3')) !!}</p>
                        </div>
                    </li>
                </ul>
                <div class="well well-lg">{!! trans('form.section.how.notice', ['link' => '<a href="https://github.com/Korko/SecretSanta">GitHub</a>']) !!}</div>
            </div>
        </div>
        <!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!--/#how-->

    <div class="parallax parallax2">
        <div class="container inner">

        </div>
        <!-- /.container -->
    </div><!-- #spacer1 -->

    <div id="form" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">@lang('form.section.go.title')</h2>
            <p class="lead main text-center">@lang('form.section.go.subtitle')</p>
            <div class="row text-center form">
                <div id="success-wrapper" class="alert alert-success" v-show="sent">
                    @lang('form.success')
                </div>

                <div id="errors-wrapper" class="alert alert-danger" v-show="errors.length && !sent">
                    <ul id="errors">
                        <li v-for="error in errors">@{{ error }}</li>
                    </ul>
                </div>

                <form action="{{ url('/') }}" @submit.prevent="submit" method="post" autocomplete="off">
                    <fieldset :disabled="sending || sent">
                        <legend>@lang('form.participants')</legend>
                        <div class="table-responsive form-group">
                            <table id="participants" class="table table-hover table-numbered">
                                <thead>
                                    <tr>
                                        <th class="col-xs-3">@lang('form.participant.name')</th>
                                        <th class="col-xs-3">@lang('form.participant.email')</th>
                                        <th class="col-xs-0"></th>
                                        <th class="col-xs-2 col-lg-2">@lang('form.participant.phone')</th>
                                        <th class="col-xs-2">@lang('form.participant.partner')</th>
                                        <th class="col-xs-1 col-lg-1"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Default is two empty rows to have two entries at any time --}}
                                    <tr class="participant" v-for="(participant, idx) in participants">
                                        <td class="row">
                                            <label>
                                                <div class="input-group">
                                                    <span class="input-group-addon counter">@{{ idx+1 }}</span>
                                                    <input type="text" name="name[]" required="required" placeholder="@lang('form.name.placeholder')" v-model="participant.name" class="form-control participant-name" />
                                                </div>
                                            </label>
                                        </td>
                                        <td class="row border-left">
                                            <input type="email" name="email[]" placeholder="@lang('form.email.placeholder')" v-model="participant.email" class="form-control participant-email" :required="!participant.phone" />
                                        </td>
                                        <td>
                                            @lang('form.mail-sms')
                                        </td>
                                        <td class="row border-right">
                                            <input type='tel' pattern='0[67]\d{8}' maxlength="10" name="phone[]" placeholder="@lang('form.phone.placeholder')" v-model="participant.phone" class="form-control participant-phone" :required="!participant.email" />
                                        </td>
                                        <td class="row">
                                            <select name="partner[]" class="form-control participant-partner" v-model="participant.partner">
                                                <option value="-1">@lang('form.partner.none')</option>
                                                <option v-for="(partner, pidx) in participants" v-if="partner.name && idx !== pidx" :value="pidx">@{{ partner.name }}</option>
                                            </select>
                                        </td>
                                        <td class="row participant-remove-wrapper">
                                            <button type="button" class="btn btn-danger participant-remove" :disabled="idx < 2" @click="removeParticipant(idx)"><span class="glyphicon glyphicon-minus"></span><span> @lang('form.partner.remove')</span></button>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success participant-add" @click="addParticipant()"><span class="glyphicon glyphicon-plus"></span> @lang('form.partner.add')</button>
                        </div>

                        <div class="row" id="contact">
                            <fieldset id="form-mail-group" class="col-md-6" :disabled="!this.emailUsed">
                                <div class="form-group">
                                    <label for="mailTitle">@lang('form.mail.title')</label>
                                    <input id="mailTitle" type="text" name="title" :required="this.emailUsed" placeholder="@lang('form.mail.title.placeholder')" value="{{ old('title') }}" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="mailContent">@lang('form.mail.content')</label>
                                    <textarea id="mailContent" name="contentMail" :required="this.emailUsed" placeholder="@lang('form.mail.content.placeholder')" class="form-control" rows="3">{{ old('contentMail') }}</textarea>
                                    <p class="help-block">@lang('form.mail.content.tip1')</p>
                                    <p class="help-block">@lang('form.mail.content.tip2')</p>
                                </div>
                            </fieldset>

                            <fieldset id="form-sms-group" class="col-md-6" :disabled="!this.phoneUsed">
                                <div class="form-group">
                                    <label for="smsContent">@lang('form.sms.content')</label>
                                    <textarea id="smsContent" name="contentSMS" :required="this.phoneUsed" placeholder="@lang('form.sms.content.placeholder')" class="form-control" rows="3" maxlength="130">{{ old('contentSMS') }}</textarea>
                                    <p class="help-block">@lang('form.sms.content.tip1')</p>
                                    <p class="help-block">@lang('form.sms.content.tip2')</p>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-group btn">
                            {!! Recaptcha::renderElement(['data-theme' => 'light']) !!}
                        </div>

                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary btn-lg">
				<span v-if="sending"><span class="glyphicon glyphicon-refresh spinning"></span> @lang('form.sending')</span>
				<span v-else>@lang('form.submit')</span>
			</button>
                    </fieldset>
                </form>

            </div>
            <!-- /.services -->
        </div>
        <!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div>

    <footer id="footer" class="dark-wrapper">
        <div class="container inner">
            <div class="row">
                <div class="col-sm-6">
                    &copy; Copyright SecretSanta.fr 2015
                    <br/>{!! trans('footer.project', ['author' => '<a class="themeBy" href="https://www.korko.fr">Korko</a>']) !!}
                    <br/>{!! trans('footer.theme', ['author' => '<a class="themeBy" href="https://www.themewagon.com">ThemeWagon</a>']) !!}
                    <br/>{!! trans('footer.icons', ['author' => '<a class="themeBy" href="https://www.iconfinder.com/iconsets/doublejdesign-free-icon-handy_color">Double-J Designs</a>']) !!}
                </div>
            </div>
        </div>
        <!-- /.container -->
    </footer>

    <span id="forkongithub" class="hidden-sm"><a href="https://github.com/Korko/SecretSanta" title="Fork me on GitHub">Fork me on GitHub</a></span>

    <script type="text/javascript" src="js/bundle.js"></script>

    {!! Recaptcha::renderScript(App::getLocale()) !!}

    @if(Session::has('message'))
    <script type="text/javascript">
        alertify.alert("{{ session('message') }}");
    </script>
    @endif
</body>
</html>
