<?php

use App\Http\Controllers\Api\ActivitiesController;
use App\Http\Controllers\Api\ContactsController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\FormController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\MapController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\StructureController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Middleware\EnsureFrontendRequest;
use App\Http\Middleware\SetLocaleFromRequest;
use App\Modules\Identity\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Публичный API только для фронтенда (Next.js): закрыт токеном,
// локаль из Accept-Language. Базовый префикс — /api/v1.
Route::prefix('v1/{locale}')
    ->middleware([EnsureFrontendRequest::class, SetLocaleFromRequest::class])
    ->group(function () {
        // ——— Новости ———
        Route::get('news', [NewsController::class, 'index']);
        Route::get('news/{slug}', [NewsController::class, 'show']);
        Route::get('news/{slug}/related', [NewsController::class, 'related']);

        // ——— Контент-разделы (агрегаты) ———
        Route::get('structure', [StructureController::class, 'index']);
        Route::get('activities', [ActivitiesController::class, 'index']);
        Route::get('documents', [DocumentController::class, 'index']);
        Route::get('forum', [ForumController::class, 'index']);
        Route::get('regions', [MapController::class, 'index']);
        Route::get('contacts', [ContactsController::class, 'index']);
        Route::get('home', [HomeController::class, 'index']);
        Route::get('home/slides', [HomeController::class, 'slides']);

        // ——— Формы (запись, троттлинг) ———
        Route::middleware('throttle:20,1')->group(function () {
            Route::post('reports', [FormController::class, 'report']);
            Route::post('contact', [FormController::class, 'contact']);
            Route::post('subscriptions', [FormController::class, 'subscribe']);
        });

        // ——— Headless CMS (Dynamic Data) ———
        Route::get('/{collection:slug}', [ContentController::class, 'index']);
        Route::get('/{collection:slug}/{entry:slug}', [ContentController::class, 'show']);
    });

// Authenticated identity endpoint (Sanctum token or session). The single
// authenticated API surface for the IAM module — returns the current user.
Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return new UserResource($request->user()->loadMissing('avatar'));
});
