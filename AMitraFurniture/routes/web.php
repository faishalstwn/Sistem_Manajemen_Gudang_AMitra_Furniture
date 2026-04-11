<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\AdminProductController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminWarehouseController;
use App\Http\Controllers\AdminBarangMasukController;
use App\Http\Controllers\AdminBarangKeluarController;
use App\Http\Controllers\AdminExportController;
use App\Http\Controllers\AdminLokasiGudangController;

/*
|--------------------------------------------------------------------------
| HOME (USER)
|--------------------------------------------------------------------------
*/
Route::get('/', [HomeController::class, 'index'])->name('home');

/*
|--------------------------------------------------------------------------
| AUTH USER
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.store');

    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.store');

    // Google OAuth Routes
    Route::get('/auth/google', [AuthController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [AuthController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| PRODUCT (USER)
|--------------------------------------------------------------------------
*/
Route::get('/produk', [ProductController::class, 'index'])->name('products.index');
Route::get('/produk/{slug}', [ProductController::class, 'show'])->name('products.show');
Route::get('/kategori/{category}', [HomeController::class, 'category'])->name('category.show');
Route::get('/produk/{id}/detail', [ProductController::class, 'detail'])->name('products.detail');

// SEARCH WITH OPTIONAL PARAMETER
Route::get('/search/{keyword?}', [ProductController::class, 'search'])->name('search');

/*
|--------------------------------------------------------------------------
| CART
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('cart.add');
    Route::patch('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'remove'])->name('cart.remove');
    Route::delete('/cart/{cart}/destroy', [CartController::class, 'destroy'])->name('cart.destroy');
});

/*
|--------------------------------------------------------------------------
| USER AREA
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/profil', [ProfileController::class, 'show'])->name('profile.show');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profile.update');
    Route::patch('/profil/password', [ProfileController::class, 'updatePassword'])->name('profile.updatePassword');

    Route::get('/checkout', [OrderController::class, 'checkout'])->name('orders.checkout');
    Route::get('/checkout/{id}', [ProductController::class, 'checkout'])->name('checkout.direct');

    Route::post('/checkout/{id}/pay', [OrderController::class, 'pay'])
        ->name('checkout.pay');

    Route::post('/checkout', [OrderController::class, 'store'])->name('orders.store');
    Route::post('/checkout/direct', [OrderController::class, 'storeDirect'])->name('orders.storeDirect');

    Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
    Route::get('/orders/{order}/track', [OrderController::class, 'track'])->name('orders.track');
    Route::post('/orders/{order}/confirm-payment', [OrderController::class, 'confirmPayment'])->name('orders.confirmPayment');
    
    // FILTER ORDERS WITH OPTIONAL STATUS
    Route::get('/orders/filter/{status?}', [OrderController::class, 'filter'])->name('orders.filter');

    // REVIEWS
    Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');

    // Payment Pages
    Route::get('/payment/page/{order}', [OrderController::class, 'paymentPage'])
        ->name('payment.page');

    Route::get('/payment/finish/{order}', [OrderController::class, 'finishPayment'])
        ->name('payment.finish');

    Route::get('/payment/success/{order}', [OrderController::class, 'paymentSuccess'])
        ->name('payment.success');
});

/*
|--------------------------------------------------------------------------
| MIDTRANS CALLBACK
|--------------------------------------------------------------------------
*/
Route::post('/payment/callback', [OrderController::class, 'callback'])
    ->name('payment.callback');

/*
|--------------------------------------------------------------------------
| ================= ADMIN AREA =================
|--------------------------------------------------------------------------
| ADMIN TERPISAH & DIAMANKAN
|--------------------------------------------------------------------------
*/

// LOGIN ADMIN
Route::get('/admin/login', [AuthController::class, 'loginForm'])
    ->name('admin.login');

Route::post('/admin/login', [AuthController::class, 'login'])
    ->name('admin.login.store');

// REGISTER ADMIN
Route::get('/admin/register', [AuthController::class, 'registerForm'])
    ->name('admin.register');

Route::post('/admin/register', [AuthController::class, 'register'])
    ->name('admin.register.store');

// ADMIN AREA
Route::middleware(['auth', 'admin'])->group(function () {

    // DASHBOARD + GRAFIK
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])
        ->name('admin.dashboard');
    
    // LAPORAN WITH OPTIONAL MONTH
    Route::get('/admin/laporan/{bulan?}', [AdminDashboardController::class, 'report'])
        ->name('admin.laporan');

    // PESANAN (UBAH STATUS)
    Route::get('/admin/pesanan', [AdminOrderController::class, 'index'])
        ->name('admin.pesanan.index');

    Route::get('/admin/pesanan/{order}/edit', [AdminOrderController::class, 'edit'])
        ->name('admin.pesanan.edit');

    Route::put('/admin/pesanan/{order}', [AdminOrderController::class, 'update'])
        ->name('admin.pesanan.update');

    // PRODUK (CRUD + GAMBAR)
    Route::get('/admin/produk', [AdminProductController::class, 'index'])
        ->name('admin.produk.index');

    Route::get('/admin/produk/create', [AdminProductController::class, 'create'])
        ->name('admin.produk.create');

    Route::post('/admin/produk', [AdminProductController::class, 'store'])
        ->name('admin.produk.store');

    Route::get('/admin/produk/{product}/edit', [AdminProductController::class, 'edit'])
        ->name('admin.produk.edit');

    Route::put('/admin/produk/{product}', [AdminProductController::class, 'update'])
        ->name('admin.produk.update');

    Route::delete('/admin/produk/{product}', [AdminProductController::class, 'destroy'])
        ->name('admin.produk.destroy');

    // PENGIRIMAN & NOTIFIKASI (UI)
    Route::get('/admin/pengiriman', [AdminOrderController::class, 'shipping'])
        ->name('admin.pengiriman');

    Route::get('/admin/notifikasi', fn () => view('admin.notifikasi'))
        ->name('admin.notifikasi');

    // ── WMS MONITOR ──────────────────────────────────────────────────────
    Route::get('/admin/gudang', [AdminDashboardController::class, 'gudang'])
        ->name('admin.gudang');

    // ── WAREHOUSE MANAGEMENT (CRUD STOK) ──────────────────────────────
    Route::get('/admin/gudang/kelola', [AdminWarehouseController::class, 'index'])
        ->name('admin.gudang.kelola');

    Route::get('/admin/gudang/riwayat', [AdminWarehouseController::class, 'riwayat'])
        ->name('admin.gudang.riwayat');

    // Stok Masuk
    Route::get('/admin/gudang/{product}/stok-masuk', [AdminWarehouseController::class, 'stokMasukForm'])
        ->name('admin.gudang.stok-masuk.form');
    Route::post('/admin/gudang/{product}/stok-masuk', [AdminWarehouseController::class, 'stokMasuk'])
        ->name('admin.gudang.stok-masuk');

    // Stok Keluar
    Route::get('/admin/gudang/{product}/stok-keluar', [AdminWarehouseController::class, 'stokKeluarForm'])
        ->name('admin.gudang.stok-keluar.form');
    Route::post('/admin/gudang/{product}/stok-keluar', [AdminWarehouseController::class, 'stokKeluar'])
        ->name('admin.gudang.stok-keluar');

    // Koreksi / Adjustment
    Route::get('/admin/gudang/{product}/adjustment', [AdminWarehouseController::class, 'adjustmentForm'])
        ->name('admin.gudang.adjustment.form');
    Route::post('/admin/gudang/{product}/adjustment', [AdminWarehouseController::class, 'adjustment'])
        ->name('admin.gudang.adjustment');

    // ── BARANG MASUK ──────────────────────────────────────────────────
    Route::get('/admin/barang-masuk', [AdminBarangMasukController::class, 'index'])
        ->name('admin.barang-masuk.index');
    Route::get('/admin/barang-masuk/create', [AdminBarangMasukController::class, 'create'])
        ->name('admin.barang-masuk.create');
    Route::post('/admin/barang-masuk', [AdminBarangMasukController::class, 'store'])
        ->name('admin.barang-masuk.store');

    // ── BARANG KELUAR ─────────────────────────────────────────────────
    Route::get('/admin/barang-keluar', [AdminBarangKeluarController::class, 'index'])
        ->name('admin.barang-keluar.index');
    Route::get('/admin/barang-keluar/create', [AdminBarangKeluarController::class, 'create'])
        ->name('admin.barang-keluar.create');
    Route::post('/admin/barang-keluar', [AdminBarangKeluarController::class, 'store'])
        ->name('admin.barang-keluar.store');

    // ── EXPORT LAPORAN ────────────────────────────────────────────────
    Route::get('/admin/export/excel/stok', [AdminExportController::class, 'excelStok'])
        ->name('admin.export.excel.stok');

    // ── PETA LOKASI GUDANG ────────────────────────────────────────────
    Route::get('/admin/lokasi-gudang/peta', [AdminLokasiGudangController::class, 'peta'])
        ->name('admin.lokasi-gudang.peta');
    Route::get('/admin/lokasi-gudang', [AdminLokasiGudangController::class, 'index'])
        ->name('admin.lokasi-gudang.index');
    Route::get('/admin/lokasi-gudang/create', [AdminLokasiGudangController::class, 'create'])
        ->name('admin.lokasi-gudang.create');
    Route::post('/admin/lokasi-gudang', [AdminLokasiGudangController::class, 'store'])
        ->name('admin.lokasi-gudang.store');
    Route::get('/admin/lokasi-gudang/{location}', [AdminLokasiGudangController::class, 'show'])
        ->name('admin.lokasi-gudang.show');
    Route::get('/admin/lokasi-gudang/{location}/edit', [AdminLokasiGudangController::class, 'edit'])
        ->name('admin.lokasi-gudang.edit');
    Route::put('/admin/lokasi-gudang/{location}', [AdminLokasiGudangController::class, 'update'])
        ->name('admin.lokasi-gudang.update');
    Route::delete('/admin/lokasi-gudang/{location}', [AdminLokasiGudangController::class, 'destroy'])
        ->name('admin.lokasi-gudang.destroy');
    Route::post('/admin/lokasi-gudang/{location}/assign', [AdminLokasiGudangController::class, 'assignProduct'])
        ->name('admin.lokasi-gudang.assign');
    Route::delete('/admin/lokasi-gudang/{location}/remove/{product}', [AdminLokasiGudangController::class, 'removeProduct'])
        ->name('admin.lokasi-gudang.remove');
    Route::patch('/admin/lokasi-gudang/{location}/qty/{product}', [AdminLokasiGudangController::class, 'updateProductQty'])
        ->name('admin.lokasi-gudang.update-qty');
    Route::get('/admin/export/excel/barang-masuk', [AdminExportController::class, 'excelBarangMasuk'])
        ->name('admin.export.excel.barang-masuk');
    Route::get('/admin/export/excel/barang-keluar', [AdminExportController::class, 'excelBarangKeluar'])
        ->name('admin.export.excel.barang-keluar');
    Route::get('/admin/export/pdf/stok', [AdminExportController::class, 'pdfStok'])
        ->name('admin.export.pdf.stok');
    Route::get('/admin/export/pdf/barang-masuk', [AdminExportController::class, 'pdfBarangMasuk'])
        ->name('admin.export.pdf.barang-masuk');
    Route::get('/admin/export/pdf/barang-keluar', [AdminExportController::class, 'pdfBarangKeluar'])
        ->name('admin.export.pdf.barang-keluar');
});
