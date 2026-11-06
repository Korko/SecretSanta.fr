<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class OrganizerChangeEmailRequest extends Request
{
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
