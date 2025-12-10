<?php

namespace App\Http\Requests;

class OrganizerChangeEmailRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return parent::rules() + [
            'email' => 'required|email',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'email.required' => __('validation.custom.organizer.email.required'),
            'email.email' => __('validation.custom.organizer.email.format'),
        ];
    }
}
