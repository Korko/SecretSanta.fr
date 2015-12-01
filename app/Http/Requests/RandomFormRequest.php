<?php

namespace App\Http\Requests;

use App\Http\Requests\Request;

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
// TODO: Add new rules: arrayunique and fieldin:fieldName (fieldin only if defined?)
	$rules = [
	    'name' => 'required|array|arrayunique',
	    'email' => 'array',
	    'number' => 'array',
	    'partner' => 'array'
	];

	foreach ($this->request->get('name') as $key => $name) {
	    $rules += [
		'email.'.$key => 'required_unless:number.'.$key.'|email'
		'number.'.$key => 'required_unless:email.'.$key.'|numeric|regex:#336\d{8}',
		'partner.'.$key => 'string|fieldin:name'
	    ];
	}

	return $rules;
    }
}
