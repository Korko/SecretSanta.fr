<?php

namespace App\Http\Requests;

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
            'participant-organizer'       => 'required|boolean',

            'organizer.name'              => 'exclude_if:participant-organizer,true|required',
            'organizer.email'             => 'exclude_if:participant-organizer,true|required|email',

            'participants'                => 'required|array|min:3',

            'participants.*.name'         => 'required|distinct',
            'participants.*.email'        => 'required|email',
            'participants.*.exclusions'   => 'sometimes|array',
            'participants.*.exclusions.*' => 'integer|in_keys:participants',

            'title'                       => 'required|string',
            'content-email'               => 'required|string|contains:{TARGET}',

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
            'participant-organizer.required' => __('validation.custom.randomform.participant-organizer.required'),
            'organizer.name.required'        => __('validation.custom.randomform.organizer.name.required'),
            'organizer.email.required'       => __('validation.custom.randomform.organizer.email.required'),
            'organizer.email.email'          => __('validation.custom.randomform.organizer.email.email'),
            'participants.min'               => __('validation.custom.randomform.participants.length'),
            'participants.*.name.required'   => __('validation.custom.randomform.participant.name.required'),
            'participants.*.name.distinct'   => __('validation.custom.randomform.participant.name.distinct'),
            'participants.*.email.required'  => __('validation.custom.randomform.participant.email.required'),
            'participants.*.email.email'     => __('validation.custom.randomform.participant.email.format'),
            'title.required'                 => __('validation.custom.randomform.title.required'),
            'content-email.required'         => __('validation.custom.randomform.content.required'),
            'content-email.contains'         => __('validation.custom.randomform.content.contains'),
            'data-expiration.required'       => __('validation.custom.randomform.expiration.required'),
            'data-expiration.after_or_equal' => __('validation.custom.randomform.expiration.min'),
            'data-expiration.before'         => __('validation.custom.randomform.expiration.max'),
            'data-expiration.date_format'    => __('validation.custom.randomform.expiration.format'),
        ];
    }
}
