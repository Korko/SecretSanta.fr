<?php

namespace App\Http\Requests\Exclusion;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour créer une exclusion
 */
class CreateExclusionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'participant_id' => ['required', 'exists:participants,id'],
            'excluded_participant_id' => ['required', 'exists:participants,id', 'different:participant_id'],
            'type' => ['nullable', 'in:strong,weak'],
        ];
    }

    public function messages(): array
    {
        return [
            'participant_id.required' => 'Le participant est obligatoire',
            'participant_id.exists' => 'Le participant n\'existe pas',
            'excluded_participant_id.required' => 'Le participant à exclure est obligatoire',
            'excluded_participant_id.exists' => 'Le participant à exclure n\'existe pas',
            'excluded_participant_id.different' => 'Un participant ne peut pas s\'exclure lui-même',
            'type.in' => 'Le type doit être "strong" ou "weak"',
        ];
    }
}
