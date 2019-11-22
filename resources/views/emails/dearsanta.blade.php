@extends('emails/layout')

@section('content')

<tr>
        <td class="h2" style="font-size:35px; line-height:46px; text-align:center; font-style:italic; padding-bottom:15px;">
                Message de votre cible
        </td>
</tr>
<tr>
        <td style="text-align:center; font-family: monospace; font-style:italic; padding-bottom:15px;">

                <p>Ceci est un message automatique, merci de ne pas y répondre.</p>
                <hr style="padding:0 10px !important" />

                <p style="padding-bottom:10px !important">Bonjour,</p>

                <p>Voici un message de la part de votre cible :</p>
                <blockquote style="border:1px solid #000000;background-color:#dddddd;">{!! nl2br(htmlentities( $content )) !!}</blockquote>

                <p><a href="{{ config('app.url') }}">{{ config('app.name') }}</a></p>

        </td>
</tr>

@endsection

