<?php

namespace App\Http\Requests;

class FixOrganizerRequest extends Request
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
            'url' => 'required|string',
            'email' => 'required|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'url.required' => __('validation.custom.fixOrganizer.url.required'),
            'email.required' => __('validation.custom.fixOrganizer.email.required'),
        ];
    }
}
