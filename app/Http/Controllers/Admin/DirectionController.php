<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DirectionRequest;
use App\Models\Direction;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DirectionController extends Controller
{
    public function index(): Response
    {
        $items = Direction::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Direction $d): array => [
                'id' => $d->id,
                'title' => $d->getTranslation('title', 'ru'),
                'icon' => $d->icon,
                'stat_value' => $d->stat_value,
            ]);

        return Inertia::render('admin/directions/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/directions/form', $this->formData());
    }

    public function edit(Direction $direction): Response
    {
        return Inertia::render('admin/directions/form', [
            ...$this->formData(),
            'item' => [
                'id' => $direction->id,
                'key' => $direction->key,
                'icon' => $direction->icon,
                'title' => $direction->getTranslations('title'),
                'description' => $direction->getTranslations('description'),
                'stat_value' => $direction->stat_value,
                'stat_label' => $direction->getTranslations('stat_label'),
                'sort_order' => $direction->sort_order,
            ],
        ]);
    }

    public function store(DirectionRequest $request): RedirectResponse
    {
        Direction::create($request->payload());

        return to_route('admin.directions.index')->with('success', 'Направление создано.');
    }

    public function update(DirectionRequest $request, Direction $direction): RedirectResponse
    {
        $direction->update($request->payload());

        return to_route('admin.directions.index')->with('success', 'Направление обновлено.');
    }

    public function destroy(Direction $direction): RedirectResponse
    {
        $direction->delete();

        return to_route('admin.directions.index')->with('success', 'Направление удалено.');
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
