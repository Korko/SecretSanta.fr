@extends('emails/layout')

@section('title')
    Une idée cadeau pour {{ $targetName }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">
        @if ($senderName)
            {{ $senderName }} vous propose une idée de cadeau pour {{ $targetName }} :
        @else
            Un(e) autre participant(e) vous propose une idée de cadeau pour {{ $targetName }} :
        @endif
    </p>

    <blockquote>{!! nl2br(htmlentities( $content )) !!}</blockquote>

    <p style="padding-bottom:10px !important">Cette personne ne sait pas que vous êtes le père/mère noël secret de {{ $targetName }}. Votre page de participant(e) : <a href="{{ $dearSantaLink }}">{{ $dearSantaLink }}</a></p>
@endsection
