<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockOpname;
use App\Models\StockOpnameItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;

class AdminStockOpnameController extends Controller
{
    public function index(Request $request)
    {
        $query = StockOpname::with('user')->withCount('items');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('dari')) {
            $query->whereDate('tanggal', '>=', $request->dari);
        }

        if ($request->filled('sampai')) {
            $query->whereDate('tanggal', '<=', $request->sampai);
        }

        $opnames = $query->latest('tanggal')->paginate(15)->withQueryString();

        $stats = [
            'total'    => StockOpname::count(),
            'draft'    => StockOpname::where('status', 'draft')->count(),
            'selesai'  => StockOpname::where('status', 'selesai')->count(),
        ];

        return Inertia::render('Admin/StockOpname/Index', ['opnames' => $opnames, 'stats' => $stats]);
    }

    public function create()
    {
        $products = Product::orderBy('name')->get();
        $kode     = StockOpname::generateKode();

        return Inertia::render('Admin/StockOpname/Create', ['products' => $products, 'kode' => $kode]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal'           => 'required|date',
            'catatan'           => 'nullable|string|max:1000',
            'items'             => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.stok_fisik' => 'required|integer|min:0',
        ]);

        $opname = DB::transaction(function () use ($request) {
            $opname = StockOpname::create([
                'kode'    => StockOpname::generateKode(),
                'user_id' => Auth::id(),
                'tanggal' => $request->tanggal,
                'status'  => 'draft',
                'catatan' => $request->catatan,
            ]);

            foreach ($request->items as $item) {
                $product    = Product::findOrFail($item['product_id']);
                $stokSistem = $product->stock;
                $stokFisik  = (int) $item['stok_fisik'];

                StockOpnameItem::create([
                    'stock_opname_id' => $opname->id,
                    'product_id'      => $product->id,
                    'stok_sistem'     => $stokSistem,
                    'stok_fisik'      => $stokFisik,
                    'selisih'         => $stokFisik - $stokSistem,
                    'keterangan'      => $item['keterangan'] ?? null,
                ]);
            }

            return $opname;
        });

        return redirect()->route('admin.stock-opname.show', $opname)
            ->with('success', "Stock opname {$opname->kode} berhasil dibuat.");
    }

    public function show(StockOpname $stockOpname)
    {
        $stockOpname->load(['user', 'items.product']);

        return Inertia::render('Admin/StockOpname/Show', ['stockOpname' => $stockOpname]);
    }

    public function approve(StockOpname $stockOpname)
    {
        if ($stockOpname->status !== 'draft') {
            return back()->with('error', 'Stock opname ini sudah diselesaikan.');
        }

        DB::transaction(function () use ($stockOpname) {
            foreach ($stockOpname->items as $item) {
                if ($item->selisih === 0) continue;

                $product       = $item->product;
                $previousStock = $product->stock;
                $newStock      = $item->stok_fisik;

                $product->update(['stock' => $newStock]);

                StockMovement::create([
                    'product_id'     => $product->id,
                    'user_id'        => Auth::id(),
                    'type'           => 'adjustment',
                    'quantity'       => abs($item->selisih),
                    'previous_stock' => $previousStock,
                    'new_stock'      => $newStock,
                    'reference'      => $stockOpname->kode,
                    'note'           => "Stock opname: selisih {$item->selisih} unit",
                ]);
            }

            $stockOpname->update(['status' => 'selesai']);
        });

        return back()->with('success', "Stock opname {$stockOpname->kode} telah disetujui. Stok produk sudah diperbarui.");
    }
}
