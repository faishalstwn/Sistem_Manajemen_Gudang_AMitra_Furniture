<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WmsIntegrationController;

/*
|--------------------------------------------------------------------------
| API Routes - WMS Integration
|--------------------------------------------------------------------------
| Semua route di sini diprotect oleh middleware 'wms.token'
| yang memvalidasi header: Authorization: Bearer {WMS_API_KEY}
|--------------------------------------------------------------------------
*/

// Handle CORS preflight
Route::options('{any}', function () {
    return response('', 204)
        ->header('Access-Control-Allow-Origin', env('WMS_URL', 'http://localhost:5173'))
        ->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

Route::middleware('wms.token')->group(function () {

    // ── PRODUK ─────────────────────────────────────────────────────────
    // Ambil semua produk e-commerce (untuk ditampilkan di WMS)
    Route::get('/products', [WmsIntegrationController::class, 'getProducts']);

    // Update stok produk dari WMS → E-commerce
    // Body: { "stock": 50 }
    Route::patch('/products/{id}/stock', [WmsIntegrationController::class, 'updateStock']);

    // Sinkronisasi massal stok: body: [{ "id": 1, "stock": 10 }, ...]
    Route::post('/products/sync-stock', [WmsIntegrationController::class, 'syncStock']);

    // ── PESANAN ────────────────────────────────────────────────────────
    // Ambil pesanan e-commerce untuk ditampilkan di WMS
    Route::get('/orders', [WmsIntegrationController::class, 'getOrders']);

    // Ubah status pengiriman dari WMS
    // Body: { "status": "shipped" }
    Route::patch('/orders/{id}/status', [WmsIntegrationController::class, 'updateOrderStatus']);

    // ── STATISTIK ──────────────────────────────────────────────────────
    // Ringkasan data e-commerce untuk dashboard WMS
    Route::get('/summary', [WmsIntegrationController::class, 'summary']);
});
