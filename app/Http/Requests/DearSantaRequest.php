<?php

declare(strict_types=1);

namespace App\Http\Requests;

final class DearSantaRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return parent::rules() + [
            'content' => 'required|string',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'content.required' => __('validation.custom.dearSanta.content.required'),
        ];
    }
}
