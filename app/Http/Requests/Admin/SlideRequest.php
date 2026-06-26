<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class SlideRequest extends FormRequest
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
            'category' => ['nullable', 'array'],
            'category.*' => ['nullable', 'string', 'max:255'],
            'title' => ['required', 'array'],
            'title.ru' => ['required', 'string', 'max:255'],
            'title.tg' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'date' => ['nullable', 'string', 'max:255'],
            'source' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'news_id' => ['nullable', 'exists:news,id'],
            'image' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'category' => $this->input('category', []),
            'title' => $this->input('title'),
            'date' => $this->input('date'),
            'source' => $this->input('source'),
            'sort_order' => $this->input('sort_order') ?? 0,
            'news_id' => $this->input('news_id'),
        ];
    }
}
