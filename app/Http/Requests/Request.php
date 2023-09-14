<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function combineRules(array $rules1, array $rules2)
    {
        foreach($rules2 as $key => $item) {
            if (!isset($rules1[$key])) {
                $rules1[$key] = $item;
            } else {
                $rules1[$key] = array_merge(
                    (array) $rules1[$key],
                    (array) $rules2[$key]
                );
            }
        }

        return $rules1;
    }

    public function rules(): array
    {
        return [];
    }
}
