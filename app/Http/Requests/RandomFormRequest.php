<?php

namespace Korko\SecretSanta\Http\Requests;

use Korko\SecretSanta\Http\Requests\Request;

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
        $rules = [
            'g-recaptcha-response' => 'required|recaptcha',
            'name' => 'required|arrayunique',
            'title' => 'required|string',
            'content' => 'required|contains:{TARGET}',
            'email' => 'array',
            'number' => 'array',
            'partner' => 'array'
        ];

        foreach ($this->request->get('name') as $key => $name) {
            $rules += [
                'email.'.$key => 'required_if:number.'.$key.',null|email',
                'number.'.$key => 'required_if:email.'.$key.',null|numeric|regex:#336\d{8}',
                'partner.'.$key => 'sometimes|string|fieldin:name'
            ];
        }

        return $rules;
    }
}
