<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class LeaderRequest extends FormRequest
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
            'name' => ['required', 'array'],
            'name.ru' => ['required', 'string', 'max:255'],
            'name.tg' => ['nullable', 'string', 'max:255'],
            'name.en' => ['nullable', 'string', 'max:255'],
            'role' => ['required', 'array'],
            'role.ru' => ['required', 'string', 'max:255'],
            'role.tg' => ['nullable', 'string', 'max:255'],
            'role.en' => ['nullable', 'string', 'max:255'],
            'rank' => ['nullable', 'array'],
            'rank.*' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'array'],
            'bio.*' => ['nullable', 'string'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
            'photo' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'name' => $this->input('name'),
            'role' => $this->input('role'),
            'rank' => $this->input('rank', []),
            'bio' => $this->input('bio', []),
            'sort_order' => $this->input('sort_order', 0),
        ];
    }
}
