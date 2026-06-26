<?php

namespace App\Http\Requests\Admin;

use App\Enums\RiskLevel;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegionRequest extends FormRequest
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
        $regionId = $this->route('region')?->id;

        return [
            'name' => ['required', 'array'],
            'name.ru' => ['required', 'string', 'max:255'],
            'name.tg' => ['nullable', 'string', 'max:255'],
            'name.en' => ['nullable', 'string', 'max:255'],
            'center' => ['nullable', 'array'],
            'center.*' => ['nullable', 'string', 'max:255'],
            'note' => ['nullable', 'array'],
            'note.*' => ['nullable', 'string'],
            'slug' => ['required', 'string', 'max:255', Rule::unique('regions', 'slug')->ignore($regionId)],
            'risk' => ['required', Rule::enum(RiskLevel::class)],
            'active_incidents' => ['nullable', 'integer', 'min:0'],
            'stations' => ['nullable', 'integer', 'min:0'],
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
            'center' => $this->input('center', []),
            'note' => $this->input('note', []),
            'slug' => $this->input('slug'),
            'risk' => $this->input('risk'),
            'active_incidents' => $this->input('active_incidents', 0),
            'stations' => $this->input('stations', 0),
            'sort_order' => $this->input('sort_order', 0),
        ];
    }
}
