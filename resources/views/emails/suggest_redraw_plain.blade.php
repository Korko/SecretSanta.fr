@extends('emails/layout_plain')

@section('title')
    Proposition de l'organisateur
@endsection

@section('content')
    Bonjour {{ $participantName }},

    {{ $organizerName }}, l'organisateur de cet évènement SecretSanta vous propose de réorganiser un tirage au sort. Cela voudrait dire que potentiellement, vous ne devriez plus offrir un cadeau à {{ $targetName }}.

    Vous n'êtes pas obligé d'accepter ! Mais si vous le souhaitez, vous pouvez aller sur le lien suivant afin d'indiquer à votre organisateur que vous acceptez de remettre le nom de votre cible au hasard. Quand vous serez assez nombreux à faire de même, il pourra relancer l'aléatoire et alors vous recevrez un nouvel email, similaire au tout premier, vous indiquant le nom de votre nouvelle cible.

    Pour accepter la proposition de l'organisateur, c'est par ici : {{ $acceptLink }}

    Si vous ne souhaitez pas changer de cible, vous pouvez simplement ignorer ce message.
@endsection