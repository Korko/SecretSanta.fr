@extends('emails/layout_plain')

@section('title')
    Une idée cadeau pour {{ $targetName }}
@endsection

@section('content')
    @if ($senderName)
    {{ $senderName }} vous propose une idée de cadeau pour {{ $targetName }} :
    @else
    Un(e) autre participant(e) vous propose une idée de cadeau pour {{ $targetName }} :
    @endif
    > {{ $content }}
    Cette personne ne sait pas que vous êtes le père/mère noël secret de {{ $targetName }}. Votre page de participant(e) : {{ $dearSantaLink }}
@endsection
