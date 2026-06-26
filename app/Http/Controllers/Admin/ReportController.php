<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ReportController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/reports/index', [
            'items' => Report::query()->latest()->get(),
            'statuses' => collect(SubmissionStatus::cases())
                ->map(fn (SubmissionStatus $s): array => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    public function updateStatus(Request $request, Report $report): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(SubmissionStatus::class)],
        ]);

        $report->update(['status' => $request->input('status')]);

        return back()->with('success', 'Статус обновлён');
    }

    public function destroy(Report $report): RedirectResponse
    {
        $report->delete();

        return back()->with('success', 'Удалено');
    }
}
