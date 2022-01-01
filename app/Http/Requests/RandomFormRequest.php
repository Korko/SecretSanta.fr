<?php

namespace App\Http\Requests;

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
            'participant-organizer'       => 'sometimes|boolean',

            'organizer'                   => 'sometimes|array',
            'organizer.name'              => 'exclude_if:participant-organizer,true|required',
            'organizer.email'             => 'exclude_if:participant-organizer,true|required|email',

            'participants'                => 'required|array|min:3',

            'participants.*.name'         => 'required|distinct',
            'participants.*.email'        => 'required|email',
            'participants.*.exclusions'   => 'sometimes|array',
            'participants.*.exclusions.*' => 'integer|in_keys:participants',

            'title'                       => 'required|string',
            'content'                     => 'required|string|contains:{TARGET}',

            'data-expiration'             => 'required|date_format:Y-m-d|after_or_equal:tomorrow|before:+6month',
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
            'organizer.name.required'        => Lang::get('validation.custom.randomform.organizer.name.required'),
            'organizer.email.required'       => Lang::get('validation.custom.randomform.organizer.email.required'),
            'organizer.email.email'          => Lang::get('validation.custom.randomform.organizer.email.email'),
            'participants.min'               => Lang::get('validation.custom.randomform.participants.length'),
            'participants.*.name.required'   => Lang::get('validation.custom.randomform.participant.name.required'),
            'participants.*.name.distinct'   => Lang::get('validation.custom.randomform.participant.name.distinct'),
            'participants.*.email.required'  => Lang::get('validation.custom.randomform.participant.email.required'),
            'participants.*.email.email'     => Lang::get('validation.custom.randomform.participant.email.format'),
            'title.required'                 => Lang::get('validation.custom.randomform.title.required'),
            'content.required'               => Lang::get('validation.custom.randomform.content.required'),
            'content.contains'               => Lang::get('validation.custom.randomform.content.contains'),
            'data-expiration.required'       => Lang::get('validation.custom.randomform.expiration.required'),
            'data-expiration.after_or_equal' => Lang::get('validation.custom.randomform.expiration.min'),
            'data-expiration.before'         => Lang::get('validation.custom.randomform.expiration.max'),
            'data-expiration.date_format'    => Lang::get('validation.custom.randomform.expiration.format'),
        ];
    }
}
