<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\BarangMasuk;
use App\Models\BarangKeluar;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
    {
        // Total penjualan (hanya yang sudah dibayar)
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_price');
        
        // Pesanan baru hari ini
        $newOrdersToday = Order::whereDate('created_at', today())->count();
        
        // Total pesanan
        $totalOrders = Order::count();
        
        // Total produk
        $totalProducts = Product::count();
        
        // Total pelanggan
        $totalCustomers = User::where('is_admin', 0)->count();
        
        // Pesanan pending
        $pendingOrders = Order::where('payment_status', 'pending')->count();
        
        // Produk stok rendah (< 10)
        $lowStockProducts = Product::where('stock', '<', 10)->count();
        
        // Pesanan terbaru (5 terakhir)
        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->take(5)
            ->get();
        
        // Produk terlaris (berdasarkan jumlah order)
        $topProducts = Product::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get()
            ->filter(function($product) {
                return $product->orders_count > 0;
            });
        
        // Statistik bulanan (6 bulan terakhir)
        $monthlyStats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthlyStats[] = [
                'month' => $date->format('M'),
                'orders' => Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->count(),
                'revenue' => Order::whereYear('created_at', $date->year)
                    ->whereMonth('created_at', $date->month)
                    ->where('payment_status', 'paid')
                    ->sum('total_price')
            ];
        }
        
        return view('admin.dashboard', compact(
            'totalRevenue',
            'newOrdersToday',
            'totalOrders',
            'totalProducts',
            'totalCustomers',
            'pendingOrders',
            'lowStockProducts',
            'recentOrders',
            'topProducts',
            'monthlyStats'
        ));
    }

    /**
     * Show report with optional month parameter
     */
    public function report($bulan = null)
    {
        // Jika tidak ada parameter bulan, gunakan bulan saat ini
        if (!$bulan) {
            $bulan = now()->month;
        }
        
        $year = now()->year;
        
        // Data pesanan untuk bulan tersebut
        $orders = Order::whereYear('created_at', $year)
                      ->whereMonth('created_at', $bulan)
                      ->with(['user', 'orderItems.product'])
                      ->get();
        
        // Total revenue bulan tersebut
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total_price');
        
        // Total orders
        $totalOrders = $orders->count();
        
        // Pesanan berdasarkan status
        $ordersByStatus = [
            'pending' => $orders->where('payment_status', 'pending')->count(),
            'paid' => $orders->where('payment_status', 'paid')->count(),
            'failed' => $orders->where('payment_status', 'failed')->count(),
            'expired' => $orders->where('payment_status', 'expired')->count(),
        ];
        
        $monthName = now()->month($bulan)->format('F');
        
        return view('admin.laporan', compact(
            'orders',
            'totalRevenue',
            'totalOrders',
            'ordersByStatus',
            'bulan',
            'monthName',
            'year'
        ));
    }

    // ──────────────────────────────────────────────────────
    // Halaman Dashboard Statistik Gudang
    // ──────────────────────────────────────────────────────
    public function gudang()
    {
        // Produk yang perlu diperhatikan
        $lowStockProducts = Product::where('stock', '<', 10)
            ->orderBy('stock')
            ->get();

        $outOfStockProducts = Product::where('stock', 0)->get();

        $allProducts = Product::orderBy('name')->get();

        // Pesanan yang sudah dibayar & perlu diproses
        $pendingShipmentOrders = Order::with(['user', 'orderItems.product'])
            ->where('status', 'processing')
            ->where('payment_status', 'paid')
            ->latest()
            ->take(20)
            ->get();

        // ── DATA CHART ─────────────────────────────────────
        // 12 bulan terakhir
        $months      = [];
        $masukPerBulan  = [];
        $keluarPerBulan = [];

        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $label = $date->format('M Y');
            $months[] = $label;

            $masukPerBulan[] = BarangMasuk::whereYear('tanggal_masuk', $date->year)
                ->whereMonth('tanggal_masuk', $date->month)
                ->sum('jumlah') ?? 0;

            $keluarPerBulan[] = BarangKeluar::whereYear('tanggal_keluar', $date->year)
                ->whereMonth('tanggal_keluar', $date->month)
                ->sum('jumlah') ?? 0;
        }

        // Top 10 produk berdasarkan stok terbanyak
        $topStokProducts = Product::orderByDesc('stock')
            ->take(10)
            ->get();

        $chartLabels      = $months;
        $chartMasuk       = $masukPerBulan;
        $chartKeluar      = $keluarPerBulan;
        $chartTopStokNames = $topStokProducts->pluck('name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 20))->toArray();
        $chartTopStokData  = $topStokProducts->pluck('stock')->toArray();

        return view('admin.gudang', compact(
            'lowStockProducts',
            'outOfStockProducts',
            'allProducts',
            'pendingShipmentOrders',
            'chartLabels',
            'chartMasuk',
            'chartKeluar',
            'chartTopStokNames',
            'chartTopStokData',
        ));
    }
}