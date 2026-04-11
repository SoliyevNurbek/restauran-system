<?php

namespace App\Http\Controllers\Billing;

use App\Http\Controllers\Controller;
use App\Services\Billing\PaymeMerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PaymeWebhookController extends Controller
{
    public function __invoke(Request $request, PaymeMerchantService $payme): JsonResponse
    {
        try {
            return response()->json($payme->handle($request));
        } catch (ValidationException $exception) {
            $payload = json_decode((string) data_get($exception->errors(), 'merchant.0'), true);

            return response()->json([
                'error' => [
                    'code' => $payload['code'] ?? -32504,
                    'message' => $payload['message'] ?? 'Authorization failed',
                ],
                'id' => $request->input('id'),
            ]);
        }
    }
}
