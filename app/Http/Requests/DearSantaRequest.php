<?php

namespace Korko\SecretSanta\Http\Requests;

class DearSantaRequest extends Request
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
        $rules = [
            'g-recaptcha-response' => 'required|captcha',
            'title'                => 'required|string',
            'content'              => 'required|string',
            'key'                  => 'required|string',
        ];

        return $rules;
    }
}
