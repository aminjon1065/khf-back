<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SettingController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('admin/settings/index', [
            'locales' => config('khf.locales'),
            'president' => Setting::get('president'),
            'siteStats' => Setting::get('site_stats'),
            'forumStats' => Setting::get('forum_stats'),
            'mapStats' => Setting::get('map_stats'),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        Setting::put('president', $request->input('president'));
        Setting::put('site_stats', $request->input('site_stats'));
        Setting::put('forum_stats', $request->input('forum_stats'));
        Setting::put('map_stats', $request->input('map_stats'));

        return back()->with('success', 'Настройки сохранены');
    }
}
