<!DOCTYPE html>
<html>
    <head>
        <title>Tirage au sort de Secret Santa</title>
        <style>
            .participant:nth-child(-n+2) a.remove {display: none}
        </style>
        <link rel="stylesheet" href="{{ url('media/css/alertify.core.css') }}" />
        <link rel="stylesheet" href="{{ url('media/css/alertify.default.css') }}" />
    </head>
    <body>
        {{-- Display errors if any --}}
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <form action="" method="post" autocomplete="off">
            {{ csrf_field() }}
            <fieldset>
                <legend>Détails des participants</legend>
                <table id="participants">
                    <thead>
                        <tr>
                            <th>Nom ou pseudonyme</th>
                            <th>Adresse e-mail</th>
                            <th>Supprimer la ligne</th>
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
                                <a href="" class="remove" onclick="removeParticipant(event.target.parentNode.parentNode);return false;" title="Supprimer ce participant"><img src="https://cdn1.iconfinder.com/data/icons/realistiK-new/16x16/actions/edit_remove.png" /></a>
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
                        <textarea name="content" required="required">{{ old('content') }}</textarea>
                    </label>
                </p>
                <input type="submit" name="submit" value="Lancez l'aléatoire !" />
            </fieldset>
        </form>
        <a href="https://github.com/Korko/SecretSanta" title="Fork me on GitHub"><img style="position: absolute; top: 0; right: 0; border: 0;" src="https://camo.githubusercontent.com/365986a132ccd6a44c23a9169022c0b5c890c387/68747470733a2f2f73332e616d617a6f6e6177732e636f6d2f6769746875622f726962626f6e732f666f726b6d655f72696768745f7265645f6161303030302e706e67" alt="Fork me on GitHub" data-canonical-src="https://s3.amazonaws.com/github/ribbons/forkme_right_red_aa0000.png"></a>

        <script type="text/javascript" src="{{ url('media/js/randomForm.js') }}"></script>
        <script type="text/javascript" src="{{ url('media/js/alertify.min.js') }}"></script>

        @if(Session::has('message'))
        <script type="text/javascript">
            alertify.success("{{ session('message') }}");
        </script>
        @endif
    </body>
</html>
