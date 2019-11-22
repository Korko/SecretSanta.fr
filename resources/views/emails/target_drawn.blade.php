@extends('emails/layout')

@section('content')

<tr>
	<td class="h2" style="font-size:35px; line-height:46px; text-align:center; font-style:italic; padding-bottom:15px;">
		Message aux participants
	</td>
</tr>
<tr>
	<td style="text-align:center; font-family: monospace; font-style:italic; padding-bottom:15px;">

		<p>Ceci est un message automatique, merci de ne pas y répondre.</p>
                <hr style="padding:0 10px !important" />

		<p style="padding-bottom:10px !important">Bonjour {{ $santa['name'] }},</p>

		<p style="padding-bottom:10px !important">Vous avez été convié à un Secret Santa !<p>

		<p>Voici le message de l'organisateur :</p>
		<blockquote style="border:1px solid #000000;background-color:#dddddd;">{!! nl2br(htmlentities( str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $content) )) !!}</blockquote>

		@if (!empty($dearSantaLink))
		<p style="padding-bottom:10px !important">Vous pouvez, si vous le souhaitez, écrire un petit message à votre Santa, la personne qui devra vous faire un cadeau. Pour cela, rendez-vous sur le lien suivant : <a href="{{ $dearSantaLink }}">{{ $dearSantaLink }}</a>.</p>
		@endif

		<p style="padding-bottom:10px !important">Amusez-vous bien !</p>

		<p><a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>

	</td>
</tr>

@endsection
