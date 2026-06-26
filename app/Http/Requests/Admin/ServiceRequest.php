<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ServiceRequest extends FormRequest
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
            'title' => ['required', 'array'],
            'title.ru' => ['required', 'string', 'max:255'],
            'title.tg' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'array'],
            'subtitle.*' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'is_primary' => ['boolean'],
            'tel' => ['nullable', 'string', 'max:255'],
            'route_key' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'title' => $this->input('title'),
            'subtitle' => $this->input('subtitle', []),
            'key' => $this->input('key'),
            'icon' => $this->input('icon'),
            'is_primary' => $this->boolean('is_primary'),
            'tel' => $this->input('tel'),
            'route_key' => $this->input('route_key'),
            'sort_order' => $this->input('sort_order') ?? 0,
        ];
    }
}
