<?php

namespace App\Http\Controllers\Admin;

use App\Enums\SubmissionStatus;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class ContactMessageController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/messages/index', [
            'items' => ContactMessage::query()->latest()->get(),
            'statuses' => collect(SubmissionStatus::cases())
                ->map(fn (SubmissionStatus $s): array => ['value' => $s->value, 'label' => $s->label()]),
        ]);
    }

    public function updateStatus(Request $request, ContactMessage $contactMessage): RedirectResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(SubmissionStatus::class)],
        ]);

        $contactMessage->update(['status' => $request->input('status')]);

        return back()->with('success', 'Статус обновлён');
    }

    public function destroy(ContactMessage $contactMessage): RedirectResponse
    {
        $contactMessage->delete();

        return back()->with('success', 'Удалено');
    }
}
