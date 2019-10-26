<?php

namespace App\Services;

use Collective\Html\FormBuilder;
use App\Services\SymmetricalEncrypter as Encrypter;

class CryptedFormBuilder extends FormBuilder
{
    /**
     * Create a form input field.
     *
     * @param  string $type
     * @param  string $name
     * @param  string $value
     * @param  array  $options
     *
     * @return \Illuminate\Support\HtmlString
     */
    public function input($type, $name, $value = null, $options = [])
    {
        $key = md5(csrf_token() . config('app.key'));
        $name = (new Encrypter($key))->encrypt($name);
        return parent::input($type, $name, $value, $options);
    }
}
