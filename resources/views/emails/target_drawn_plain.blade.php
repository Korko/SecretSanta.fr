Ceci est un message automatique, merci de ne pas y répondre.

---

Bonjour {{ $santa['name'] }},

Vous avez été convié à un Secret Santa !

Voici le message de l'organisateur :
> {{ implode("\n> ", explode("\n", str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $content))) }}

@if (!empty($dearSantaLink))
Vous pouvez, si vous le souhaitez, écrire un petit message à votre Santa, la personne qui devra vous faire un cadeau. Pour cela, rendez-vous sur le lien suivant : {{ $dearSantaLink }}.

@endif
Amusez-vous bien !

{{ config('app.name') }} - {{ config('app.url') }}

