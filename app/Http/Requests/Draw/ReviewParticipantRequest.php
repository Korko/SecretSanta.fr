<?php

namespace App\Http\Requests\Draw;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour accepter/refuser un participant
 */
class ReviewParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:accept,reject'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'L\'action est obligatoire',
            'action.in' => 'L\'action doit être "accept" ou "reject"',
        ];
    }
}
