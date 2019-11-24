Ceci est un message automatique, merci de ne pas y répondre.

---

{{ str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $content) }}

---

@if (!empty($dearSantaLink))
Pour écrire à votre Père Noël Secret : {{ $dearSantaLink }}

@endif
{{ config('app.name') }} - {{ config('app.url') }}
