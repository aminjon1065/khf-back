<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\DepartmentRequest;
use App\Models\Department;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class DepartmentController extends Controller
{
    public function index(): Response
    {
        $items = Department::query()
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get()
            ->map(fn (Department $d): array => [
                'id' => $d->id,
                'title' => $d->getTranslation('title', 'ru'),
                'icon' => $d->icon,
                'sort_order' => $d->sort_order,
            ]);

        return Inertia::render('admin/departments/index', ['items' => $items]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/departments/form', $this->formData());
    }

    public function edit(Department $department): Response
    {
        return Inertia::render('admin/departments/form', [
            ...$this->formData(),
            'item' => [
                'id' => $department->id,
                'title' => $department->getTranslations('title'),
                'description' => $department->getTranslations('description'),
                'head' => $department->getTranslations('head'),
                'icon' => $department->icon,
                'sort_order' => $department->sort_order,
            ],
        ]);
    }

    public function store(DepartmentRequest $request): RedirectResponse
    {
        Department::create($request->payload());

        return to_route('admin.departments.index')->with('success', 'Отдел создан.');
    }

    public function update(DepartmentRequest $request, Department $department): RedirectResponse
    {
        $department->update($request->payload());

        return to_route('admin.departments.index')->with('success', 'Отдел обновлён.');
    }

    public function destroy(Department $department): RedirectResponse
    {
        $department->delete();

        return to_route('admin.departments.index')->with('success', 'Отдел удалён.');
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
