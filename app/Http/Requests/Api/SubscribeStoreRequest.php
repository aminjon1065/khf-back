<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class SubscribeStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'channel' => ['required', 'string'],
            'region' => ['required', 'string'],
            'contact' => ['required', 'string'],
            'categories' => ['array'],
            'categories.*' => ['string'],
        ];
    }
}
