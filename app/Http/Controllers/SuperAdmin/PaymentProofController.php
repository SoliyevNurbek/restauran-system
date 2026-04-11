<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\SubscriptionPayment;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class PaymentProofController extends Controller
{
    public function __invoke(SubscriptionPayment $payment): Response
    {
        abort_unless(auth()->user()?->isSuperAdmin(), 403);
        abort_unless($payment->proof_file_path, 404);

        $path = 'private/'.$payment->proof_file_path;
        abort_unless(Storage::disk('local')->exists($path), 404);

        $content = Storage::disk('local')->get($path);
        $mime = Storage::disk('local')->mimeType($path) ?: 'image/jpeg';

        return response($content, 200, [
            'Content-Type' => $mime,
            'Cache-Control' => 'private, no-store',
            'Pragma' => 'no-cache',
            'X-Content-Type-Options' => 'nosniff',
            'Content-Disposition' => 'inline; filename="payment-proof-'.$payment->getKey().'"',
        ]);
    }
}
