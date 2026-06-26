<?php

namespace App\Http\Requests\Admin;

use App\Enums\PublishStatus;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class NewsRequest extends FormRequest
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
        $newsId = $this->route('news')?->id;

        return [
            'title' => ['required', 'array'],
            'title.ru' => ['required', 'string', 'max:255'],
            'title.tg' => ['nullable', 'string', 'max:255'],
            'title.en' => ['nullable', 'string', 'max:255'],
            'excerpt' => ['nullable', 'array'],
            'excerpt.*' => ['nullable', 'string'],
            'body' => ['nullable', 'array'],
            'body.*' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('news', 'slug')->ignore($newsId)],
            'news_category_id' => ['nullable', 'exists:news_categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'region' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::enum(PublishStatus::class)],
            'published_at' => ['nullable', 'date'],
            'cover' => ['nullable', 'image', 'max:5120'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'title' => $this->input('title'),
            'excerpt' => $this->input('excerpt', []),
            'body' => $this->input('body', []),
            'slug' => $this->input('slug'),
            'news_category_id' => $this->input('news_category_id'),
            'author' => $this->input('author'),
            'region' => $this->input('region'),
            'status' => $this->input('status'),
            'published_at' => $this->input('published_at'),
        ];
    }
}
