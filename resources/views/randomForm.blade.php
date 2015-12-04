<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js"> <!--<![endif]-->
<head>
    <title>Secret Santa</title>

    <!-- meta -->
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1, user-scalable=no">

    <!-- css -->
    <link rel="stylesheet" href="media/css/bootstrap.min.css">
    <link rel="stylesheet" href="media/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="media/css/font-awesome.min.css">
    <link rel="stylesheet" href="media/css/main.css">

    <!-- google font -->
    <link rel='stylesheet' href='https://fonts.googleapis.com/css?family=Kreon:300,400,700'>

    <!-- js -->
    <script src="media/js/vendor/modernizr-2.6.2-respond-1.1.0.min.js"></script>

    <style>
        .participant:nth-child(-n+2) a.remove {display: none}
    </style>
    <link rel="stylesheet" href="media/css/alertify.core.css" />
    <link rel="stylesheet" href="media/css/alertify.default.css" />
    <link rel="stylesheet" href="media/css/alertify.bootstrap.css" />
</head>
<body data-spy="scroll" data-target="#navbar" data-offset="120" >
    <!--[if lt IE 7]>
        <p class="browsehappy">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
    <![endif]-->

    <div id="menu" class="navbar navbar-inverse navbar-fixed-top" role="navigation">
        <div class="container">
        <div id="navbar">
            <div class="hidden-xs" id="logo">
                <a href="#header">
                    <img src="media/img/logo.png" alt="">
                </a>
            </div>
        </div><!--/.navbar-collapse -->
        </div><!-- container -->
    </div><!-- menu -->

    <div id="header">
        <div class="bg-overlay"></div>
        <div class="center text-center">
            <div class="banner">
                <h1 class="">Secret Santa</h1>
            </div>
            <div class="subtitle"><h4>Offrez-vous des cadeaux... secretement !</h4></div>
        </div>
        <div class="bottom text-center">
            <a id="scrollDownArrow" href="#"><i class="fa fa-chevron-down"></i></a>
        </div>
    </div>
    <!-- /#header -->

    <div id="story" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">Comment faire ?</h2>
            <p class="lead main text-center">Vous allez voir, c'est simple !</p>
            <div class="row text-center story">



            </div>
            <!-- /.services -->
        </div>
        <!-- /.container -->
        <section class="ss-style-bottom"></section>
    </div><!-- #story -->

    <div id="form" class="light-wrapper">
        <section class="ss-style-top"></section>
        <div class="container inner">
            <h2 class="section-title text-center">A vous de jouer !</h2>
            <p class="lead main text-center">Remplissez, cliquez et c'est parti !</p>
            <div class="row text-center story">

                <div id="errors-wrapper" class="alert alert-danger" style="display: none">
                    <ul id="errors">
                    </ul>
                </div>

                <form action="{{ url('/') }}" method="post" autocomplete="off">
                    {{ csrf_field() }}
                    <fieldset>
                        <legend>Détails des participants</legend>
                        <table id="participants">
                            <thead>
                                <tr>
                                    <th>Nom ou pseudonyme</th>
                                    <th>Adresse e-mail</th>
                                    <th></th>
                                    <th>Partenaire (ne pourra être sa cible)</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Default is two empty rows to have two entries at any time --}}
                                @foreach(old('name', ['', '']) as $idx => $name)
                                <tr class="participant">
                                    <td>
                                        <input type="text" name="name[]" required="required" placeholder="exemple : Paul ou Korko" onblur="updateParticipant(event.target.parentNode.parentNode)" value="{{ $name }}" />
                                    </td>
                                    <td>
                                        <input type="email" name="email[]" required="required" placeholder="exemple : michel@aol.com" value="{{ array_get(old('email', []), $idx) }}" />
                                    </td>
                                    <td>
                                        {{-- Don't add new lines between the <a> and the <img>, it will result in ugly space characters --}}
                                        <a href="" class="remove" onclick="removeParticipant(event.target.parentNode.parentNode.parentNode);return false;" title="Supprimer ce participant"><img src="https://cdn1.iconfinder.com/data/icons/realistiK-new/16x16/actions/edit_remove.png" /></a>
                                    </td>
                                    <td>
                                        <select name="partner[]" onchange="updatePartner(event.target.parentNode.parentNode);">
                                            <option value="" {{ !array_get(old('partner'), $idx) ? 'selected="selected"' : '' }}>Aucun</option>
                                            @foreach(array_diff_key(old('name', []), [$idx => false]) as $idx2 => $name)
                                                {{ $selected = (array_get(old('partner'), $idx) !== '' && intval(array_get(old('partner'), $idx)) === $idx2) }}
                                                <option value="{{ $idx2 }}" {{ $selected ? 'selected="selected"' : '' }}>{{ $name }}</option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        {{-- Don't add new lines between the <a> and the <img>, it will result in ugly space characters --}}
                        <a href="" title="Ajouter un participant" onclick="addParticipant();return false;"><img src="https://cdn2.iconfinder.com/data/icons/splashyIcons/add_small.png" alt="+" /></a>
                        <br />
                        <br />
                        <p>
                            <label>Titre du mail<br />
                                <input type="text" name="title" required="required" placeholder="exemple : Soirée secretsanta du 23 décembre" value="{{ old('title') }}" />
                            </label>
                        </p>
                        <p>
                            <label>Contenu du mail (utilisez "{SANTA}" pour le nom de celui qui recevra le mail et "{TARGET}" pour le nom de sa cible)<br />
                                <textarea name="content" required="required" placeholder="exemple : Salut {SANTA}, pour la soirée secret santa, ta cible c'est {TARGET}.">{{ old('content') }}</textarea>
                            </label>
                        </p>
                        <input type="submit" name="submit" value="Lancez l'aléatoire !" />
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