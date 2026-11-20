@extends('emails/layout')

@section('title')
    Messages de {{ $targetName }}
@endsection

@section('content')
    <p style="padding-bottom:10px !important">Voici tous les messages que {{ $targetName }} a écrits à son Père Noël Secret, c'est-à-dire à vous.</p>

    {{-- Not $message: the layout embeds its images through the mail message of that name --}}
    @foreach ($messages as $targetMessage)
        <p style="padding-bottom:5px !important"><em>{{ $targetMessage['date'] }}</em></p>
        <blockquote>{!! nl2br(e($targetMessage['content'])) !!}</blockquote>
    @endforeach
@endsection
