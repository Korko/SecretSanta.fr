<?php

namespace App\Http\Requests\Draw;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour ajouter un participant
 */
class AddParticipantRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du participant est obligatoire',
            'email.required' => 'L\'email du participant est obligatoire',
            'email.email' => 'L\'email doit être valide',
        ];
    }
}
