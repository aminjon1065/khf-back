<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentCategoryRequest extends FormRequest
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
        $categoryId = $this->route('document_category')?->id;

        return [
            'name' => ['required', 'array'],
            'name.ru' => ['required', 'string', 'max:255'],
            'name.tg' => ['nullable', 'string', 'max:255'],
            'name.en' => ['nullable', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('document_categories', 'slug')->ignore($categoryId)],
            'sort_order' => ['nullable', 'integer'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'name' => $this->input('name'),
            'slug' => $this->input('slug'),
            'sort_order' => $this->input('sort_order'),
        ];
    }
}
