<?php

namespace App\Http\Controllers;

use App\Exceptions\ProductNotFoundException;
use App\Http\Requests\RecordStockMovementRequest;
use App\Models\StockMovement;
use App\Services\ProductService;
use App\Services\StockMovementService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Inertia\Inertia;

class AdminBarangMasukController extends Controller
{
    protected StockMovementService $stockService;
    protected ProductService $productService;

    public function __construct(
        StockMovementService $stockService,
        ProductService $productService
    ) {
        $this->stockService = $stockService;
        $this->productService = $productService;
    }

    /**
     * Display history of barang masuk.
     */
    public function index(Request $request)
    {
        $barangMasuk = $this->stockService->getAllMovements([
            'product_id' => $request->produk_id,
            'type'       => 'in',
            'date_from'  => $request->dari,
            'date_to'    => $request->sampai,
            'per_page'   => 20,
        ]);

        $products = $this->productService->getAllProducts([
            'per_page' => 999,
        ])->getCollection()->pluck('name', 'id');

        $totalJumlah = StockMovement::where('type', 'in')->sum('quantity');

        $totalHariIni = StockMovement::where('type', 'in')
            ->whereDate('created_at', today())
            ->sum('quantity');

        return Inertia::render('Admin/BarangMasuk/Index', compact(
            'barangMasuk',
            'products',
            'totalJumlah',
            'totalHariIni'
        ));
    }

    /**
     * Show form untuk recording barang masuk.
     */
    public function create()
    {
        $products = $this->productService->getAllProducts([
            'per_page' => 999,
        ])->items();

        return Inertia::render('Admin/BarangMasuk/Create', ['products' => $products]);
    }

    /**
     * Store barang masuk record dengan automatic stock update.
     */
    public function store(RecordStockMovementRequest $request): RedirectResponse
    {
        try {
            $validated = $request->validated();

            $this->stockService->recordStockIn(
                productId: (int) $validated['product_id'],
                quantity: (int) $validated['quantity'],
                reference: $validated['reference'] ?? '',
                notes: $validated['notes'] ?? '',
            );

            return redirect()
                ->route('admin.barang-masuk.index')
                ->with('success', 'Barang masuk berhasil dicatat dan stok otomatis diperbarui.');
        } catch (ProductNotFoundException $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Throwable $e) {
            return redirect()
                ->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mencatat barang masuk: ' . $e->getMessage()]);
        }
    }

    /**
     * Show detail barang masuk.
     */
    public function show(int $id): View|RedirectResponse
    {
        try {
            $movement = StockMovement::with('product')->findOrFail($id);

            return Inertia::render('Admin/BarangMasuk/Show', ['movement' => $movement]);
        } catch (\Throwable $e) {
            return redirect()
                ->route('admin.barang-masuk.index')
                ->withErrors(['error' => 'Data barang masuk tidak ditemukan.']);
        }
    }

    /**
     * Get statistics untuk barang masuk.
     */
    public function statistics(Request $request): JsonResponse
    {
        $stats = StockMovement::where('type', 'in')
            ->when($request->filled('dari'), fn ($q) => $q->whereDate('created_at', '>=', $request->dari))
            ->when($request->filled('sampai'), fn ($q) => $q->whereDate('created_at', '<=', $request->sampai))
            ->selectRaw('COUNT(*) as total_records')
            ->selectRaw('SUM(quantity) as total_quantity')
            ->selectRaw('AVG(quantity) as avg_quantity')
            ->selectRaw('MAX(quantity) as max_quantity')
            ->first();

        return response()->json([
            'status' => 'success',
            'data'   => $stats,
        ]);
    }
}