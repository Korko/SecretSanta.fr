<?php

namespace App\Http\Requests;

use App\Enums\QuestionToSanta;
use Illuminate\Validation\Rules\Enum;
use Lang;

class DearTargetRequest extends Request
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules(): array
    {
        return parent::rules() + [
            'type' => ['required', 'string', new Enum(QuestionToSanta::class)],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'type.required' => Lang::get('validation.custom.dearTarget.type.required'),
        ];
    }
}
