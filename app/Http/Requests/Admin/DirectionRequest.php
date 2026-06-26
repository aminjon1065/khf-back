<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DirectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
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
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'stat_label' => ['nullable', 'array'],
            'stat_label.*' => ['nullable', 'string', 'max:255'],
            'key' => ['nullable', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'stat_value' => ['nullable', 'string', 'max:255'],
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
            'description' => $this->input('description', []),
            'stat_label' => $this->input('stat_label', []),
            'key' => $this->input('key'),
            'icon' => $this->input('icon'),
            'stat_value' => $this->input('stat_value'),
            'sort_order' => $this->input('sort_order'),
        ];
    }
}
