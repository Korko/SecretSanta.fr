<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class OrganizerChangeNameRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     */
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return parent::rules() + [
            'name' => 'required|string|max:255',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'name.required' => __('validation.custom.randomform.participant.name.required'),
        ];
    }
}
