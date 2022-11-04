<?php

namespace App\Http\Requests;

use App\Enums\AppMode;
use App\Rules\Limitation;
use Illuminate\Validation\Rules\Enum;
use Lang;

class RandomFormRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return parent::rules() + [
            'participant-organizer' => ['sometimes', 'boolean'],

            'organizer' => ['sometimes', 'array'],
            'organizer.name' => ['exclude_if:participant-organizer,true', 'required', 'max:55'],
            'organizer.email' => ['exclude_if:participant-organizer,true', 'required', 'email', 'max:320'],

            'participants' => [
                'required', 'array', 'min:3', new Limitation(
                    // Max rule is ignored if there no 'array' rule with it (to detect how to count)
                    array_map(fn ($limit) => 'array|max:'.$limit, config('modes.limitations.participants'))
                ),
            ],

            'participants.*.name' => ['required', 'distinct', 'max:55'],
            'participants.*.email' => ['required', 'email', 'max:320'],
            'participants.*.exclusions' => ['sometimes', 'array'],
            'participants.*.exclusions.*' => ['integer', 'in_keys:participants'],

            'title' => ['required', 'string', 'max:36773'],
            'content' => ['required', 'string', 'max:36773'],

            'mode' => ['sometimes', new Enum(AppMode::class)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'participant-organizer.required' => Lang::get('validation.custom.randomform.participant-organizer.required'),
            'organizer.name.required' => Lang::get('validation.custom.randomform.organizer.name.required'),
            'organizer.email.required' => Lang::get('validation.custom.randomform.organizer.email.required'),
            'organizer.email.email' => Lang::get('validation.custom.randomform.organizer.email.email'),
            'participants.min' => Lang::get('validation.custom.randomform.participants.length'),
            'participants.*.name.required' => Lang::get('validation.custom.randomform.participant.name.required'),
            'participants.*.name.distinct' => Lang::get('validation.custom.randomform.participant.name.distinct'),
            'participants.*.email.required' => Lang::get('validation.custom.randomform.participant.email.required'),
            'participants.*.email.email' => Lang::get('validation.custom.randomform.participant.email.format'),
            'title.required' => Lang::get('validation.custom.randomform.title.required'),
            'content.required' => Lang::get('validation.custom.randomform.content.required'),
        ];
    }
}
