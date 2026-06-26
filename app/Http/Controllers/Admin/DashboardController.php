<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Models\Document;
use App\Models\News;
use App\Models\Report;
use App\Models\Subscription;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('admin/dashboard', [
            'stats' => [
                'news' => News::count(),
                'documents' => Document::count(),
                'reports' => Report::where('status', 'new')->count(),
                'messages' => ContactMessage::where('status', 'new')->count(),
                'subscriptions' => Subscription::count(),
            ],
        ]);
    }
}
