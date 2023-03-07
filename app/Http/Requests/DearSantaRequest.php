<?php

namespace App\Http\Requests;

use Lang;

class DearSantaRequest extends Request
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
            'content' => 'required|string|max:36773',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'content.required' => Lang::get('validation.custom.dearSanta.content.required'),
        ];
    }
}
