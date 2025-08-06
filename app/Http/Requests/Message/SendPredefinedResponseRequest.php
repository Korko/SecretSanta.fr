<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour envoyer une réponse prédéfinie
 */
class SendPredefinedResponseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'response_id' => ['required', 'exists:predefined_responses,id'],
            'type' => ['required', 'in:to_secret_santa,to_target'],
        ];
    }

    public function messages(): array
    {
        return [
            'response_id.required' => 'La réponse prédéfinie est obligatoire',
            'response_id.exists' => 'La réponse prédéfinie n\'existe pas',
            'type.required' => 'Le type de message est obligatoire',
            'type.in' => 'Le type doit être "to_secret_santa" ou "to_target"',
        ];
    }
}
