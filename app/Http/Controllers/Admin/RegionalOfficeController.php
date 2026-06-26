<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\RegionalOfficeRequest;
use App\Models\RegionalOffice;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class RegionalOfficeController extends Controller
{
    public function index(): Response
    {
        $items = RegionalOffice::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (RegionalOffice $o): array => [
                'id' => $o->id,
                'region' => $o->getTranslation('region', 'ru'),
                'phone' => $o->phone,
                'sort_order' => $o->sort_order,
            ]);

        return Inertia::render('admin/regional-offices/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/regional-offices/form', $this->formData());
    }

    public function edit(RegionalOffice $regional_office): Response
    {
        return Inertia::render('admin/regional-offices/form', [
            ...$this->formData(),
            'item' => [
                'id' => $regional_office->id,
                'region' => $regional_office->getTranslations('region'),
                'head' => $regional_office->getTranslations('head'),
                'address' => $regional_office->getTranslations('address'),
                'phone' => $regional_office->phone,
                'sort_order' => $regional_office->sort_order,
            ],
        ]);
    }

    public function store(RegionalOfficeRequest $request): RedirectResponse
    {
        RegionalOffice::create($request->payload());

        return to_route('admin.regional-offices.index')->with('success', 'Региональное отделение создано.');
    }

    public function update(RegionalOfficeRequest $request, RegionalOffice $regional_office): RedirectResponse
    {
        $regional_office->update($request->payload());

        return to_route('admin.regional-offices.index')->with('success', 'Региональное отделение обновлено.');
    }

    public function destroy(RegionalOffice $regional_office): RedirectResponse
    {
        $regional_office->delete();

        return to_route('admin.regional-offices.index')->with('success', 'Региональное отделение удалено.');
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
