<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class OfficeRequest extends FormRequest
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
            'address' => ['nullable', 'array'],
            'address.*' => ['nullable', 'string'],
            'hours' => ['nullable', 'array'],
            'hours.*' => ['nullable', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'is_head' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'region' => $this->input('region'),
            'address' => $this->input('address', []),
            'hours' => $this->input('hours', []),
            'phone' => $this->input('phone'),
            'email' => $this->input('email'),
            'is_head' => $this->boolean('is_head'),
            'sort_order' => $this->input('sort_order'),
        ];
    }
}
