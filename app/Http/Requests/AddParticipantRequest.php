<?php

namespace App\Http\Requests;

class AddParticipantRequest extends Request
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
        return $this->combineRules(
            parent::rules(), [
                'name' => [
                    'required',
                    'string',
                    'max:55',
                ],
                'email' => [
                    'sometimes',
                    'email',
                    'max:255',
                ]
            ]
        );
    }
}
