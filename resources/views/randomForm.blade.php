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
    <meta property="og:image" content="{{ URL::asset('media/img/logo_black.png') }}">
    <meta property="og:description" content="@lang('headers.description')">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="{{ App::getLocale() }}">

    <!-- twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@korkof">
    <meta name="twitter:creator" content="Korko">
    <meta name="twitter:title" content="SecretSanta">
    <meta name="twitter:description" content="@lang('headers.description')">
    <meta name="twitter:image" content="{{ URL::asset('media/img/logo_black.png') }}">

    <!-- facebook image -->
    <link rel="image_src" href="{{ URL::asset('media/img/logo_black.png') }}" />

    <!-- css -->
    <link rel="stylesheet" href="media/css/bootstrap.min.css" />
    <link rel="stylesheet" href="media/css/bootstrap-theme.min.css" />
    <link rel="stylesheet" href="media/css/font-awesome.min.css" />
    <link rel="stylesheet" href="media/css/alertify.core.css" />
    <link rel="stylesheet" href="media/css/alertify.default.css" />
    <link rel="stylesheet" href="media/css/alertify.bootstrap.css" />
    <link rel="stylesheet" href="media/css/main.css" />

    <!-- google font -->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Kreon:300,400,700' />

    <!-- js -->
    <script src="media/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>
</head>
<body data-spy="scroll" data-target="#navbar" data-offset="120" >
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <div id="menu" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
        <div id="navbar">
            <div id="logo">
                <a href="#header">
                    <img src="media/img/logo.png" alt="" />
                </a>
            </div>
            <ul class="nav navbar-nav hidden-xs">
                <li><a href="#what">@lang('form.nav.what')</a></li>
                <li><a href="#how">@lang('form.nav.how')</a></li>
                <li><a href="#form">@lang('form.nav.go')</a></li>

                <!--fix for scroll spy active menu element-->
                <li style="display:none;"><a href="#header"></a></li>
            </ul>
        </div><!--/.navbar-collapse -->
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
                            <img class="media-object" src="media/img/calendar-icon.png" />
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
                            <img class="media-object" src="media/img/user-icon.png" />
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
                            <img class="media-object" src="media/img/paper-icon.png" />
                        </div>
                    </li>
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="media/img/mail-icon.png" />
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
                <div id="success-wrapper" class="alert alert-success" style="display: none">
                    @lang('form.success')
                </div>

                <div id="errors-wrapper" class="alert alert-danger" style="display: none">
                    <ul id="errors">
                    </ul>
                </div>

                <form action="{{ url('/') }}" method="post" autocomplete="off">
                    <fieldset>
                        <legend>@lang('form.participants')</legend>
                        <div class="table-responsive form-group">
                            <table id="participants" class="table table-hover table-numbered">
                                <thead>
                                    <tr>
                                        <th class="col-xs-3">@lang('form.participant.name')</th>
                                        <th class="col-xs-3">@lang('form.participant.email')</th>
                                        <th class="col-xs-0"></th>
                                        <th class="col-xs-2 col-lg-1">@lang('form.participant.phone')</th>
                                        <th class="col-xs-2">@lang('form.participant.partner')</th>
                                        <th class="col-xs-1 col-lg-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- Default is two empty rows to have two entries at any time --}}
                                    @foreach(old('name', ['', '']) as $idx => $name)
                                    <tr class="participant">
                                        <td class="row">
                                            <label>
                                                <div class="input-group">
                                                    <span class="input-group-addon counter">{{ $idx+1 }}</span>
                                                    <input type="text" name="name[]" required="required" placeholder="@lang('form.name.placeholder')" value="{{ $name }}" class="form-control participant-name" />
                                                </div>
                                            </label>
                                        </td>
                                        <td class="row border-left">
                                            <input type="email" name="email[]" placeholder="@lang('form.email.placeholder')" value="{{ array_get(old('email', []), $idx) }}" class="form-control participant-email" />
                                        </td>
                                        <td>
                                            et/ou
                                        </td>
                                        <td class="row border-right">
                                            <input type='tel' pattern='0[67]\d{8}' maxlength="10" name="phone[]" placeholder="@lang('form.phone.placeholder')" value="{{ array_get(old('phone', []), $idx) }}" class="form-control participant-phone" />
                                        </td>
                                        <td class="row">
                                            <select name="partner[]" class="form-control participant-partner">
                                                <option value="" {{ !array_get(old('partner'), $idx) ? 'selected="selected"' : '' }}>@lang('form.partner.none')</option>
                                                @foreach(array_diff_key(old('name', []), [$idx => false]) as $idx2 => $name)
                                                    {{ $selected = (array_get(old('partner'), $idx) !== '' && intval(array_get(old('partner'), $idx)) === $idx2) }}
                                                    <option value="{{ $idx2 }}" {{ $selected ? 'selected="selected"' : '' }}>{{ ($idx2+1).". ".$name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="row participant-remove-wrapper">
                                            <button type="button" class="btn btn-danger participant-remove"><span class="glyphicon glyphicon-minus"></span><span> @lang('form.partner.remove')</span></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success participant-add"><span class="glyphicon glyphicon-plus"></span> @lang('form.partner.add')</button>
                        </div>

                        <div class="row" id="contact">
                            <fieldset id="form-mail-group" class="col-md-6">
                                <div class="form-group">
                                    <label for="mailTitle">@lang('form.mail.title')</label>
                                    <input id="mailTitle" type="text" name="title" required="required" placeholder="@lang('form.mail.title.placeholder')" value="{{ old('title') }}" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="mailContent">@lang('form.mail.content')</label>
                                    <textarea id="mailContent" name="contentMail" required="required" placeholder="@lang('form.mail.content.placeholder')" class="form-control" rows="3">{{ old('contentMail') }}</textarea>
                                    <p class="help-block">@lang('form.mail.content.tip1')</p>
                                    <p class="help-block">@lang('form.mail.content.tip2')</p>
                                </div>
                            </fieldset>

                            <fieldset id="form-sms-group" class="col-md-6">
                                <div class="form-group">
                                    <label for="smsContent">@lang('form.sms.content')</label>
                                    <textarea id="smsContent" name="contentSMS" required="required" placeholder="@lang('form.sms.content.placeholder')" class="form-control" rows="3" maxlength="130">{{ old('contentSMS') }}</textarea>
                                    <p class="help-block">@lang('form.sms.content.tip1')</p>
                                    <p class="help-block">@lang('form.sms.content.tip2')</p>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-group btn">
                            {!! Recaptcha::render() !!}
                        </div>

                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary btn-lg">@lang('form.submit')</button>
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

    <script type="text/javascript" src="media/js/jquery-2.1.3.min.js"></script>
    <script type="text/javascript" src="media/js/jquery.actual.min.js"></script>
    <script type="text/javascript" src="media/js/jquery.scrollTo.min.js"></script>
    <script type="text/javascript" src="media/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="media/js/main.js"></script>
    <script type="text/javascript" src="media/js/randomForm.js"></script>
    <script type="text/javascript" src="media/js/alertify.min.js"></script>

    @if(Session::has('message'))
    <script type="text/javascript">
        alertify.alert("{{ session('message') }}");
    </script>
    @endif

    <!-- Piwik -->
    <script type="text/javascript">
      var _paq = _paq || [];
      _paq.push(["setDocumentTitle", document.domain + "/" + document.title]);
      _paq.push(["setDomains", ["*.secretsanta.fr"]]);
      _paq.push(['trackPageView']);
      _paq.push(['enableLinkTracking']);
      (function() {
        var u="//k0rko.fr/piwik/";
        _paq.push(['setTrackerUrl', u+'piwik.php']);
        _paq.push(['setSiteId', 3]);
        var d=document, g=d.createElement('script'), s=d.getElementsByTagName('script')[0];
        g.type='text/javascript'; g.async=true; g.defer=true; g.src=u+'piwik.js'; s.parentNode.insertBefore(g,s);
      })();
    </script>
    <noscript><p><img src="//k0rko.fr/piwik/piwik.php?idsite=3" style="border:0;" alt="" /></p></noscript>
    <!-- End Piwik Code -->
</body>
</html>
