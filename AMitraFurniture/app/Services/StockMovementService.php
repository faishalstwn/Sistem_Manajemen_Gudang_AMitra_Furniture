<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockMovement;
use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFoundException;

class StockMovementService
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    /**
     * Record stock in (Barang Masuk)
     *
     * @param int $productId
     * @param int $quantity
     * @param string $reference (purchase order number, etc)
     * @param string $notes
     * @return StockMovement
     * @throws ProductNotFoundException
     */
    public function recordStockIn(int $productId, int $quantity, string $reference = '', string $notes = ''): StockMovement
    {
        $product = $this->productService->getProductById($productId);

        $previousStock = $product->stock;

        // Update product stock
        $product->increment('stock', $quantity);

        // Record movement
        $movement = StockMovement::create([
            'product_id' => $productId,
            'type' => 'in',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $previousStock + $quantity,
            'reference' => $reference,
            'note' => $notes,
            'user_id' => auth()?->id(),
        ]);

        // Log audit
        \Log::info('Stock recorded IN', [
            'product_id' => $productId,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'new_stock' => $product->stock,
            'reference' => $reference,
            'user_id' => auth()?->id(),
        ]);

        return $movement;
    }

    /**
     * Record stock out (Barang Keluar)
     *
     * @param int $productId
     * @param int $quantity
     * @param string $reference (order number, etc)
     * @param string $notes
     * @return StockMovement
     * @throws ProductNotFoundException
     * @throws InsufficientStockException
     */
    public function recordStockOut(int $productId, int $quantity, string $reference = '', string $notes = ''): StockMovement
    {
        $product = $this->productService->getProductById($productId);

        // Check stock availability
        $this->productService->checkStockAvailability($productId, $quantity);

        $previousStock = $product->stock;

        // Update product stock
        $product->decrement('stock', $quantity);

        // Record movement
        $movement = StockMovement::create([
            'product_id' => $productId,
            'type' => 'out',
            'quantity' => $quantity,
            'previous_stock' => $previousStock,
            'new_stock' => $previousStock - $quantity,
            'reference' => $reference,
            'note' => $notes,
            'user_id' => auth()?->id(),
        ]);

        // Log audit
        \Log::info('Stock recorded OUT', [
            'product_id' => $productId,
            'product_name' => $product->name,
            'quantity' => $quantity,
            'new_stock' => $product->stock,
            'reference' => $reference,
            'user_id' => auth()?->id(),
        ]);

        return $movement;
    }

    /**
     * Get stock movements for product
     *
     * @param int $productId
     * @param array $filters
     * @return \Illuminate\Pagination\Paginator
     */
    public function getProductMovements(int $productId, array $filters = [])
    {
        $query = StockMovement::where('product_id', $productId);

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Get all stock movements
     *
     * @param array $filters
     * @return \Illuminate\Pagination\Paginator
     */
    public function getAllMovements(array $filters = [])
    {
        $query = StockMovement::query();

        if (isset($filters['product_id'])) {
            $query->where('product_id', $filters['product_id']);
        }

        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query->with('product')->orderBy('created_at', 'desc')->paginate($filters['per_page'] ?? 20);
    }

    /**
     * Get stock movement statistics
     *
     * @return array
     */
    public function getMovementStatistics(): array
    {
        $totalIn = StockMovement::where('type', 'in')->sum('quantity');
        $totalOut = StockMovement::where('type', 'out')->sum('quantity');

        return [
            'total_in' => $totalIn,
            'total_out' => $totalOut,
            'net_movement' => $totalIn - $totalOut,
            'total_movements' => StockMovement::count(),
        ];
    }
}
