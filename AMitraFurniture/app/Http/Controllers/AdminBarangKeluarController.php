<?php

namespace App\Http\Controllers;

use App\Models\StockMovement;
use App\Services\ProductService;
use App\Services\StockMovementService;
use App\Http\Requests\RecordStockMovementRequest;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\InsufficientStockException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminBarangKeluarController extends Controller
{
    protected StockMovementService $stockService;
    protected ProductService $productService;

    public function __construct(StockMovementService $stockService, ProductService $productService)
    {
        $this->stockService = $stockService;
        $this->productService = $productService;
    }

    /**
     * Display history of barang keluar (stock out)
     */
    public function index(Request $request)
    {
        $barangKeluar = $this->stockService->getAllMovements([
            'product_id' => $request->produk_id,
            'type' => 'out',
            'date_from' => $request->dari,
            'date_to' => $request->sampai,
            'per_page' => 20,
        ]);

        $productList = $this->productService->getAllProducts([
            'per_page' => 999,
        ]);

        $products = collect($productList->items())->pluck('name', 'id');

        $query = StockMovement::with('product')
            ->where('type', 'out')
            ->when($request->filled('produk_id'), function ($q) use ($request) {
                $q->where('product_id', $request->produk_id);
            })
            ->when($request->filled('dari'), function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->dari);
            })
            ->when($request->filled('sampai'), function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->sampai);
            });

        $totalJumlah = (clone $query)->sum('quantity');
        $totalHariIni = (clone $query)->whereDate('created_at', today())->sum('quantity');

        return Inertia::render('Admin/BarangKeluar/Index', [
            'barangKeluar' => $barangKeluar,
            'products' => $products,
            'totalJumlah' => $totalJumlah,
            'totalHariIni' => $totalHariIni,
        ]);
    }

    /**
     * Show form untuk recording barang keluar
     */
    public function create()
    {
        $products = $this->productService->getAllProducts([
            'per_page' => 999,
        ])->items();

        $productsWithStock = collect($products)->filter(fn($p) => $p->stock > 0);

        return Inertia::render('Admin/BarangKeluar/Create', ['productsWithStock' => $productsWithStock]);
    }

    /**
     * Store barang keluar record dengan automatic stock decrement dan validation
     */
    public function store(RecordStockMovementRequest $request)
    {
        try {
            $validated = $request->validated();
            $validated['type'] = 'out';

            $this->productService->checkStockAvailability(
                $validated['product_id'],
                $validated['quantity']
            );

            $this->stockService->recordStockOut(
                productId: $validated['product_id'],
                quantity: $validated['quantity'],
                reference: $validated['reference'] ?? '',
                notes: $validated['notes'] ?? '',
            );

            return redirect()->route('admin.barang-keluar.index')
                ->with('success', 'Barang keluar berhasil dicatat dan stok otomatis dikurangi');
        } catch (InsufficientStockException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (ProductNotFoundException $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mencatat barang keluar: ' . $e->getMessage()]);
        }
    }

    /**
     * Show detail barang keluar
     */
    public function show($id)
    {
        try {
            $movement = StockMovement::findOrFail($id);
            $product = $this->productService->getProductById($movement->product_id);

            return Inertia::render('Admin/BarangKeluar/Show', ['movement' => $movement, 'product' => $product]);
        } catch (ProductNotFoundException $e) {
            return redirect()->route('admin.barang-keluar.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    /**
     * Get statistics untuk barang keluar
     */
    public function statistics(Request $request)
    {
        $stats = StockMovement::where('type', 'out')
            ->when($request->filled('dari'), fn($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->filled('sampai'), fn($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->selectRaw('COUNT(*) as total_records')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('AVG(quantity) as avg_quantity')
            ->selectRaw('MAX(quantity) as max_quantity')
            ->first();

        return response()->json([
            'status' => 'success',
            'data' => $stats,
        ]);
    }
}