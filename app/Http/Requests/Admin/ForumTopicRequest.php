<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ForumTopicRequest extends FormRequest
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
            'forum_category_id' => ['nullable', 'exists:forum_categories,id'],
            'author' => ['nullable', 'string', 'max:255'],
            'replies' => ['nullable', 'integer'],
            'views' => ['nullable', 'integer'],
            'pinned' => ['boolean'],
            'last_activity' => ['nullable', 'string', 'max:255'],
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
            'forum_category_id' => $this->input('forum_category_id'),
            'author' => $this->input('author'),
            'replies' => $this->input('replies'),
            'views' => $this->input('views'),
            'pinned' => $this->boolean('pinned'),
            'last_activity' => $this->input('last_activity'),
            'sort_order' => $this->input('sort_order'),
        ];
    }
}
