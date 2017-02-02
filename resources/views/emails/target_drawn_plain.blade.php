{{ str_replace(['{SANTA}', '{TARGET}'], [$santa['name'], $target['name']], $content) }}
{{ !empty($dearSantaLink) ? trans('form.mail.post2', ['link' => $dearSantaLink]) : trans('form.mail.post') }}

