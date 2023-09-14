<?php

namespace App\Http\Requests;

class JoinDrawRequest extends AddNameRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return $this->combineRules(
            parent::rules(), [
                'email' => [
                    'required',
                    'email',
                    'max:320'
                ],
            ]
        );
    }
}
