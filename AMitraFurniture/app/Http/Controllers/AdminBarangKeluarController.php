<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminBarangKeluarController extends Controller
{
    /**
     * Riwayat barang keluar
     */
    public function index(Request $request)
    {
        $query = BarangKeluar::with('produk');

        if ($request->filled('produk_id')) {
            $query->where('produk_id', $request->produk_id);
        }

        if ($request->filled('tujuan')) {
            $query->where('tujuan', 'like', '%' . $request->tujuan . '%');
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal_keluar', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal_keluar', '<=', $request->sampai);
        }

        $barangKeluar = $query->latest()->paginate(20)->withQueryString();
        $products     = Product::orderBy('name')->pluck('name', 'id');

        $totalJumlah  = BarangKeluar::sum('jumlah');
        $totalHariIni = BarangKeluar::whereDate('tanggal_keluar', today())->sum('jumlah');

        return view('admin.barang-keluar.index', compact(
            'barangKeluar', 'products', 'totalJumlah', 'totalHariIni'
        ));
    }

    /**
     * Form tambah barang keluar
     */
    public function create()
    {
        $products = Product::where('stock', '>', 0)->orderBy('name')->get();
        return view('admin.barang-keluar.create', compact('products'));
    }

    /**
     * Simpan barang keluar & kurangi stok produk
     */
    public function store(Request $request)
    {
        // Validasi awal tanpa max stok (butuh product_id dulu)
        $request->validate([
            'produk_id'      => 'required|exists:products,id',
            'jumlah'         => 'required|integer|min:1',
            'tujuan'         => 'nullable|string|max:255',
            'tanggal_keluar' => 'required|date',
            'catatan'        => 'nullable|string|max:500',
        ]);

        $product = Product::findOrFail($request->produk_id);

        // Validasi stok mencukupi
        if ($request->jumlah > $product->stock) {
            return back()->withInput()
                ->withErrors(['jumlah' => "Stok produk '{$product->name}' hanya tersisa {$product->stock} unit."]);
        }

        $validated = $request->only(['produk_id', 'jumlah', 'tujuan', 'tanggal_keluar', 'catatan']);

        DB::transaction(function () use ($validated, $product) {
            // Simpan record barang keluar
            BarangKeluar::create($validated);

            // Kurangi stok produk
            $previousStock = $product->stock;
            $newStock      = $previousStock - $validated['jumlah'];
            $product->update(['stock' => $newStock]);

            // Catat di stock_movements
            StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => Auth::id(),
                'type'           => 'out',
                'quantity'       => $validated['jumlah'],
                'previous_stock' => $previousStock,
                'new_stock'      => $newStock,
                'reference'      => $validated['tujuan'] ?? null,
                'note'           => 'Barang keluar ke: ' . ($validated['tujuan'] ?? '-') .
                                    ($validated['catatan'] ? ' | ' . $validated['catatan'] : ''),
            ]);
        });

        return redirect()->route('admin.barang-keluar.index')
            ->with('success', 'Barang keluar berhasil dicatat. Stok produk otomatis dikurangi.');
    }
}
