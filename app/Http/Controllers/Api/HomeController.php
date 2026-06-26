<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\SlideResource;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Slide;
use Illuminate\Http\JsonResponse;

class HomeController extends Controller
{
    /**
     * GET /api/v1/home — агрегат главной страницы (сервисы, президент, статистика).
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => [
                'services' => ServiceResource::collection(Service::ordered()->get()),
                'president' => $this->president(),
                'stats' => Setting::get('site_stats'),
            ],
        ]);
    }

    /**
     * GET /api/v1/home/slides — слайды героя.
     */
    public function slides(): JsonResponse
    {
        return response()->json([
            'data' => SlideResource::collection(Slide::with('news')->ordered()->get()),
        ]);
    }

    /**
     * Цитата Президента, локализованная по текущей локали запроса.
     *
     * @return array<string, mixed>|null
     */
    private function president(): ?array
    {
        /** @var array<string, mixed>|null $p */
        $p = Setting::get('president');

        if ($p === null) {
            return null;
        }

        $locale = app()->getLocale();

        return [
            'name' => $p['name'],
            'role' => $p['role'][$locale] ?? $p['role']['tg'],
            'quote' => $p['quote'][$locale] ?? $p['quote']['tg'],
            'href' => $p['href'],
        ];
    }
}
