<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ForumCategoryRequest extends FormRequest
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
            'slug' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'string', 'max:255'],
            'topics_count' => ['nullable', 'integer'],
            'posts_count' => ['nullable', 'integer'],
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
            'slug' => $this->input('slug'),
            'icon' => $this->input('icon'),
            'topics_count' => $this->input('topics_count'),
            'posts_count' => $this->input('posts_count'),
            'sort_order' => $this->input('sort_order'),
        ];
    }
}
