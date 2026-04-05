<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class WmsTokenMiddleware
{
    /**
     * Validasi bahwa request dari WMS menggunakan token yang benar.
     * Header yang diharapkan: Authorization: Bearer {WMS_API_KEY}
     */
    public function handle(Request $request, Closure $next): Response
    {
        $expectedToken = env('WMS_API_KEY');

        // Ambil token dari header Authorization: Bearer <token>
        $authHeader = $request->header('Authorization', '');
        $token = null;

        if (str_starts_with($authHeader, 'Bearer ')) {
            $token = substr($authHeader, 7);
        }

        // Fallback: cek query param ?api_key=...
        if (! $token) {
            $token = $request->query('api_key');
        }

        if (! $token || $token !== $expectedToken) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized. Token WMS tidak valid.',
            ], 401);
        }

        // Tambahkan CORS headers untuk React WMS frontend
        $response = $next($request);
        $response->headers->set('Access-Control-Allow-Origin', env('WMS_URL', 'http://localhost:5173'));
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS');
        $response->headers->set('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');

        return $response;
    }
}
