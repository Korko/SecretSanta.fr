@extends('emails/layout')

@section('content')

<tr>
	<td class="h2" style="font-size:35px; line-height:46px; text-align:center; font-style:italic; padding-bottom:15px;">
		Récapitulatif Organisateur
	</td>
</tr>
<tr>
	<td style="text-align:center; font-family: monospace; font-style:italic; padding-bottom:15px;">
		<p style="padding-bottom:10px !important">Bonjour {{ $organizerName }},</p>

		<p style="padding-bottom:10px !important">Merci d'avoir organisé un SecretSanta en utilisant <a href="{{ config('app.url') }}">{{ config('app.name') }}</a>.</p>

                <p style="padding-bottom:10px !important">Afin de surveiller si tous les participants ont bien reçu leur email ou modifier une adresse en cas d'erreur, rendez vous sur votre interface personnalisée : <a href="{{ $panelLink }}">{{ $panelLink }}</a>

		<p style="padding-bottom:10px !important">Conformément à la date d'expiration que vous avez définie ({{ $expirationDate }}), vous recevrez le lendemain un récapitulatif définitif contenant la liste des participants. Vous pourrez le réutiliser lors de votre prochain SecretSanta pour accélérer la rentrée des informations.</p>

		<p style="padding-bottom:10px !important">Amusez-vous bien !</p>

		<p><a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>
	</td>
</tr>

@endsection
