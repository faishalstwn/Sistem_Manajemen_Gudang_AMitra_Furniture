<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WmsIntegrationController extends Controller
{
    // ──────────────────────────────────────────────────────
    // GET /api/products
    // Kembalikan semua produk e-commerce untuk ditampilkan di WMS
    // ──────────────────────────────────────────────────────
    public function getProducts(): JsonResponse
    {
        $products = Product::select('id', 'name', 'category', 'price', 'stock', 'image', 'slug')
            ->latest()
            ->get()
            ->map(function ($p) {
                return [
                    'id'       => $p->id,
                    'name'     => $p->name,
                    'category' => $p->category,
                    'price'    => (float) $p->price,
                    'stock'    => $p->stock,
                    'image'    => $p->image ? asset($p->image) : null,
                    'slug'     => $p->slug,
                ];
            });

        return response()->json([
            'success' => true,
            'data'    => $products,
        ]);
    }

    // ──────────────────────────────────────────────────────
    // PATCH /api/products/{id}/stock
    // Update stok satu produk dari WMS
    // Body: { "stock": 50 }
    // ──────────────────────────────────────────────────────
    public function updateStock(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $oldStock = $product->stock;
        $product->update(['stock' => $request->stock]);

        return response()->json([
            'success'   => true,
            'message'   => "Stok produk '{$product->name}' diperbarui: {$oldStock} → {$request->stock}",
            'product'   => [
                'id'    => $product->id,
                'name'  => $product->name,
                'stock' => $product->stock,
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────
    // POST /api/products/sync-stock
    // Sinkronisasi massal stok dari WMS
    // Body: [ { "id": 1, "stock": 10 }, { "id": 2, "stock": 5 } ]
    // ──────────────────────────────────────────────────────
    public function syncStock(Request $request): JsonResponse
    {
        $request->validate([
            '*.id'    => 'required|integer|exists:products,id',
            '*.stock' => 'required|integer|min:0',
        ]);

        $updated = [];
        $failed  = [];

        foreach ($request->all() as $item) {
            try {
                $product = Product::find($item['id']);
                if ($product) {
                    $product->update(['stock' => $item['stock']]);
                    $updated[] = [
                        'id'    => $product->id,
                        'name'  => $product->name,
                        'stock' => $product->stock,
                    ];
                }
            } catch (\Exception $e) {
                $failed[] = ['id' => $item['id'], 'error' => $e->getMessage()];
            }
        }

        return response()->json([
            'success'       => true,
            'total_updated' => count($updated),
            'updated'       => $updated,
            'failed'        => $failed,
        ]);
    }

    // ──────────────────────────────────────────────────────
    // GET /api/orders
    // Kembalikan pesanan e-commerce untuk dilihat di WMS
    // Query params: ?status=processing&limit=20
    // ──────────────────────────────────────────────────────
    public function getOrders(Request $request): JsonResponse
    {
        $query = Order::with(['user', 'orderItems.product'])
            ->latest();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $limit  = min((int) $request->get('limit', 20), 100);
        $orders = $query->take($limit)->get()->map(function ($order) {
            return [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'customer'       => $order->user?->name ?? 'Guest',
                'customer_email' => $order->user?->email ?? null,
                'total_price'    => (float) $order->total_price,
                'status'         => $order->status,
                'payment_status' => $order->payment_status,
                'alamat'         => $order->alamat,
                'nomor_telepon'  => $order->nomor_telepon,
                'items'          => $order->orderItems->map(fn($item) => [
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product?->name ?? 'Produk dihapus',
                    'quantity'     => $item->quantity,
                    'price'        => (float) $item->price,
                ]),
                'created_at' => $order->created_at?->format('Y-m-d H:i:s'),
            ];
        });

        return response()->json([
            'success' => true,
            'total'   => $orders->count(),
            'data'    => $orders,
        ]);
    }

    // ──────────────────────────────────────────────────────
    // PATCH /api/orders/{id}/status
    // Update status order dari WMS (misal: setelah barang dikirim dari gudang)
    // Body: { "status": "shipped", "payment_status": "paid" }
    // ──────────────────────────────────────────────────────
    public function updateOrderStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status'         => 'sometimes|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'sometimes|in:pending,paid,failed',
        ]);

        $order = Order::findOrFail($id);

        $updateData = array_filter([
            'status'         => $request->status,
            'payment_status' => $request->payment_status,
        ]);

        $order->update($updateData);

        return response()->json([
            'success' => true,
            'message' => "Status pesanan #{$order->order_code} diperbarui.",
            'order'   => [
                'id'             => $order->id,
                'order_code'     => $order->order_code,
                'status'         => $order->status,
                'payment_status' => $order->payment_status,
            ],
        ]);
    }

    // ──────────────────────────────────────────────────────
    // GET /api/summary
    // Ringkasan data e-commerce untuk dashboard WMS
    // ──────────────────────────────────────────────────────
    public function summary(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_products'      => Product::count(),
                'low_stock_products'  => Product::where('stock', '<', 10)->count(),
                'out_of_stock'        => Product::where('stock', 0)->count(),
                'total_orders'        => Order::count(),
                'pending_orders'      => Order::where('status', 'pending')->count(),
                'processing_orders'   => Order::where('status', 'processing')->count(),
                'shipped_orders'      => Order::where('status', 'shipped')->count(),
                'total_revenue'       => (float) Order::where('payment_status', 'paid')->sum('total_price'),
                'new_orders_today'    => Order::whereDate('created_at', today())->count(),
            ],
        ]);
    }
}
