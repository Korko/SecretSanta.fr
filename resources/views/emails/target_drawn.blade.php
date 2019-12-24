@extends('emails/layout')

@section('content')

<tr>
	<td class="h2" style="font-size:35px; line-height:46px; text-align:center; font-style:italic; padding-bottom:15px;">
		Message aux participants
	</td>
</tr>
<tr>
	<td style="text-align:center; font-family: monospace; font-style:italic; padding-bottom:15px;">

		<p style="border:1px solid #000000;background-color:#dddddd;">Ceci est un message automatique, merci de ne pas y répondre.</p>

		<blockquote>{!! nl2br(htmlentities($content)) !!}</blockquote>

                <hr/>

		<p style="padding-bottom:10px !important">Pour écrire à votre Père Noël Secret : <a href="{{ $dearSantaLink }}">{{ $dearSantaLink }}</a></p>

		<p><a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>

	</td>
</tr>

@endsection
