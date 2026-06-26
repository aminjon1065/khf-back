<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class RegionalOfficeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // доступ ограничен permission-middleware на роуте
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'region' => ['required', 'array'],
            'region.ru' => ['required', 'string', 'max:255'],
            'region.tg' => ['nullable', 'string', 'max:255'],
            'region.en' => ['nullable', 'string', 'max:255'],
            'head' => ['nullable', 'array'],
            'head.*' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'array'],
            'address.*' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'region' => $this->input('region'),
            'head' => $this->input('head', []),
            'address' => $this->input('address', []),
            'phone' => $this->input('phone'),
            'sort_order' => $this->input('sort_order', 0),
        ];
    }
}
