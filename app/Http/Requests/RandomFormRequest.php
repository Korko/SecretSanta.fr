<?php

namespace App\Http\Requests;

use App;

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
        $rules = [];
        if (!App::environment('local', 'dev')) {
            $rules['g-recaptcha-response'] = 'required|captcha';
        }

        $rules += [
            'name'                 => 'required|array|min:3|distinct',
            'title'                => 'required_with:email|nullable|string',
            'contentMail'          => 'required_with:email|nullable|string|contains:{TARGET}',
            'contentSMS'           => 'required_with:phone|nullable|string|contains:{TARGET}|smsCount:'.config('sms.max'),
            'email'                => 'array',
            'phone'                => 'array',
            'exclusions'           => 'array',
            'dearsanta'            => 'boolean|in:"0","1"',
            'dearsanta-expiration' => 'required_if:dearsanta,"1"|date|after:tomorrow|before:+1year',
        ];

        if (!empty($this->request->get('name'))) {
            foreach ($this->request->get('name', []) as $key => $name) {
                $emailCondition = '';
                if ($key === 0) {
                    $emailCondition = 'required_with:email|';
                }

                $rules += [
                    'email.'.$key      => $emailCondition.'required_without:phone.'.$key.'|required_if:dearsanta,1|nullable|email',
                    'phone.'.$key      => 'required_without:email.'.$key.'|nullable|numeric|regex:#0?[67]\d{8}#',
                    'exclusions.'.$key => 'sometimes|array',
                ];

                $exclusions = $this->request->get('exclusions') ?: [];
                $exclusions = isset($exclusions[$key]) ? (array) $exclusions[$key] : [];
                foreach ($exclusions as $key2 => $name2) {
                    $rules += [
                        'exclusions.'.$key.'.'.$key2 => 'integer|fieldinkeys:name,'.$key,
                    ];
                }
            }
        }

        return $rules;
    }
}
