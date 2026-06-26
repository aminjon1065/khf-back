<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\ContactStoreRequest;
use App\Http\Requests\Api\ReportStoreRequest;
use App\Http\Requests\Api\SubscribeStoreRequest;
use App\Models\ContactMessage;
use App\Models\Report;
use App\Models\Subscription;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class FormController extends Controller
{
    /**
     * POST /api/v1/forms/report — приём сообщения о ЧС.
     */
    public function report(ReportStoreRequest $request): JsonResponse
    {
        $report = Report::create([
            ...$request->validated(),
            'reference' => 'ЧС-'.date('Y').'-'.Str::upper(Str::random(6)),
        ]);

        return response()->json(['ok' => true, 'reference' => $report->reference], 201);
    }

    /**
     * POST /api/v1/forms/contact — обратная связь.
     */
    public function contact(ContactStoreRequest $request): JsonResponse
    {
        ContactMessage::create($request->validated());

        return response()->json(['ok' => true], 201);
    }

    /**
     * POST /api/v1/forms/subscribe — подписка на оповещения.
     */
    public function subscribe(SubscribeStoreRequest $request): JsonResponse
    {
        Subscription::create($request->validated());

        return response()->json(['ok' => true], 201);
    }
}
