<?php

namespace App\Http\Controllers\Admin;

use App\Enums\ProgramStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ProgramRequest;
use App\Models\Program;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProgramController extends Controller
{
    public function index(): Response
    {
        $items = Program::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Program $p): array => [
                'id' => $p->id,
                'title' => $p->getTranslation('title', 'ru'),
                'period' => $p->period,
                'status' => $p->status->value,
            ]);

        return Inertia::render('admin/programs/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/programs/form', $this->formData());
    }

    public function edit(Program $program): Response
    {
        return Inertia::render('admin/programs/form', [
            ...$this->formData(),
            'item' => [
                'id' => $program->id,
                'title' => $program->getTranslations('title'),
                'description' => $program->getTranslations('description'),
                'period' => $program->period,
                'status' => $program->status->value,
                'sort_order' => $program->sort_order,
            ],
        ]);
    }

    public function store(ProgramRequest $request): RedirectResponse
    {
        Program::create($request->payload());

        return to_route('admin.programs.index')->with('success', 'Программа создана.');
    }

    public function update(ProgramRequest $request, Program $program): RedirectResponse
    {
        $program->update($request->payload());

        return to_route('admin.programs.index')->with('success', 'Программа обновлена.');
    }

    public function destroy(Program $program): RedirectResponse
    {
        $program->delete();

        return to_route('admin.programs.index')->with('success', 'Программа удалена.');
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(): array
    {
        return [
            'locales' => config('khf.locales'),
            'statuses' => collect(ProgramStatus::cases())
                ->map(fn (ProgramStatus $s): array => ['value' => $s->value, 'label' => $s->value]),
        ];
    }
}
