<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\DepartmentController;
use App\Http\Controllers\Admin\DirectionController;
use App\Http\Controllers\Admin\DocumentCategoryController;
use App\Http\Controllers\Admin\DocumentController;
use App\Http\Controllers\Admin\ForumCategoryController;
use App\Http\Controllers\Admin\ForumTopicController;
use App\Http\Controllers\Admin\HotlineController;
use App\Http\Controllers\Admin\LeaderController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\NewsController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\ProgramController;
use App\Http\Controllers\Admin\RegionalOfficeController;
use App\Http\Controllers\Admin\RegionController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SlideController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ——— Контент ———
Route::middleware('permission:manage news')->group(function () {
    Route::resource('news', NewsController::class)->except('show');
});

Route::middleware('permission:manage documents')->group(function () {
    Route::resource('documents', DocumentController::class)->except('show');
    Route::resource('document-categories', DocumentCategoryController::class)
        ->except('show')
        ->parameters(['document-categories' => 'documentCategory']);
});

Route::middleware('permission:manage structure')->group(function () {
    Route::resource('leaders', LeaderController::class)->except('show');
    Route::resource('departments', DepartmentController::class)->except('show');
    Route::resource('regional-offices', RegionalOfficeController::class)->except('show');
});

Route::middleware('permission:manage activities')->group(function () {
    Route::resource('directions', DirectionController::class)->except('show');
    Route::resource('programs', ProgramController::class)->except('show');
});

Route::middleware('permission:manage forum')->group(function () {
    Route::resource('forum-categories', ForumCategoryController::class)
        ->except('show')
        ->parameters(['forum-categories' => 'forumCategory']);
    Route::resource('forum-topics', ForumTopicController::class)
        ->except('show')
        ->parameters(['forum-topics' => 'forumTopic']);
});

Route::middleware('permission:manage regions')->group(function () {
    Route::resource('regions', RegionController::class)->except('show');
});

Route::middleware('permission:manage contacts')->group(function () {
    Route::resource('hotlines', HotlineController::class)->except('show');
    Route::resource('offices', OfficeController::class)->except('show');
});

Route::middleware('permission:manage home')->group(function () {
    Route::resource('slides', SlideController::class)->except('show');
    Route::resource('services', ServiceController::class)->except('show');
});

// ——— Обращения (чтение + статус + удаление) ———
Route::middleware('permission:manage submissions')->group(function () {
    Route::get('reports', [ReportController::class, 'index'])->name('reports.index');
    Route::patch('reports/{report}/status', [ReportController::class, 'updateStatus'])->name('reports.status');
    Route::delete('reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

    Route::get('messages', [ContactMessageController::class, 'index'])->name('messages.index');
    Route::patch('messages/{contactMessage}/status', [ContactMessageController::class, 'updateStatus'])->name('messages.status');
    Route::delete('messages/{contactMessage}', [ContactMessageController::class, 'destroy'])->name('messages.destroy');

    Route::get('subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::patch('subscriptions/{subscription}/status', [SubscriptionController::class, 'updateStatus'])->name('subscriptions.status');
    Route::delete('subscriptions/{subscription}', [SubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
});

// ——— Медиатека ———
Route::middleware('permission:manage media')->group(function () {
    Route::get('media', [MediaController::class, 'index'])->name('media.index');
    Route::delete('media/{media}', [MediaController::class, 'destroy'])->name('media.destroy');
});

// ——— Настройки (синглтоны) ———
Route::middleware('permission:manage settings')->group(function () {
    Route::get('settings', [SettingController::class, 'edit'])->name('settings.edit');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});

// ——— Пользователи и роли ———
Route::middleware('permission:manage users')->group(function () {
    Route::resource('users', UserController::class)->except('show');
});
