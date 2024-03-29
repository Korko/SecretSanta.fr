<?php

namespace App\Http\Requests;

use Closure;

class JoinDrawRequest extends AddParticipantRequest
{
    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return $this->combineRules(
            parent::rules(), [
                'name' => [
                    // New participant name should not be the same as the organizer name
                    function (string $attribute, mixed $value, Closure $fail) {
                        if($this->draw->organizer->name === $value) {
                            $fail('validation.custom.participant.name.same-as-organizer');
                        }
                    },
                    // New participant name should not be the same as another participant
                    function (string $attribute, mixed $value, Closure $fail) {
                        $otherNames = $this->draw
                            ->participants
                            ->where('email', '!=', null)
                            ->pluck('name');

                        if($otherNames->contains($value)) {
                            $fail('validation.custom.participant.name.not-unique');
                        }
                    }
                ],
                'email' => [
                    'required'
                ],
            ]
        );
    }
}
