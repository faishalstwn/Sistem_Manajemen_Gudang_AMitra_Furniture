<?php

namespace App\Http\Controllers;

use App\Models\BarangMasuk;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminBarangMasukController extends Controller
{
    /**
     * Riwayat barang masuk
     */
    public function index(Request $request)
    {
        $query = BarangMasuk::with('produk');

        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        if ($request->filled('supplier')) {
            $query->where('supplier', 'like', '%' . $request->supplier . '%');
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_masuk', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_masuk', '<=', $request->sampai);
        }

        $barangMasuk = $query->latest()->paginate(20)->withQueryString();
        $products    = Product::orderBy('name')->pluck('name', 'id');

        $totalJumlah = BarangMasuk::sum('jumlah');
        $totalHariIni = BarangMasuk::whereDate('tanggal_masuk', today())->sum('jumlah');

        return view('admin.barang-masuk.index', compact(
            'barangMasuk', 'products', 'totalJumlah', 'totalHariIni'
        ));
    }

    /**
     * Form tambah barang masuk
     */
    public function create()
    {
        $products = Product::orderBy('name')->get();
        return view('admin.barang-masuk.create', compact('products'));
    }

    /**
     * Simpan barang masuk & update stok produk
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'produk_id'     => 'required|exists:products,id',
            'jumlah'        => 'required|integer|min:1',
            'tanggal_masuk' => 'required|date',
            'supplier'      => 'nullable|string|max:255',
            'catatan'       => 'nullable|string|max:500',
        ]);

        DB::transaction(function () use ($validated) {
            // Simpan record barang masuk
            BarangMasuk::create($validated);

            // Update stok produk
            $product       = Product::findOrFail($validated['produk_id']);
            $previousStock = $product->stock;
            $newStock      = $previousStock + $validated['jumlah'];
            $product->update(['stock' => $newStock]);

            // Catat di stock_movements untuk konsistensi riwayat
            StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => Auth::id(),
                'type'           => 'in',
                'quantity'       => $validated['jumlah'],
                'previous_stock' => $previousStock,
                'new_stock'      => $newStock,
                'reference'      => $validated['supplier'] ?? null,
                'note'           => 'Barang masuk dari supplier: ' . ($validated['supplier'] ?? '-') .
                                    ($validated['catatan'] ? ' | ' . $validated['catatan'] : ''),
            ]);
        });

        return redirect()->route('admin.barang-masuk.index')
            ->with('success', 'Barang masuk berhasil dicatat. Stok produk otomatis diperbarui.');
    }
}
