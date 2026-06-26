<?php

namespace App\Http\Requests\Admin;

use App\Enums\DocType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DocumentRequest extends FormRequest
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
            'number' => ['nullable', 'string', 'max:255'],
            'document_date' => ['nullable', 'date'],
            'size' => ['nullable', 'string', 'max:255'],
            'sort_order' => ['nullable', 'integer'],
            'type' => ['nullable', Rule::enum(DocType::class)],
            'document_category_id' => ['nullable', 'exists:document_categories,id'],
            'file' => ['nullable', 'file', 'max:20480'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function payload(): array
    {
        return [
            'title' => $this->input('title'),
            'number' => $this->input('number'),
            'document_date' => $this->input('document_date'),
            'size' => $this->input('size'),
            'sort_order' => $this->input('sort_order'),
            'type' => $this->input('type'),
            'document_category_id' => $this->input('document_category_id'),
        ];
    }
}
