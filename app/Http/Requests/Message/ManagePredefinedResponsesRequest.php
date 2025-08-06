<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour gérer les réponses prédéfinies
 */
class ManagePredefinedResponsesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'responses' => ['nullable', 'array', 'max:50'],
            'responses.*' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'responses.max' => 'Vous ne pouvez créer plus de 50 réponses prédéfinies',
            'responses.*.required' => 'Les réponses ne peuvent être vides',
            'responses.*.max' => 'Les réponses ne peuvent dépasser 255 caractères',
        ];
    }
}
