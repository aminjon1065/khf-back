<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\OfficeRequest;
use App\Models\Office;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class OfficeController extends Controller
{
    public function index(): Response
    {
        $items = Office::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Office $o): array => [
                'id' => $o->id,
                'region' => $o->getTranslation('region', 'ru'),
                'phone' => $o->phone,
                'is_head' => $o->is_head,
            ]);

        return Inertia::render('admin/offices/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/offices/form', $this->formData());
    }

    public function edit(Office $office): Response
    {
        return Inertia::render('admin/offices/form', [
            ...$this->formData(),
            'item' => [
                'id' => $office->id,
                'region' => $office->getTranslations('region'),
                'address' => $office->getTranslations('address'),
                'hours' => $office->getTranslations('hours'),
                'phone' => $office->phone,
                'email' => $office->email,
                'is_head' => $office->is_head,
                'sort_order' => $office->sort_order,
            ],
        ]);
    }

    public function store(OfficeRequest $request): RedirectResponse
    {
        Office::create($request->payload());

        return to_route('admin.offices.index')->with('success', 'Офис создан.');
    }

    public function update(OfficeRequest $request, Office $office): RedirectResponse
    {
        $office->update($request->payload());

        return to_route('admin.offices.index')->with('success', 'Офис обновлён.');
    }

    public function destroy(Office $office): RedirectResponse
    {
        $office->delete();

        return to_route('admin.offices.index')->with('success', 'Офис удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
        ];
    }
}
