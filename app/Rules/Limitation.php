<?php

namespace App\Rules;

use App\Enums\AppMode;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Concerns\FormatsMessages;

class Limitation implements Rule, DataAwareRule, ValidatorAwareRule
{
    /**
     * All of the data under validation.
     *
     * @var array
     */
    protected $data = [];

    /**
     * The main validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $validator;

    /**
     * The last validator instance
     *
     * @var \Illuminate\Validation\Validator
     */
    protected $customValidator = null;

    /**
     * Validation rules
     *
     * @var array
     */
    protected $rules = [];

    /**
     * Create a new rule instance.
     *
     * @param array<\App\Enums\AppMode, string>  $rules List of validation rules depending on the AppMode of the request
     * @return void
     */
    public function __construct(array $rules)
    {
        $this->rules = $rules;
    }

    /**
     * Set the data under validation.
     *
     * @param  array  $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the main current validator.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return $this
     */
    public function setValidator($validator)
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $mode = Arr::get($this->data, 'mode', (AppMode::FREE)->value);

        if (isset($this->rules[$mode])) {
            $this->customValidator = Validator::make(
                $this->data,
                [$attribute => $this->rules[$mode]],
                $this->getCustomMessages($mode)
            );

            return $this->customValidator->passes();
        }

        unset($this->customValidator);

        return true;
    }

    /**
     * Replace the place-holder in main validator custom messages
     *
     * @param  int  $mode Choosen AppMode key
     * @return array
     */
    protected function getCustomMessages($mode) : array
    {
        $messages = $this->validator->customMessages;

        foreach ($messages as $rule => $message) {
            $messages[$rule] = str_replace(':mode', AppMode::from($mode)->name(), $message);
        }

        return $messages;
    }

    /**
     * Get the validation error message.
     *
     * @return string|array
     */
    public function message()
    {
        if (! is_null($this->customValidator)) {
            return $this->customValidator->errors()->all();
        }
        return '';
    }
}
