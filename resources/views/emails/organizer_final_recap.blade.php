@extends('emails/layout')

@section('content')

<tr>
	<td class="h2" style="font-size:35px; line-height:46px; text-align:center; font-style:italic; padding-bottom:15px;">
		Récapitulatif Final Organisateur
	</td>
</tr>
<tr>
	<td style="text-align:center; font-family: monospace; font-style:italic; padding-bottom:15px;">
		<p style="padding-bottom:10px !important">Bonjour {{ $organizerName }},</p>

		<p style="padding-bottom:10px !important">Merci d'avoir organisé votre évènement en utilisant <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.</p>

		<p style="padding-bottom:10px !important">Vous trouverez en pièce jointe un récapitulatif des participants et exclusions définies pour l'évènement du {{ $expirationDate }}, ainsi que le résultat du tirage. Le fichier récapitulatif peut être réutilisé sur {{ config('app.name') }} pour gagner du temps lors de l'organisation de votre prochain évènement. Le fichier avec les résultats vous permettra de définir de nouvelles exclusions si vous ne souhaitez pas pouvoir retomber sur la même personne la prochaine fois !</p>

		<p style="padding-bottom:10px !important">Secrètement votre,</p>

		<p><a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>
	</td>
</tr>

@endsection
