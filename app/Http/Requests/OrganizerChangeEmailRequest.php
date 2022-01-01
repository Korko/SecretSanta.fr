<?php

namespace App\Http\Requests;

use Lang;

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
            'email' => 'required|email|max:320',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'email.required' => Lang::get('validation.custom.organizer.email.required'),
            'email.email'    => Lang::get('validation.custom.organizer.email.format'),
        ];
    }
}
