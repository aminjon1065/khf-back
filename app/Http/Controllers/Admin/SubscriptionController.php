<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\Subscription;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class SubscriptionController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/subscriptions/index', [
            'items' => Subscription::query()->latest()->get(),
            'statuses' => collect(SubmissionStatus::cases())
                ->map(fn (SubmissionStatus $s): array => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    public function updateStatus(Request $request, Subscription $subscription): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(SubmissionStatus::class)],
        ]);

        $subscription->update(['status' => $request->input('status')]);

        return back()->with('success', 'Статус обновлён');
    }

    public function destroy(Subscription $subscription): RedirectResponse
    {
        $subscription->delete();

        return back()->with('success', 'Удалено');
    }
}
