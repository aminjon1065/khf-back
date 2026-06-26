<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ServiceRequest;
use App\Models\Service;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ServiceController extends Controller
{
    public function index(): Response
    {
        $items = Service::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Service $s): array => [
                'id' => $s->id,
                'title' => $s->getTranslation('title', 'ru'),
                'subtitle' => $s->getTranslation('subtitle', 'ru'),
                'key' => $s->key,
                'icon' => $s->icon,
                'is_primary' => $s->is_primary,
                'tel' => $s->tel,
                'route_key' => $s->route_key,
                'sort_order' => $s->sort_order,
            ]);

        return Inertia::render('admin/services/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/services/form', $this->formData());
    }

    public function edit(Service $service): Response
    {
        return Inertia::render('admin/services/form', [
            ...$this->formData(),
            'item' => [
                'id' => $service->id,
                'title' => $service->getTranslations('title'),
                'subtitle' => $service->getTranslations('subtitle'),
                'key' => $service->key,
                'icon' => $service->icon,
                'is_primary' => $service->is_primary,
                'tel' => $service->tel,
                'route_key' => $service->route_key,
                'sort_order' => $service->sort_order,
            ],
        ]);
    }

    public function store(ServiceRequest $request): RedirectResponse
    {
        Service::create($request->payload());

        return to_route('admin.services.index')->with('success', 'Сервис создан.');
    }

    public function update(ServiceRequest $request, Service $service): RedirectResponse
    {
        $service->update($request->payload());

        return to_route('admin.services.index')->with('success', 'Сервис обновлён.');
    }

    public function destroy(Service $service): RedirectResponse
    {
        $service->delete();

        return to_route('admin.services.index')->with('success', 'Сервис удалён.');
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
