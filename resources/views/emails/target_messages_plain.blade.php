@extends('emails/layout_plain')

@section('title')
    Messages de {{ $targetName }}
@endsection

@section('content')
    Voici tous les messages que {{ $targetName }} a écrits à son Père Noël Secret, c'est-à-dire à vous.
@foreach ($messages as $targetMessage)

    {{ $targetMessage['date'] }}
    > {{ $targetMessage['content'] }}
@endforeach
@endsection
