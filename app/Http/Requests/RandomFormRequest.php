<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\ParticipantLimit;

final class RandomFormRequest extends Request
{
    /**
     * Get the validation rules that apply to the request.
     */
    /** @return array<string, mixed> */
    public function rules(): array
    {
        return parent::rules() + [
            'participant-organizer' => 'sometimes|boolean',

            'organizer' => 'sometimes|array',
            'organizer.name' => 'exclude_if:participant-organizer,true|required|string|max:255',
            'organizer.email' => 'exclude_if:participant-organizer,true|required|email',

            'limit' => 'sometimes|nullable|string',
            'participants' => 'required|array|min:3|max:'.$this->maxParticipants(),

            'participants.*.name' => 'required|string|max:255|distinct',
            'participants.*.email' => 'required|email',
            'participants.*.exclusions' => 'sometimes|array',
            'participants.*.exclusions.*' => 'integer|in_keys:participants',

            'title' => 'required|string|max:255',
            'content-email' => 'required|string|max:10000|contains_text:{TARGET}',

            'data-expiration' => 'required|date_format:Y-m-d|after_or_equal:tomorrow|before:+1year',

            // "Le jour J": optional
            'data-location' => 'sometimes|nullable|string|max:255',
            'data-budget' => 'sometimes|nullable|numeric|min:0|max:100000',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     */
    public function messages(): array
    {
        return [
            'participant-organizer.required' => __('validation.custom.randomform.participant-organizer.required'),
            'organizer.name.required' => __('validation.custom.randomform.organizer.name.required'),
            'organizer.email.required' => __('validation.custom.randomform.organizer.email.required'),
            'organizer.email.email' => __('validation.custom.randomform.organizer.email.email'),
            'participants.min' => __('validation.custom.randomform.participants.length'),
            'participants.max' => __('validation.custom.randomform.participants.max', ['max' => $this->maxParticipants(), 'email' => config()->string('app.email')]),
            'participants.*.name.required' => __('validation.custom.randomform.participant.name.required'),
            'participants.*.name.distinct' => __('validation.custom.randomform.participant.name.distinct'),
            'participants.*.email.required' => __('validation.custom.randomform.participant.email.required'),
            'participants.*.email.email' => __('validation.custom.randomform.participant.email.format'),
            'title.required' => __('validation.custom.randomform.title.required'),
            'content-email.required' => __('validation.custom.randomform.content.required'),
            'content-email.contains_text' => __('validation.custom.randomform.content.contains_text'),
            'data-expiration.required' => __('validation.custom.randomform.expiration.required'),
            'data-expiration.after_or_equal' => __('validation.custom.randomform.expiration.min'),
            'data-expiration.before' => __('validation.custom.randomform.expiration.max'),
            'data-expiration.date_format' => __('validation.custom.randomform.expiration.format'),
            'data-location.max' => __('validation.custom.randomform.location.max'),
            'data-budget.numeric' => __('validation.custom.randomform.budget.format'),
            'data-budget.min' => __('validation.custom.randomform.budget.format'),
            'data-budget.max' => __('validation.custom.randomform.budget.max'),
        ];
    }

    /** The single use administrator code given with the form (?limit=… on the home page), if still unused */
    public function participantLimit(): ?ParticipantLimit
    {
        return once(fn (): ?ParticipantLimit => ParticipantLimit::findUsable($this->string('limit')->value()));
    }

    public function maxParticipants(): int
    {
        return $this->participantLimit()->max ?? config()->integer('app.max_participants');
    }
}
