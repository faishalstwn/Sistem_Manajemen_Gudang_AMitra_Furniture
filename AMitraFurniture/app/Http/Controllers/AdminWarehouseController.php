<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminWarehouseController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    // Halaman Utama Manajemen Gudang
    // ──────────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan status stok
        if ($request->filled('status')) {
            match ($request->status) {
                'habis'  => $query->where('stock', 0),
                'rendah' => $query->where('stock', '>', 0)->where('stock', '<', 10),
                'aman'   => $query->where('stock', '>=', 10),
                default  => null,
            };
        }

        // Filter berdasarkan kategori
        if ($request->filled('kategori')) {
            $query->where('category', $request->kategori);
        }

        // Pencarian
        if ($request->filled('cari')) {
            $query->where('name', 'like', '%' . $request->cari . '%');
        }

        $products   = $query->orderBy('stock')->paginate(15)->withQueryString();
        $categories = Product::distinct()->pluck('category')->filter()->sort();

        // Ringkasan statistik stok
        $stats = [
            'total'   => Product::count(),
            'aman'    => Product::where('stock', '>=', 10)->count(),
            'rendah'  => Product::where('stock', '>', 0)->where('stock', '<', 10)->count(),
            'habis'   => Product::where('stock', 0)->count(),
            'total_unit' => Product::sum('stock'),
        ];

        // 5 pergerakan stok terbaru
        $recentMovements = StockMovement::with(['product', 'user'])
            ->latest()
            ->take(8)
            ->get();

        return view('admin.gudang.index', compact(
            'products',
            'categories',
            'stats',
            'recentMovements',
        ));
    }

    // ──────────────────────────────────────────────────────────────────
    // Stok Masuk (Penerimaan / Restok)
    // ──────────────────────────────────────────────────────────────────

    public function stokMasukForm(Product $product)
    {
        return view('admin.gudang.stok-masuk', compact('product'));
    }

    public function stokMasuk(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity'  => 'required|integer|min:1',
            'reference' => 'nullable|string|max:100',
            'note'      => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $previousStock = $product->stock;
            $newStock      = $previousStock + $validated['quantity'];

            $product->update(['stock' => $newStock]);

            StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => Auth::id(),
                'type'           => 'in',
                'quantity'       => $validated['quantity'],
                'previous_stock' => $previousStock,
                'new_stock'      => $newStock,
                'reference'      => $validated['reference'] ?? null,
                'note'           => $validated['note'] ?? null,
            ]);
        });

        return redirect()->route('admin.gudang.kelola')
            ->with('success', "Stok masuk +{$validated['quantity']} unit untuk produk '{$product->name}' berhasil dicatat.");
    }

    // ──────────────────────────────────────────────────────────────────
    // Stok Keluar (Pengambilan Manual)
    // ──────────────────────────────────────────────────────────────────

    public function stokKeluarForm(Product $product)
    {
        return view('admin.gudang.stok-keluar', compact('product'));
    }

    public function stokKeluar(Request $request, Product $product)
    {
        $validated = $request->validate([
            'quantity'  => "required|integer|min:1|max:{$product->stock}",
            'reference' => 'nullable|string|max:100',
            'note'      => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $previousStock = $product->stock;
            $newStock      = $previousStock - $validated['quantity'];

            $product->update(['stock' => $newStock]);

            StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => Auth::id(),
                'type'           => 'out',
                'quantity'       => $validated['quantity'],
                'previous_stock' => $previousStock,
                'new_stock'      => $newStock,
                'reference'      => $validated['reference'] ?? null,
                'note'           => $validated['note'] ?? null,
            ]);
        });

        return redirect()->route('admin.gudang.kelola')
            ->with('success', "Stok keluar -{$validated['quantity']} unit untuk produk '{$product->name}' berhasil dicatat.");
    }

    // ──────────────────────────────────────────────────────────────────
    // Koreksi / Adjustment Stok
    // ──────────────────────────────────────────────────────────────────

    public function adjustmentForm(Product $product)
    {
        return view('admin.gudang.adjustment', compact('product'));
    }

    public function adjustment(Request $request, Product $product)
    {
        $validated = $request->validate([
            'new_stock' => 'required|integer|min:0',
            'note'      => 'required|string|max:500',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $previousStock = $product->stock;
            $newStock      = (int) $validated['new_stock'];
            $diff          = abs($newStock - $previousStock);

            $product->update(['stock' => $newStock]);

            StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => Auth::id(),
                'type'           => 'adjustment',
                'quantity'       => $diff,
                'previous_stock' => $previousStock,
                'new_stock'      => $newStock,
                'reference'      => null,
                'note'           => $validated['note'],
            ]);
        });

        return redirect()->route('admin.gudang.kelola')
            ->with('success', "Koreksi stok produk '{$product->name}' berhasil. Stok diubah menjadi {$validated['new_stock']} unit.");
    }

    // ──────────────────────────────────────────────────────────────────
    // Riwayat Pergerakan Stok
    // ──────────────────────────────────────────────────────────────────

    public function riwayat(Request $request)
    {
        $query = StockMovement::with(['product', 'user']);

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('product_id')) {
            $query->where('product_id', $request->product_id);
        }

        if ($request->filled('dari')) {
            $query->whereDate('created_at', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('created_at', '<=', $request->sampai);
        }

        $movements = $query->latest()->paginate(20)->withQueryString();
        $products  = Product::orderBy('name')->pluck('name', 'id');

        // Ringkasan
        $summary = [
            'total_in'         => StockMovement::where('type', 'in')->sum('quantity'),
            'total_out'        => StockMovement::where('type', 'out')->sum('quantity'),
            'total_adjustment' => StockMovement::where('type', 'adjustment')->count(),
        ];

        return view('admin.gudang.riwayat', compact('movements', 'products', 'summary'));
    }
}
