<?php

namespace App\Http\Requests\Api;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class ReportStoreRequest extends FormRequest
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
            'type' => ['required', 'string'],
            'region' => ['required', 'string'],
            'location' => ['required', 'string'],
            'description' => ['required', 'string'],
            'phone' => ['required', 'string'],
        ];
    }
}
