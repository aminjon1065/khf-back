<?php

use App\Http\Middleware\EnsureCanAccessAdmin;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'dashboard')->name('dashboard');
});

// Админка (CMS) — только для admin/editor.
Route::middleware(['auth', 'verified', EnsureCanAccessAdmin::class])
    ->prefix('admin')
    ->name('admin.')
    ->group(base_path('routes/admin.php'));

require __DIR__.'/settings.php';
