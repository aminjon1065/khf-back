<?php

namespace App\Http\Controllers\Admin;

use App\Enums\RiskLevel;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegionRequest;
use App\Models\Region;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegionController extends Controller
{
    public function index(): Response
    {
        $items = Region::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Region $r): array => [
                'id' => $r->id,
                'name' => $r->getTranslation('name', 'ru'),
                'risk' => $r->risk->value,
                'active_incidents' => $r->active_incidents,
                'stations' => $r->stations,
            ]);

        return Inertia::render('admin/regions/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/regions/form', $this->formData());
    }

    public function edit(Region $region): Response
    {
        return Inertia::render('admin/regions/form', [
            ...$this->formData(),
            'item' => [
                'id' => $region->id,
                'name' => $region->getTranslations('name'),
                'center' => $region->getTranslations('center'),
                'note' => $region->getTranslations('note'),
                'slug' => $region->slug,
                'risk' => $region->risk->value,
                'active_incidents' => $region->active_incidents,
                'stations' => $region->stations,
                'sort_order' => $region->sort_order,
            ],
        ]);
    }

    public function store(RegionRequest $request): RedirectResponse
    {
        Region::create($request->payload());

        return to_route('admin.regions.index')->with('success', 'Регион создан.');
    }

    public function update(RegionRequest $request, Region $region): RedirectResponse
    {
        $region->update($request->payload());

        return to_route('admin.regions.index')->with('success', 'Регион обновлён.');
    }

    public function destroy(Region $region): RedirectResponse
    {
        $region->delete();

        return to_route('admin.regions.index')->with('success', 'Регион удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
            'risks' => [
                ['value' => RiskLevel::Low->value, 'label' => 'Низкий'],
                ['value' => RiskLevel::Medium->value, 'label' => 'Средний'],
                ['value' => RiskLevel::High->value, 'label' => 'Высокий'],
            ],
        ];
    }
}
