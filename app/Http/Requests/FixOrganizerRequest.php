<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class FixOrganizerRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     */
    /** @return array<string, mixed> */
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
