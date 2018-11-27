<?php

namespace Korko\SecretSanta\Http\Requests;

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
            'name'                 => 'required|array|min:3|arrayunique',
            'email'                => 'array',
            'phone'                => 'array',
            'exclusions'           => 'array',
            'dearsanta'            => 'boolean|in:"0","1"',
            'dearsanta-expiration' => 'required_if:dearsanta,"1"|date|after:tomorrow|before:+1year',
        ];

        if (!empty($this->request->get('name'))) {
            $keys = array_keys($this->request->get('name', []));
            $rules += [
                'title' => 'required_with:'.implode(',', array_map(function ($key) {
                    return 'email.'.$key;
                }, $keys)).'|string',
                'contentMail' => 'required_with:'.implode(',', array_map(function ($key) {
                    return 'email.'.$key;
                }, $keys)).'|string|contains:{TARGET}',
                'contentSMS' => 'required_with:'.implode(',', array_map(function ($key) {
                    return 'phone.'.$key;
                }, $keys)).'|string|contains:{TARGET}|smsCount:'.config('sms.max'),
            ];

            foreach ($this->request->get('name') as $key => $name) {
                $rules += [
                    'email.'.$key      => 'required_without:phone.'.$key.'|required_if:dearsanta,1|email',
                    'phone.'.$key      => 'required_without:email.'.$key.'|numeric|regex:#0?[67]\d{8}#',
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
