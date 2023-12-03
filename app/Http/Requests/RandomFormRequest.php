<?php

namespace App\Http\Requests;

use App\Enums\AppMode;
use Illuminate\Validation\Rules\Enum;
use Lang;

class RandomFormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return parent::rules() + [
            'participant-organizer' => ['sometimes', 'boolean'],

            'title' => ['required', 'string', 'max:36773'],
            'description' => ['sometimes', 'string', 'max:36773'],

            'budget' => ['required', 'string', 'max:55'],
            'event-date' => ['sometimes', 'date'],

            'organizer.name' => ['required', 'string', 'max:55'],
            'organizer.email' => ['required', 'email', 'max:255'],

            'participants' => ['sometimes', 'array'],
            'participants.*.name' => ['required', 'string', 'max:55', 'distinct:ignore_case', 'different:organizer-name'],
            'participants.*.email' => ['sometimes', 'string', 'max:255'],

            'mode' => ['sometimes', new Enum(AppMode::class)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'title.required' => Lang::get('validation.custom.randomform.title.required'),
            'organizer-name.required' => Lang::get('validation.custom.randomform.organizer.name.required'),
            'organizer-email.required' => Lang::get('validation.custom.randomform.organizer.email.required'),
            'organizer-email.email' => Lang::get('validation.custom.randomform.organizer.email.email'),
            'participants.*.name.required' => Lang::get('validation.custom.randomform.participant.name.required'),
            'participants.*.name.distinct' => Lang::get('validation.custom.randomform.participant.name.distinct'),
        ];
    }
}
