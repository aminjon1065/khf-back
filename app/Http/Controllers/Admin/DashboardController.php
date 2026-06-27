<?php

namespace App\Http\Controllers\Admin;

use App\Core\Models\Collection;
use App\Core\Models\Entry;
use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Report;
use App\Models\Subscription;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $newsCollection = Collection::where('slug', 'news')->first();
        $docsCollection = Collection::where('slug', 'documents')->first();

        return Inertia::render('admin/dashboard', [
            'stats' => [
                'news' => $newsCollection ? Entry::where('collection_id', $newsCollection->id)->count() : 0,
                'documents' => $docsCollection ? Entry::where('collection_id', $docsCollection->id)->count() : 0,
                'reports' => Report::where('status', 'new')->count(),
                'messages' => ContactMessage::where('status', 'new')->count(),
                'subscriptions' => Subscription::count(),
            ],
        ]);
    }
}
