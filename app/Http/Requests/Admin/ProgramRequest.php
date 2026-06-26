<?php

namespace App\Http\Requests\Admin;

use App\Enums\ProgramStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProgramRequest extends FormRequest
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
            'period' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(ProgramStatus::class)],
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
            'period' => $this->input('period'),
            'status' => $this->input('status'),
            'sort_order' => $this->input('sort_order'),
        ];
    }
}
