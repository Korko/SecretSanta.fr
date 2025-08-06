<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour modérer un message
 */
class ModerateMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'action' => ['required', 'in:delete,dismiss'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    public function messages(): array
    {
        return [
            'action.required' => 'L\'action est obligatoire',
            'action.in' => 'L\'action doit être "delete" ou "dismiss"',
            'notes.max' => 'Les notes ne peuvent dépasser 500 caractères',
        ];
    }
}
