<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\LeaderRequest;
use App\Models\Leader;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class LeaderController extends Controller
{
    public function index(): Response
    {
        $items = Leader::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Leader $l): array => [
                'id' => $l->id,
                'name' => $l->getTranslation('name', 'ru'),
                'role' => $l->getTranslation('role', 'ru'),
                'sort_order' => $l->sort_order,
                'thumb' => $l->getFirstMedia('photo')?->getUrl('thumb'),
            ]);

        return Inertia::render('admin/leaders/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/leaders/form', $this->formData());
    }

    public function edit(Leader $leader): Response
    {
        return Inertia::render('admin/leaders/form', [
            ...$this->formData(),
            'item' => [
                'id' => $leader->id,
                'name' => $leader->getTranslations('name'),
                'role' => $leader->getTranslations('role'),
                'rank' => $leader->getTranslations('rank'),
                'bio' => $leader->getTranslations('bio'),
                'sort_order' => $leader->sort_order,
                'photo' => $this->photoUrls($leader),
            ],
        ]);
    }

    public function store(LeaderRequest $request): RedirectResponse
    {
        $leader = Leader::create($request->payload());
        $this->syncPhoto($leader, $request);

        return to_route('admin.leaders.index')->with('success', 'Руководитель создан.');
    }

    public function update(LeaderRequest $request, Leader $leader): RedirectResponse
    {
        $leader->update($request->payload());
        $this->syncPhoto($leader, $request);

        return to_route('admin.leaders.index')->with('success', 'Руководитель обновлён.');
    }

    public function destroy(Leader $leader): RedirectResponse
    {
        $leader->delete();

        return to_route('admin.leaders.index')->with('success', 'Руководитель удалён.');
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

    private function syncPhoto(Leader $leader, LeaderRequest $request): void
    {
        if ($request->hasFile('photo')) {
            $leader->clearMediaCollection('photo');
            $leader->addMediaFromRequest('photo')->toMediaCollection('photo');
        }
    }

    /**
     * @return array{thumb:string, card:string, hero:string, original:string}|null
     */
    private function photoUrls(Leader $leader): ?array
    {
        $media = $leader->getFirstMedia('photo');

        if ($media === null) {
            return null;
        }

        return [
            'thumb' => $media->getUrl('thumb'),
            'card' => $media->getUrl('card'),
            'hero' => $media->getUrl('hero'),
            'original' => $media->getUrl(),
        ];
    }
}
