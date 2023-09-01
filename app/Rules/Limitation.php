<?php

namespace App\Rules;

use App\Enums\AppMode;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\Validator;

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
     * Create a new rule instance.
     *
     * @param  array<\App\Enums\AppMode, string>  $rules List of validation rules depending on the AppMode of the request
     */
    public function __construct(
        protected array $rules = []
    ) {
    }

    /**
     * Set the data under validation.
     */
    public function setData(array $data): self
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Set the main current validator.
     */
    public function setValidator(Validator $validator): self
    {
        $this->validator = $validator;

        return $this;
    }

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $mode = Arr::get($this->data, 'mode', (AppMode::FREE)->value);

        if (isset($this->rules[$mode])) {
            $this->customValidator = ValidatorFacade::make(
                $this->data,
                [$attribute => $this->rules[$mode]],
                $this->getCustomMessages($mode)
            );

            if(! $this->customValidator->passes()) {
                $fail();
            }
        }

        unset($this->customValidator);
    }

    /**
     * Replace the place-holder in main validator custom messages
     *
     * @param  int  $mode Chosen AppMode key
     */
    protected function getCustomMessages($mode): array
    {
        $messages = $this->validator->customMessages;

        foreach ($messages as $rule => $message) {
            $messages[$rule] = str_replace(':mode', AppMode::from($mode)->name(), $message);
        }

        return $messages;
    }

    /**
     * Get the validation error message.
     */
    public function message(): string|array
    {
        if (! is_null($this->customValidator)) {
            return $this->customValidator->errors()->all();
        }

        return '';
    }
}
