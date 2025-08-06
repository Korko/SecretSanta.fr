<?php

namespace App\Http\Requests\Draw;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Validation pour créer un tirage
 */
class CreateDrawRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Accessible à tous
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:1000'],
            'organizer_name' => ['required', 'string', 'max:255'],
            'organizer_email' => ['required', 'email', 'max:255'],
            'auto_accept_participants' => ['boolean'],
            'allow_target_messages' => ['boolean'],
            'registration_deadline' => ['nullable', 'date', 'after:now'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Le titre du tirage est obligatoire',
            'organizer_name.required' => 'Le nom de l\'organisateur est obligatoire',
            'organizer_email.required' => 'L\'email de l\'organisateur est obligatoire',
            'organizer_email.email' => 'L\'email doit être valide',
            'registration_deadline.after' => 'La date limite doit être dans le futur',
        ];
    }
}
