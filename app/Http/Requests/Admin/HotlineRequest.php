<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class HotlineRequest extends FormRequest
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
            'label' => ['required', 'array'],
            'label.ru' => ['required', 'string', 'max:255'],
            'label.tg' => ['nullable', 'string', 'max:255'],
            'label.en' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'array'],
            'note.*' => ['nullable', 'string'],
            'number' => ['required', 'string', 'max:255'],
            'is_primary' => ['boolean'],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'label' => $this->input('label'),
            'note' => $this->input('note', []),
            'number' => $this->input('number'),
            'is_primary' => $this->boolean('is_primary'),
            'sort_order' => $this->input('sort_order'),
        ];
    }
}
