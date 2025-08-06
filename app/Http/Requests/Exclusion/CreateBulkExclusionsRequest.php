<?php

namespace App\Http\Requests\Exclusion;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour créer des exclusions en lot
 */
class CreateBulkExclusionsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'exclusions' => ['required', 'array', 'min:1'],
            'exclusions.*.participant_id' => ['required', 'exists:participants,id'],
            'exclusions.*.excluded_participant_id' => ['required', 'exists:participants,id'],
            'exclusions.*.type' => ['nullable', 'in:strong,weak'],
        ];
    }

    public function messages(): array
    {
        return [
            'exclusions.required' => 'Les exclusions sont obligatoires',
            'exclusions.array' => 'Les exclusions doivent être un tableau',
            'exclusions.min' => 'Au moins une exclusion est requise',
            'exclusions.*.participant_id.required' => 'Le participant est obligatoire',
            'exclusions.*.excluded_participant_id.required' => 'Le participant à exclure est obligatoire',
        ];
    }
}
