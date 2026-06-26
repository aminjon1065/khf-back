<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SlideRequest;
use App\Models\News;
use App\Models\Slide;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class SlideController extends Controller
{
    public function index(): Response
    {
        $items = Slide::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Slide $s): array => [
                'id' => $s->id,
                'category' => $s->getTranslation('category', 'ru'),
                'title' => $s->getTranslation('title', 'ru'),
                'date' => $s->date,
                'source' => $s->source,
                'sort_order' => $s->sort_order,
                'thumb' => $s->getFirstMedia('image')?->getUrl('thumb'),
            ]);

        return Inertia::render('admin/slides/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/slides/form', $this->formData());
    }

    public function edit(Slide $slide): Response
    {
        return Inertia::render('admin/slides/form', [
            ...$this->formData(),
            'item' => [
                'id' => $slide->id,
                'category' => $slide->getTranslations('category'),
                'title' => $slide->getTranslations('title'),
                'date' => $slide->date,
                'source' => $slide->source,
                'sort_order' => $slide->sort_order,
                'news_id' => $slide->news_id,
                'image' => $this->imageUrls($slide),
            ],
        ]);
    }

    public function store(SlideRequest $request): RedirectResponse
    {
        $slide = Slide::create($request->payload());
        $this->syncImage($slide, $request);

        return to_route('admin.slides.index')->with('success', 'Слайд создан.');
    }

    public function update(SlideRequest $request, Slide $slide): RedirectResponse
    {
        $slide->update($request->payload());
        $this->syncImage($slide, $request);

        return to_route('admin.slides.index')->with('success', 'Слайд обновлён.');
    }

    public function destroy(Slide $slide): RedirectResponse
    {
        $slide->delete();

        return to_route('admin.slides.index')->with('success', 'Слайд удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
            'newsOptions' => News::query()
                ->orderBy('id')
                ->get()
                ->map(fn (News $n): array => [
                    'id' => $n->id,
                    'title' => $n->getTranslation('title', 'ru'),
                ]),
        ];
    }

    private function syncImage(Slide $slide, SlideRequest $request): void
    {
        if ($request->hasFile('image')) {
            $slide->clearMediaCollection('image');
            $slide->addMediaFromRequest('image')->toMediaCollection('image');
        }
    }

    /**
     * @return array{thumb:string, card:string, hero:string, original:string}|null
     */
    private function imageUrls(Slide $slide): ?array
    {
        $media = $slide->getFirstMedia('image');

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
