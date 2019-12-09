@extends('emails/layout')

@section('content')

<tr>
	<td class="h2" style="font-size:35px; line-height:46px; text-align:center; font-style:italic; padding-bottom:15px;">
		Récapitulatif Final Organisateur
	</td>
</tr>
<tr>
	<td style="text-align:center; font-family: monospace; font-style:italic; padding-bottom:15px;">
		<p style="padding-bottom:10px !important">Bonjour {{ $draw->organizer->name }},</p>

		<p style="padding-bottom:10px !important">Merci d'avoir organisé un SecretSanta en utilisant <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.</p>

		<p style="padding-bottom:10px !important">Vous trouverez en pièce jointe un récapitulatif final des participants que vous avez défini sur le site. Si cela est possible, il inclut les exclusions comprenant la cible de chacun pour éviter de repiocher la même personne. Vous pourrez le réutiliser lors de votre prochain SecretSanta pour accélérer la rentrée des informations. En cas de problème durant votre tirage, vous pouvez aussi vérifier si il y a eu une erreur dans les données entrées.</p>

		<p style="padding-bottom:10px !important">Amusez-vous bien !</p>

		<p><a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>
	</td>
</tr>

@endsection
