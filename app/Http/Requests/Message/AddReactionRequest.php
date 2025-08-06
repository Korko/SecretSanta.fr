<?php

namespace App\Http\Requests\Message;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour ajouter une réaction
 */
class AddReactionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'reaction' => ['required', 'string', 'max:10'],
        ];
    }

    public function messages(): array
    {
        return [
            'reaction.required' => 'La réaction est obligatoire',
            'reaction.max' => 'La réaction ne peut dépasser 10 caractères',
        ];
    }
}
