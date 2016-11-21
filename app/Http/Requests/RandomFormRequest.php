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
            'name'                 => 'required|array|min:2|arrayunique',
            'email'                => 'array',
            'phone'                => 'array',
            'partner'              => 'array',
        ];

        if (!empty($this->request->get('name'))) {
            $rules += [
                'title'       => 'required_with:'.implode(',', array_map(function ($key) {
                    return 'email.'.$key;
                }, array_keys($this->request->get('name', [])))).'|string',
                'contentMail' => 'required_with:'.implode(',', array_map(function ($key) {
                    return 'email.'.$key;
                }, array_keys($this->request->get('name', [])))).'|contains:{TARGET}',
                'contentSMS'  => 'required_with:'.implode(',', array_map(function ($key) {
                    return 'phone.'.$key;
                }, array_keys($this->request->get('name', [])))).'|contains:{TARGET}|max:130',
            ];

            foreach ($this->request->get('name') as $key => $name) {
                $rules += [
                    'email.'.$key   => 'required_without:phone.'.$key.'|email',
                    'phone.'.$key   => 'required_without:email.'.$key.'|numeric|regex:#0[67]\d{8}#',
                    'partner.'.$key => (isset($this->request->get('partner')[$key]) && $this->request->get('partner')[$key] !== '-1') ? 'sometimes|numeric|fieldinkeys:name,'.$key : 'sometimes|numeric',
                ];
            }
        }

        return $rules;
    }
}
