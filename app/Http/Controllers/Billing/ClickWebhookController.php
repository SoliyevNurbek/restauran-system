<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Services\Billing\ClickMerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClickWebhookController extends Controller
{
    public function prepare(Request $request, ClickMerchantService $click): JsonResponse
    {
        return response()->json($click->handlePrepare($request->all()));
    }

    public function complete(Request $request, ClickMerchantService $click): JsonResponse
    {
        return response()->json($click->handleComplete($request->all()));
    }
}
