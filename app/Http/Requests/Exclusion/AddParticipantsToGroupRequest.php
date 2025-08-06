<?php

namespace App\Http\Requests\Exclusion;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour ajouter des participants à un groupe
 */
class AddParticipantsToGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'participant_ids' => ['required', 'array', 'min:1'],
            'participant_ids.*' => ['required', 'exists:participants,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'participant_ids.required' => 'Les participants sont obligatoires',
            'participant_ids.array' => 'Les participants doivent être un tableau',
            'participant_ids.min' => 'Au moins un participant est requis',
            'participant_ids.*.exists' => 'Un ou plusieurs participants n\'existent pas',
        ];
    }
}
