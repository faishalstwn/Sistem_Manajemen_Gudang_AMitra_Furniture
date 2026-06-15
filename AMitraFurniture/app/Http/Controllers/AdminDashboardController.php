<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $totalRevenue = Order::where('payment_status', 'paid')->sum('total_price');
        $newOrdersToday = Order::whereDate('created_at', today())->count();
        $totalOrders = Order::count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('is_admin', 0)->count();
        $pendingOrders = Order::where('payment_status', 'pending')->count();
        $lowStockProducts = Product::where('stock', '<', 10)->count();
        
        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->latest()
            ->take(5)
            ->get();
        
        $topProducts = Product::withCount('orders')
            ->orderBy('orders_count', 'desc')
            ->take(5)
            ->get()
            ->filter(function($product) {
                return $product->orders_count > 0;
            })->values();
        
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
        
        return Inertia::render('Admin/Dashboard', [
            'totalRevenue' => $totalRevenue,
            'newOrdersToday' => $newOrdersToday,
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'pendingOrders' => $pendingOrders,
            'lowStockProducts' => $lowStockProducts,
            'recentOrders' => $recentOrders,
            'topProducts' => $topProducts,
            'monthlyStats' => $monthlyStats,
        ]);
    }

    /**
     * Show report with optional month parameter
     */
    public function report($bulan = null)
    {
        $bulan = $bulan ? (int) $bulan : now()->month;
        if ($bulan < 1 || $bulan > 12) {
            $bulan = now()->month;
        }
        
        $year = now()->year;
        
        $orders = Order::whereYear('created_at', $year)
                      ->whereMonth('created_at', $bulan)
                      ->with(['user', 'orderItems.product'])
                      ->get();
        
        $totalRevenue = $orders->where('payment_status', 'paid')->sum('total_price');
        $totalOrders = $orders->count();
        
        $ordersByStatus = [
            'pending' => $orders->where('payment_status', 'pending')->count(),
            'paid' => $orders->where('payment_status', 'paid')->count(),
            'failed' => $orders->where('payment_status', 'failed')->count(),
            'expired' => $orders->where('payment_status', 'expired')->count(),
        ];
        
        $monthName = now()->month($bulan)->format('F');
        
        return Inertia::render('Admin/Laporan', [
            'orders' => $orders,
            'totalRevenue' => $totalRevenue,
            'totalOrders' => $totalOrders,
            'ordersByStatus' => $ordersByStatus,
            'bulan' => $bulan,
            'monthName' => $monthName,
            'year' => $year,
        ]);
    }

    // ──────────────────────────────────────────────────────
    // Halaman Dashboard Statistik Gudang
    // ──────────────────────────────────────────────────────
    public function gudang()
    {
        $lowStockProducts = Product::where('stock', '<', 10)
            ->orderBy('stock')
            ->get();

        $outOfStockProducts = Product::where('stock', 0)->get();
        $allProducts = Product::orderBy('name')->get();

        $pendingShipmentOrders = Order::with(['user', 'orderItems.product'])
            ->where('status', 'processing')
            ->where('payment_status', 'paid')
            ->latest()
            ->take(20)
            ->get();

        $months      = [];
        $masukPerBulan  = [];
        $keluarPerBulan = [];

        for ($i = 11; $i >= 0; $i--) {
            $date  = now()->subMonths($i);
            $label = $date->format('M Y');
            $months[] = $label;

            $masukPerBulan[] = StockMovement::where('type', 'in')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('quantity') ?? 0;

            $keluarPerBulan[] = StockMovement::where('type', 'out')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('quantity') ?? 0;
        }

        $topStokProducts = Product::orderByDesc('stock')
            ->take(10)
            ->get();

        $chartLabels      = $months;
        $chartMasuk       = $masukPerBulan;
        $chartKeluar      = $keluarPerBulan;
        $chartTopStokNames = $topStokProducts->pluck('name')->map(fn($n) => \Illuminate\Support\Str::limit($n, 20))->toArray();
        $chartTopStokData  = $topStokProducts->pluck('stock')->toArray();

        return Inertia::render('Admin/Gudang/Dashboard', [
            'lowStockProducts' => $lowStockProducts,
            'outOfStockProducts' => $outOfStockProducts,
            'allProducts' => $allProducts,
            'pendingShipmentOrders' => $pendingShipmentOrders,
            'chartLabels' => $chartLabels,
            'chartMasuk' => $chartMasuk,
            'chartKeluar' => $chartKeluar,
            'chartTopStokNames' => $chartTopStokNames,
            'chartTopStokData' => $chartTopStokData,
        ]);
    }
}
