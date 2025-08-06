<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour envoyer un message
 */
class SendMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string', 'max:1000'],
            'type' => ['required', 'in:to_secret_santa,to_target'],
        ];
    }

    public function messages(): array
    {
        return [
            'content.required' => 'Le contenu du message est obligatoire',
            'content.max' => 'Le message ne peut dépasser 1000 caractères',
            'type.required' => 'Le type de message est obligatoire',
            'type.in' => 'Le type doit être "to_secret_santa" ou "to_target"',
        ];
    }
}
