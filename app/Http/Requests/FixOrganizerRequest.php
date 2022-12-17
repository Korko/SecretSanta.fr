<?php

namespace App\Http\Requests;

class FixOrganizerRequest extends Request
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
            'url' => 'required|string',
            'email' => 'required|string|max:255',
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
            'url.required' => __('validation.custom.fixOrganizer.url.required'),
            'email.required' => __('validation.custom.fixOrganizer.email.required'),
        ];
    }
}
