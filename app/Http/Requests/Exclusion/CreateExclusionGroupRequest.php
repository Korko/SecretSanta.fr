<?php

namespace App\Http\Requests\Exclusion;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour créer un groupe d'exclusion
 */
class CreateExclusionGroupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Le nom du groupe est obligatoire',
            'name.max' => 'Le nom du groupe ne peut dépasser 255 caractères',
        ];
    }
}
