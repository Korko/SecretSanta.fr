<p>{{ str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $content) }}</p>

{!! nl2br(htmlentities( !empty($dearSantaLink) ? trans('form.mail.post2', ['link' => $dearSantaLink]) : trans('form.mail.post') )) !!}

