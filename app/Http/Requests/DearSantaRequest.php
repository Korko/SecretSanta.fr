<?php

namespace App\Http\Requests;

use App;

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
        $rules = [];
        if (! App::environment('local', 'dev')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $rules = [
            'content'              => 'required|string',
            'key'                  => 'required|string',
        ];

        return $rules;
    }
}
