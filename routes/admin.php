<?php

use App\Http\Controllers\Admin\ContactMessageController;
use App\Http\Controllers\Admin\Content\EntryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\MediaController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\Schema\BlueprintController;
use App\Http\Controllers\Admin\Schema\CollectionController;
use App\Http\Controllers\Admin\Schema\FieldController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

// ——— Конструктор схем (Schema Builder) ———
Route::middleware('permission:manage schema')->prefix('schema')->name('schema.')->group(function () {
    Route::resource('collections', CollectionController::class);
    Route::resource('collections.blueprints', BlueprintController::class)->shallow();
    Route::resource('blueprints.fields', FieldController::class)->shallow();
});

// ——— Динамический Контент (Entries) ———
Route::middleware('permission:manage content')->name('content.')->group(function () {
    Route::resource('collections.entries', EntryController::class)->shallow();
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
    Route::post('media', [MediaController::class, 'store'])->name('media.store');
    Route::delete('media/{medium}', [MediaController::class, 'destroy'])->name('media.destroy');
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
