<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\HotlineRequest;
use App\Models\Hotline;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class HotlineController extends Controller
{
    public function index(): Response
    {
        $items = Hotline::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Hotline $h): array => [
                'id' => $h->id,
                'number' => $h->number,
                'label' => $h->getTranslation('label', 'ru'),
                'is_primary' => $h->is_primary,
            ]);

        return Inertia::render('admin/hotlines/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/hotlines/form', $this->formData());
    }

    public function edit(Hotline $hotline): Response
    {
        return Inertia::render('admin/hotlines/form', [
            ...$this->formData(),
            'item' => [
                'id' => $hotline->id,
                'number' => $hotline->number,
                'label' => $hotline->getTranslations('label'),
                'note' => $hotline->getTranslations('note'),
                'is_primary' => $hotline->is_primary,
                'sort_order' => $hotline->sort_order,
            ],
        ]);
    }

    public function store(HotlineRequest $request): RedirectResponse
    {
        Hotline::create($request->payload());

        return to_route('admin.hotlines.index')->with('success', 'Горячая линия создана.');
    }

    public function update(HotlineRequest $request, Hotline $hotline): RedirectResponse
    {
        $hotline->update($request->payload());

        return to_route('admin.hotlines.index')->with('success', 'Горячая линия обновлена.');
    }

    public function destroy(Hotline $hotline): RedirectResponse
    {
        $hotline->delete();

        return to_route('admin.hotlines.index')->with('success', 'Горячая линия удалена.');
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
