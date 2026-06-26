<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentCategoryRequest;
use App\Models\DocumentCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DocumentCategoryController extends Controller
{
    public function index(): Response
    {
        $items = DocumentCategory::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (DocumentCategory $c): array => [
                'id' => $c->id,
                'name' => $c->getTranslation('name', 'ru'),
                'slug' => $c->slug,
            ]);

        return Inertia::render('admin/document-categories/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/document-categories/form', $this->formData());
    }

    public function edit(DocumentCategory $documentCategory): Response
    {
        return Inertia::render('admin/document-categories/form', [
            ...$this->formData(),
            'item' => [
                'id' => $documentCategory->id,
                'name' => $documentCategory->getTranslations('name'),
                'slug' => $documentCategory->slug,
                'sort_order' => $documentCategory->sort_order,
            ],
        ]);
    }

    public function store(DocumentCategoryRequest $request): RedirectResponse
    {
        DocumentCategory::create($request->payload());

        return to_route('admin.document-categories.index')->with('success', 'Категория создана.');
    }

    public function update(DocumentCategoryRequest $request, DocumentCategory $documentCategory): RedirectResponse
    {
        $documentCategory->update($request->payload());

        return to_route('admin.document-categories.index')->with('success', 'Категория обновлена.');
    }

    public function destroy(DocumentCategory $documentCategory): RedirectResponse
    {
        $documentCategory->delete();

        return to_route('admin.document-categories.index')->with('success', 'Категория удалена.');
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
