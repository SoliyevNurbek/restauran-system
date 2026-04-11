<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $headers = config('security.headers');

        $response->headers->set('Referrer-Policy', (string) ($headers['referrer_policy'] ?? 'strict-origin-when-cross-origin'));
        $response->headers->set('X-Frame-Options', (string) ($headers['frame_options'] ?? 'SAMEORIGIN'));
        $response->headers->set('X-Content-Type-Options', (string) ($headers['content_type_options'] ?? 'nosniff'));
        $response->headers->set('Permissions-Policy', (string) ($headers['permissions_policy'] ?? 'camera=(), microphone=(), geolocation=(), payment=()'));
        $response->headers->set('Cross-Origin-Opener-Policy', (string) ($headers['cross_origin_opener_policy'] ?? 'same-origin'));
        $response->headers->set('Cross-Origin-Resource-Policy', (string) ($headers['cross_origin_resource_policy'] ?? 'same-origin'));

        if ($request->isSecure()) {
            $hsts = 'max-age='.(int) ($headers['hsts_max_age'] ?? 31536000);

            if (! empty($headers['hsts_include_subdomains'])) {
                $hsts .= '; includeSubDomains';
            }

            if (! empty($headers['hsts_preload'])) {
                $hsts .= '; preload';
            }

            $response->headers->set('Strict-Transport-Security', $hsts);
        }

        return $response;
    }
}
