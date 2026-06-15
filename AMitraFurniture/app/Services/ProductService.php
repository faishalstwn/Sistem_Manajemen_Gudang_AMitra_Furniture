<?php

namespace App\Services;

use App\Exceptions\InsufficientStockException;
use App\Exceptions\ProductNotFoundException;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Log;

class ProductService
{
    private const LOW_STOCK_THRESHOLD = 10;

    private const ALLOWED_SORT_FIELDS = [
        'name',
        'stock',
        'category',
        'created_at',
        'updated_at',
    ];

    private const ALLOWED_SORT_ORDERS = [
        'asc',
        'desc',
    ];

    /**
     * Get all products with filters.
     *
     * @param array $filters
     * @return LengthAwarePaginator
     */
    public function getAllProducts(array $filters = []): LengthAwarePaginator
    {
        $query = Product::query();

        $this->applyFilters($query, $filters);
        $this->applySorting($query, $filters);

        $perPage = $this->resolvePerPage($filters);

        return $query->paginate($perPage);
    }

    /**
     * Get product by ID.
     *
     * @param int $id
     * @return Product
     *
     * @throws ProductNotFoundException
     */
    public function getProductById(int $id): Product
    {
        $product = Product::find($id);

        if (!$product) {
            throw new ProductNotFoundException("Product dengan ID {$id} tidak ditemukan");
        }

        return $product;
    }

    /**
     * Get product by slug.
     *
     * @param string $slug
     * @return Product
     *
     * @throws ProductNotFoundException
     */
    public function getProductBySlug(string $slug): Product
    {
        $product = Product::where('slug', $slug)->first();

        if (!$product) {
            throw new ProductNotFoundException("Product dengan slug '{$slug}' tidak ditemukan");
        }

        return $product;
    }

    /**
     * Create product.
     *
     * @param array $data
     * @return Product
     */
    public function createProduct(array $data): Product
    {
        $product = Product::create($data);

        Log::info('Product created', [
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'data' => $data,
        ]);

        return $product;
    }

    /**
     * Update product.
     *
     * @param int $id
     * @param array $data
     * @return Product
     *
     * @throws ProductNotFoundException
     */
    public function updateProduct(int $id, array $data): Product
    {
        $product = $this->getProductById($id);
        $oldData = $product->toArray();

        $product->update($data);

        Log::info('Product updated', [
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'old_data' => $oldData,
            'new_data' => $data,
        ]);

        return $product->fresh();
    }

    /**
     * Delete product.
     *
     * @param int $id
     * @return bool
     *
     * @throws ProductNotFoundException
     */
    public function deleteProduct(int $id): bool
    {
        $product = $this->getProductById($id);

        Log::warning('Product deleted', [
            'product_id' => $product->id,
            'user_id' => auth()->id(),
            'product_name' => $product->name,
        ]);

        return (bool) $product->delete();
    }

    /**
     * Get all categories.
     *
     * @return array
     */
    public function getCategories(): array
    {
        return Product::query()
            ->whereNotNull('category')
            ->where('category', '!=', '')
            ->distinct()
            ->orderBy('category')
            ->pluck('category')
            ->values()
            ->all();
    }

    /**
     * Get stock statistics.
     *
     * @return array
     */
    public function getStockStatistics(): array
    {
        return [
            'total'  => Product::count(),
            'aman'   => Product::where('stock', '>=', self::LOW_STOCK_THRESHOLD)->count(),
            'rendah' => Product::where('stock', '>', 0)
                ->where('stock', '<', self::LOW_STOCK_THRESHOLD)
                ->count(),
            'habis'  => Product::where('stock', 0)->count(),
        ];
    }

    /**
     * Check if product has sufficient stock.
     *
     * @param int $productId
     * @param int $quantity
     * @return bool
     *
     * @throws ProductNotFoundException
     * @throws InsufficientStockException
     */
    public function checkStockAvailability(int $productId, int $quantity): bool
    {
        $product = $this->getProductById($productId);

        if ($product->stock < $quantity) {
            throw new InsufficientStockException(
                "Stok tidak cukup untuk produk '{$product->name}'. Tersedia: {$product->stock}, diminta: {$quantity}"
            );
        }

        return true;
    }

    /**
     * Get low stock products.
     *
     * @param int $threshold
     * @return Collection
     */
    public function getLowStockProducts(int $threshold = self::LOW_STOCK_THRESHOLD): Collection
    {
        return Product::where('stock', '<', $threshold)
            ->orderBy('stock')
            ->get();
    }

    /**
     * Apply filters to query.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applyFilters(Builder $query, array $filters): void
    {
        if (!empty($filters['status'])) {
            match ($filters['status']) {
                'habis' => $query->where('stock', 0),
                'rendah' => $query->where('stock', '>', 0)
                    ->where('stock', '<', self::LOW_STOCK_THRESHOLD),
                'aman' => $query->where('stock', '>=', self::LOW_STOCK_THRESHOLD),
                default => null,
            };
        }

        if (!empty($filters['kategori'])) {
            $query->where('category', $filters['kategori']);
        }

        if (!empty($filters['cari'])) {
            $search = trim((string) $filters['cari']);

            $query->where('name', 'like', '%' . $search . '%');
        }
    }

    /**
     * Apply sorting to query safely.
     *
     * @param Builder $query
     * @param array $filters
     * @return void
     */
    private function applySorting(Builder $query, array $filters): void
    {
        $sortBy = $filters['sort_by'] ?? 'stock';
        $sortOrder = strtolower($filters['sort_order'] ?? 'asc');

        if (!in_array($sortBy, self::ALLOWED_SORT_FIELDS, true)) {
            $sortBy = 'stock';
        }

        if (!in_array($sortOrder, self::ALLOWED_SORT_ORDERS, true)) {
            $sortOrder = 'asc';
        }

        $query->orderBy($sortBy, $sortOrder);
    }

    /**
     * Resolve per-page value safely.
     *
     * @param array $filters
     * @return int
     */
    private function resolvePerPage(array $filters): int
    {
        $perPage = (int) ($filters['per_page'] ?? 15);

        if ($perPage < 1) {
            return 15;
        }

        return min($perPage, 100);
    }
}