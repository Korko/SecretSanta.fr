<?php

namespace App\Http\Requests;

class OrganizerChangeEmailRequest extends Request
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
            'email'                => 'required|string',
            'key'                  => 'required|string',
        ];
    }
}
