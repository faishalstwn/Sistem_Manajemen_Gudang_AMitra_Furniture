<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class AdminLaporanGudangController extends Controller
{
    public function index(Request $request)
    {
        $periode = $request->get('periode', 12);

        // ── Ringkasan utama ────────────────────────────────────
        $totalProduk    = Product::count();
        $totalStok      = Product::sum('stock');
        $nilaiInventori = Product::selectRaw('SUM(price * stock) as total')->value('total') ?? 0;

        $totalMasuk30  = StockMovement::where('type', 'in')->where('created_at', '>=', now()->subDays(30))->sum('quantity');
        $totalKeluar30 = StockMovement::where('type', 'out')->where('created_at', '>=', now()->subDays(30))->sum('quantity');

        // ── Trend stok masuk vs keluar per bulan ───────────────
        $months      = [];
        $dataMasuk   = [];
        $dataKeluar  = [];
        $dataSelisih = [];

        for ($i = $periode - 1; $i >= 0; $i--) {
            $date     = now()->subMonths($i);
            $months[] = $date->format('M Y');

            $masuk = StockMovement::where('type', 'in')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('quantity');

            $keluar = StockMovement::where('type', 'out')
                ->whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('quantity');

            $dataMasuk[]   = $masuk;
            $dataKeluar[]  = $keluar;
            $dataSelisih[] = $masuk - $keluar;
        }

        // ── Top movers (produk paling banyak keluar) ───────────
        $topKeluar = StockMovement::where('type', 'out')
            ->select('product_id', DB::raw('SUM(quantity) as total_keluar'))
            ->where('created_at', '>=', now()->subMonths($periode))
            ->groupBy('product_id')
            ->orderByDesc('total_keluar')
            ->take(10)
            ->with('product')
            ->get();

        // ── Slow movers (produk paling sedikit keluar / tidak keluar) ──
        $fastProductIds = StockMovement::where('type', 'out')
            ->where('created_at', '>=', now()->subMonths(3))
            ->pluck('product_id')
            ->unique();

        $slowMovers = Product::where('stock', '>', 0)
            ->whereNotIn('id', $fastProductIds)
            ->orderBy('stock', 'desc')
            ->take(10)
            ->get();

        // ── Distribusi stok per kategori ───────────────────────
        $stokPerKategori = Product::select('category', DB::raw('SUM(stock) as total_stok'), DB::raw('COUNT(*) as jumlah_produk'))
            ->groupBy('category')
            ->orderByDesc('total_stok')
            ->get();

        // ── Pergerakan stok per tipe (pie chart) ───────────────
        $movementSummary = [
            'masuk'     => StockMovement::where('type', 'in')->where('created_at', '>=', now()->subMonths($periode))->sum('quantity'),
            'keluar'    => StockMovement::where('type', 'out')->where('created_at', '>=', now()->subMonths($periode))->sum('quantity'),
            'koreksi'   => StockMovement::where('type', 'adjustment')->where('created_at', '>=', now()->subMonths($periode))->sum('quantity'),
        ];

        // ── Rata-rata turnover ─────────────────────────────────
        $avgMasukBulanan  = count($dataMasuk) > 0 ? round(array_sum($dataMasuk) / count($dataMasuk), 2) : 0;
        $avgKeluarBulanan = count($dataKeluar) > 0 ? round(array_sum($dataKeluar) / count($dataKeluar), 2) : 0;

        // ── Stock opname terakhir ──────────────────────────────
        $lastOpname = StockOpname::with('user')
            ->where('status', 'selesai')
            ->latest('tanggal')
            ->first();

        return Inertia::render('Admin/LaporanGudang/Index', [
            'totalProduk' => $totalProduk,
            'totalStok' => $totalStok,
            'nilaiInventori' => $nilaiInventori,
            'totalMasuk30' => $totalMasuk30,
            'totalKeluar30' => $totalKeluar30,
            'months' => $months,
            'dataMasuk' => $dataMasuk,
            'dataKeluar' => $dataKeluar,
            'dataSelisih' => $dataSelisih,
            'topKeluar' => $topKeluar,
            'slowMovers' => $slowMovers,
            'stokPerKategori' => $stokPerKategori,
            'movementSummary' => $movementSummary,
            'avgMasukBulanan' => $avgMasukBulanan,
            'avgKeluarBulanan' => $avgKeluarBulanan,
            'lastOpname' => $lastOpname,
            'periode' => $periode,
        ]);
    }
}
