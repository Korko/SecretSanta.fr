<?php

namespace App\Http\Requests;

use App;
use Illuminate\Foundation\Http\FormRequest;

abstract class Request extends FormRequest
{
    public function rules()
    {
        return [];
    }
}
