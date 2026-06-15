<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\WmsIntegrationController;




Route::options('{any}', function () {
    return response('', 204)
        ->header('Access-Control-Allow-Origin', env('WMS_URL', 'http://localhost:5173'))
        ->header('Access-Control-Allow-Methods', 'GET, POST, PATCH, DELETE, OPTIONS')
        ->header('Access-Control-Allow-Headers', 'Content-Type, Authorization, X-Requested-With');
})->where('any', '.*');

Route::middleware('wms.token')->group(function () {

    
    Route::get('/products', [WmsIntegrationController::class, 'getProducts']);

    
    Route::patch('/products/{id}/stock', [WmsIntegrationController::class, 'updateStock']);

   
    Route::post('/products/sync-stock', [WmsIntegrationController::class, 'syncStock']);

    
    Route::get('/orders', [WmsIntegrationController::class, 'getOrders']);

    
    Route::patch('/orders/{id}/status', [WmsIntegrationController::class, 'updateOrderStatus']);

    
    Route::get('/summary', [WmsIntegrationController::class, 'summary']);
});
