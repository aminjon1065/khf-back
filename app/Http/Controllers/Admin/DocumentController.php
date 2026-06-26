<?php

namespace App\Http\Controllers\Admin;

use App\Enums\DocType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DocumentRequest;
use App\Models\Document;
use App\Models\DocumentCategory;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DocumentController extends Controller
{
    public function index(): Response
    {
        $items = Document::query()
            ->with('category')
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Document $d): array => [
                'id' => $d->id,
                'title' => $d->getTranslation('title', 'ru'),
                'category' => $d->category?->getTranslation('name', 'ru'),
                'type' => $d->type?->value,
                'number' => $d->number,
                'document_date' => $d->document_date?->format('d.m.Y'),
            ]);

        return Inertia::render('admin/documents/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/documents/form', $this->formData());
    }

    public function edit(Document $document): Response
    {
        $media = $document->getFirstMedia('file');

        return Inertia::render('admin/documents/form', [
            ...$this->formData(),
            'item' => [
                'id' => $document->id,
                'title' => $document->getTranslations('title'),
                'number' => $document->number,
                'document_date' => $document->document_date?->format('Y-m-d'),
                'size' => $document->size,
                'sort_order' => $document->sort_order,
                'type' => $document->type?->value,
                'document_category_id' => $document->document_category_id,
                'file_name' => $media?->file_name,
                'file_url' => $media?->getUrl(),
            ],
        ]);
    }

    public function store(DocumentRequest $request): RedirectResponse
    {
        $document = Document::create($request->payload());
        $this->syncFile($document, $request);

        return to_route('admin.documents.index')->with('success', 'Документ создан.');
    }

    public function update(DocumentRequest $request, Document $document): RedirectResponse
    {
        $document->update($request->payload());
        $this->syncFile($document, $request);

        return to_route('admin.documents.index')->with('success', 'Документ обновлён.');
    }

    public function destroy(Document $document): RedirectResponse
    {
        $document->delete();

        return to_route('admin.documents.index')->with('success', 'Документ удалён.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
            'categories' => DocumentCategory::query()->orderBy('sort_order')->orderBy('id')->get()
                ->map(fn (DocumentCategory $c): array => [
                    'id' => $c->id,
                    'name' => $c->getTranslation('name', 'ru'),
                ]),
            'types' => collect(DocType::cases())
                ->map(fn (DocType $t): array => ['value' => $t->value, 'label' => $t->value]),
        ];
    }

    private function syncFile(Document $document, DocumentRequest $request): void
    {
        if ($request->hasFile('file')) {
            $document->clearMediaCollection('file');
            $document->addMediaFromRequest('file')->toMediaCollection('file');
        }
    }
}
