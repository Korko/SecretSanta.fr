<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="fr"> <!--<![endif]-->
<head>
    <title>Secret Santa</title>

    <link rel="canonical" href="{{ URL::to('/') }}">

    <!-- meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="description" content="Outils pour aider à organiser un secret-santa">
    <meta name="keywords" content="soiree,organisation,secretsanta,noel,cadeau,secret">
    <meta name="author" content="Korko <webmaster@secretsanta.fr>">

    <!-- opengraph/facebook -->
    <meta property="og:title" content="SecretSanta">
    <meta property="og:site_name" content="SecretSanta">
    <meta property="og:url" content="{{ URL::to('/') }}">
    <meta property="og:image" content="{{ URL::asset('media/img/logo_black.png') }}">
    <meta property="og:description" content="Outils pour aider à organiser un secret-santa">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="fr">

    <!-- twitter -->
    <meta name="twitter:card" content="summary">
    <meta name="twitter:site" content="@korkof">
    <meta name="twitter:creator" content="Korko">
    <meta name="twitter:title" content="SecretSanta">
    <meta name="twitter:description" content="Outils pour aider à organiser un secret-santa">
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
                <li><a href="#what">Qu'est-ce que c'est ?</a></li>
                <li><a href="#how">Comment faire ?</a></li>

                <li><a href="#form">Allez, c'est parti !</a></li>

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
            <div class="subtitle"><h4>Offrez-vous des cadeaux... secrètement !</h4></div>
        </div>
        <div class="bottom text-center">
            <a id="scrollDownArrow" href="#"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>
    <!-- /#header -->

    <div id="what" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">Qu'est-ce que c'est ?</h2>
            <p class="lead main text-center">Description du Secret Santa</p>
            <div class="row text-center what">
                <ul class="media-list">
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="media/img/calendar-icon.png" />
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Le principe</h4>
                            <p>
                                Secret Santa est un moyen drôle et original de s'offrir anonymement des cadeaux entre amis, collègues...<br />
                                Le déroulement est simple : chaque participant reçoit, de façon aléatoire, le nom de la personne à qui il devra faire un cadeau.<br />
                                Le montant du cadeau est généralement fixé au préalable (2€, 5€, 10€...)<br />
                                Le but n'est pas forcément de faire un beau cadeau mais d'être créatif !
                            </p>
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
            <h2 class="section-title text-center">Comment faire ?</h2>
            <p class="lead main text-center">Vous allez voir, c'est simple !</p>
            <div class="row text-center how">
                <ul class="media-list">
                    <li class="media">
                        <div class="media-left media-middle">
                            <img class="media-object" src="media/img/user-icon.png" />
                        </div>
                        <div class="media-body">
                            <h4 class="media-heading">Première étape : spécifier le nombre et les noms des participants</h4>
                            <p>
                                Grâce aux boutons "Ajouter un participant" et "Enlever un participant", il est possible d'ajuster le nombre de personnes.<br />
                                Pour chaque personne, indiquez un nom/prénom ou un pseudonyme. Deux participants ne peuvent avoir le même nom, sinon il est impossible de les différencier.<br />
                                A noter que secretsanta.fr est conçu de façon à ce qu'une personne ne puisse pas se piocher elle-même.
                            </p>
                        </div>
                    </li>
                    <li class="media">
                        <div class="media-body">
                            <h4 class="media-heading">Deuxième étape : remplir les informations de contact et les exclusions</h4>
                            <p>
                                Vous avez la possibilité de choisir si les participants recevront le nom de leur cible par e-mail, par sms, ou les deux.<br />
                                Pour cela, précisez pour chacun une adresse e-mail et/ou un numéro de téléphone portable.
                                (Optionel) Ajoutez des exclusions. Si vous ne voulez pas que les conjoints puissent se piocher l'un l'autre, remplissez le champ "Partenaire".<br />
                            </p>
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
                            <h4 class="media-heading">Troisième étape : préparer le mail ou le sms</h4>
                            <p>
                            Il ne vous reste plus qu'à remplir le titre et le corps du courriel ou du SMS que les participants recevront.<br />
                            Le mot clef "{TARGET}" est obligatoire dans le corps du message afin de donner à chaque personne sa "cible".<br />
                            (Optionel) Vous pouvez aussi utiliser le mot clef "{SANTA}" qui sera remplacé par le nom du destinataire du message.
                            </p>
                        </div>
                    </li>
                </ul>
                <div class="well well-lg">Pour votre information : secretsanta.fr ne sauvegarde aucune de vos données ni ne les utilise à d'autres fins. Le code source est disponible sur <a href="https://github.com/Korko/SecretSanta">GitHub</a></div>
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
            <h2 class="section-title text-center">À vous de jouer !</h2>
            <p class="lead main text-center">Remplissez, cliquez et c'est parti !</p>
            <div class="row text-center form">
                <div id="success-wrapper" class="alert alert-success" style="display: none">
                    Envoyé avec succès !
                </div>

                <div id="errors-wrapper" class="alert alert-danger" style="display: none">
                    <ul id="errors">
                    </ul>
                </div>

                <form action="{{ url('/') }}" method="post" autocomplete="off">
                    <fieldset>
                        <legend>Détails des participants</legend>
                        <div class="table-responsive form-group">
                            <table id="participants" class="table table-hover table-numbered">
                                <thead>
                                    <tr>
                                        <th class="col-xs-3">Nom ou pseudonyme</th>
                                        <th class="col-xs-3">Adresse e-mail</th>
                                        <th class="col-xs-0"></th>
                                        <th class="col-xs-2 col-lg-1">Téléphone</th>
                                        <th class="col-xs-2">Partenaire</th>
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
                                                    <input type="text" name="name[]" required="required" placeholder="exemple : Paul ou Korko" value="{{ $name }}" class="form-control participant-name" />
                                                </div>
                                            </label>
                                        </td>
                                        <td class="row border-left">
                                            <input type="email" name="email[]" placeholder="exemple : michel@aol.com" value="{{ array_get(old('email', []), $idx) }}" class="form-control participant-email" />
                                        </td>
                                        <td>
                                            et/ou
                                        </td>
                                        <td class="row border-right">
                                            <input type='tel' pattern='0[67]\d{8}' maxlength="10" name="phone[]" placeholder="ex : 0612345678" value="{{ array_get(old('phone', []), $idx) }}" class="form-control participant-phone" />
                                        </td>
                                        <td class="row">
                                            <select name="partner[]" class="form-control participant-partner">
                                                <option value="" {{ !array_get(old('partner'), $idx) ? 'selected="selected"' : '' }}>Aucun</option>
                                                @foreach(array_diff_key(old('name', []), [$idx => false]) as $idx2 => $name)
                                                    {{ $selected = (array_get(old('partner'), $idx) !== '' && intval(array_get(old('partner'), $idx)) === $idx2) }}
                                                    <option value="{{ $idx2 }}" {{ $selected ? 'selected="selected"' : '' }}>{{ ($idx2+1).". ".$name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td class="row participant-remove-wrapper">
                                            <button type="button" class="btn btn-danger participant-remove"><span class="glyphicon glyphicon-minus"></span><span> Enlever ce participant</span></button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-success participant-add"><span class="glyphicon glyphicon-plus"></span> Ajouter un participant</button>
                        </div>

                        <div class="row" id="contact">
                            <fieldset id="form-mail-group" class="col-md-6">
                                <div class="form-group">
                                    <label for="mailTitle">Titre du mail</label>
                                    <input id="mailTitle" type="text" name="title" required="required" placeholder="ex : Soirée secretsanta du 23 décembre chez Martin" value="{{ old('title') }}" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label for="mailContent">Contenu du mail</label>
                                    <textarea id="mailContent" name="contentMail" required="required" placeholder="ex : Salut {SANTA}, pour la soirée secret santa, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !" class="form-control" rows="3">{{ old('contentMail') }}</textarea>
                                    <p class="help-block">Utilisez "{SANTA}" pour le nom de celui qui recevra le mail et "{TARGET}" pour le nom de sa cible.</p>
                                    <p class="help-block">Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau.</p>
                                </div>
                            </fieldset>

                            <fieldset id="form-sms-group" class="col-md-6">
                                <div class="form-group">
                                    <label for="smsContent">Contenu du sms</label>
                                    <textarea id="smsContent" name="contentSMS" required="required" placeholder="ex : Salut {SANTA}, pour la soirée secret santa du 23 décembre chez Martin, ta cible c'est {TARGET}. Pour rappel, le montant du cadeau est de 3€ !" class="form-control" rows="3">{{ old('contentSMS') }}</textarea>
                                    <p class="help-block">Utilisez "{SANTA}" pour le nom de celui qui recevra le sms et "{TARGET}" pour le nom de sa cible.</p>
                                    <p class="help-block">Conseil : Pensez à rappeler la date, le lieu ainsi que le montant du cadeau.</p>
                                </div>
                            </fieldset>
                        </div>

                        <div class="form-group btn">
                            {!! Recaptcha::render() !!}
                        </div>

                        {{ csrf_field() }}
                        <button type="submit" class="btn btn-primary btn-lg">Lancez l'aléatoire !</button>
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
                    <br/>Projet De <a class="themeBy" href="http://www.korko.fr">Korko</a>
                    <br/>Theme Par <a class="themeBy" href="http://www.themewagon.com">ThemeWagon</a>
                </div>
            </div>
        </div>
        <!-- /.container -->
    </footer>

    <span id="forkongithub"><a href="https://github.com/Korko/SecretSanta" title="Fork me on GitHub">Fork me on GitHub</a></span>

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
</body>
</html>
