<?php

namespace App\Http\Requests;

class RandomFormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules() + [
            'participants'                => 'required|array|min:3',

            'participants.*.name'         => 'required|distinct',
            'participants.*.email'        => 'required|email',
            'participants.*.exclusions'   => 'sometimes|array',
            'participants.*.exclusions.*' => 'integer|in_keys:participants',

            'title'                       => 'required|string',
            'content-email'               => 'required|string|contains:{TARGET}',

            'data-expiration'             => 'required|date|after_or_equal:tomorrow|before:+1year',
        ];
    }
}
