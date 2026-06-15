<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class WmsIntegrationController extends Controller
{
   
  public function getProducts(): JsonResponse
{
    $products = Cache::remember('wms_products', 60, function () {
        return Product::select('id', 'name', 'category', 'price', 'stock', 'image', 'slug')
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
    });

    return response()->json([
        'success' => true,
        'data'    => $products,
    ]);
}
  
    public function updateStock(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'stock' => 'required|integer|min:0',
        ]);

        $result = DB::transaction(function () use ($request, $id) {
            $product  = Product::lockForUpdate()->findOrFail($id);
            $oldStock = $product->stock;
            $newStock = (int) $request->stock;
            $product->update(['stock' => $newStock]);

            StockMovement::create([
                'product_id'     => $product->id,
                'user_id'        => null,
                'type'           => 'adjustment',
                'quantity'       => abs($newStock - $oldStock),
                'previous_stock' => $oldStock,
                'new_stock'      => $newStock,
                'reference'      => 'WMS-API',
                'note'           => 'Update stok via WMS Integration API',
            ]);

            return ['product' => $product->fresh(), 'oldStock' => $oldStock];
        });

        return response()->json([
            'success'   => true,
            'message'   => "Stok produk '{$result['product']->name}' diperbarui: {$result['oldStock']} → {$result['product']->stock}",
            'product'   => [
                'id'    => $result['product']->id,
                'name'  => $result['product']->name,
                'stock' => $result['product']->stock,
            ],
        ]);
    }

   
    public function syncStock(Request $request): JsonResponse
{
    $request->validate([
    '*.id'    => 'required|integer',
    '*.stock' => 'required|integer|min:0',
]);
       $ids = collect($request->all())->pluck('id')->unique();

    $validIds = Product::whereIn('id', $ids)->pluck('id');

    if ($validIds->count() !== $ids->count()) {
        return response()->json([
            'success' => false,
            'message' => 'Ada ID produk yang tidak valid'
        ], 400);
    }

    $updated = [];
    $failed  = [];

    try {
        DB::transaction(function () use ($request, &$updated) {
            $ids      = collect($request->all())->pluck('id');
            $products = Product::lockForUpdate()
                               ->whereIn('id', $ids)
                               ->get()
                               ->keyBy('id');

            $movements = [];

            foreach ($request->all() as $item) {
                $product = $products->get($item['id']);
                if ($product) {
                    $oldStock = $product->stock;
                    $newStock = (int) $item['stock'];
                    $product->update(['stock' => $newStock]);

                    $movements[] = [
                        'product_id'     => $product->id,
                        'user_id'        => null,
                        'type'           => 'adjustment',
                        'quantity'       => abs($newStock - $oldStock),
                        'previous_stock' => $oldStock,
                        'new_stock'      => $newStock,
                        'reference'      => 'WMS-SYNC',
                        'note'           => 'Sinkronisasi stok massal via WMS Integration API',
                        'created_at'     => now(),
                        'updated_at'     => now(),
                    ];

                    $updated[] = [
                        'id'    => $product->id,
                        'name'  => $product->name,
                        'stock' => $newStock,
                    ];
                }
            }

            StockMovement::insert($movements);
        });
    } catch (\Exception $e) {
        $failed[] = ['error' => $e->getMessage()];
    }

    return response()->json([
        'success'       => true,
        'total_updated' => count($updated),
        'updated'       => $updated,
        'failed'        => $failed,
    ]);
  } 

  
    public function getOrders(Request $request): JsonResponse
{
    $limit = min((int) $request->get('limit', 20), 100);

    $orders = Order::query()
        ->with([
            'user:id,name,email',
            'orderItems:id,order_id,product_id,quantity,price',
            'orderItems.product:id,name'
        ])
        ->when($request->status, fn ($q) => 
            $q->where('status', $request->status)
        )
        ->when($request->payment_status, fn ($q) => 
            $q->where('payment_status', $request->payment_status)
        )
        ->latest()
        ->limit($limit)
        ->get();

    $data = $orders->map(function ($order) {
        return [
            'id'             => $order->id,
            'order_code'     => $order->order_code,

            'customer' => [
                'name'  => $order->user?->name ?? 'Guest',
                'email' => $order->user?->email,
            ],

            'total_price'    => (float) $order->total_price,
            'status'         => $order->status,
            'payment_status' => $order->payment_status,

            'shipping' => [
                'address' => $order->alamat,
                'phone'   => $order->nomor_telepon,
            ],

            'items' => $order->orderItems->map(function ($item) {
                return [
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product?->name ?? 'Produk dihapus',
                    'quantity'     => $item->quantity,
                    'price'        => (float) $item->price,
                    'subtotal'     => (float) ($item->price * $item->quantity),
                ];
            }),

            'created_at' => $order->created_at?->format('Y-m-d H:i:s'),
        ];
    });

    return response()->json([
        'success' => true,
        'meta' => [
            'total' => $orders->count(),
            'limit' => $limit,
        ],
        'data' => $data,
    ]);
}

   
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

   
   public function summary(): JsonResponse
{
    $data = Cache::remember('wms_summary', 30, function () {

        $orders = DB::table('orders')
            ->selectRaw("
                COUNT(*) as total_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_orders,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing_orders,
                SUM(CASE WHEN status = 'shipped' THEN 1 ELSE 0 END) as shipped_orders,
                SUM(CASE WHEN payment_status = 'paid' THEN total_price ELSE 0 END) as total_revenue,
                SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as new_orders_today
            ")
            ->first();

        $products = DB::table('products')
            ->selectRaw("
                COUNT(*) as total_products,
                SUM(CASE WHEN stock < 10 THEN 1 ELSE 0 END) as low_stock_products,
                SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_of_stock
            ")
            ->first();

        return [
            'total_products'     => $products->total_products,
            'low_stock_products' => $products->low_stock_products,
            'out_of_stock'       => $products->out_of_stock,

            'total_orders'       => $orders->total_orders,
            'pending_orders'     => $orders->pending_orders,
            'processing_orders'  => $orders->processing_orders,
            'shipped_orders'     => $orders->shipped_orders,

            'total_revenue'      => (float) $orders->total_revenue,
            'new_orders_today'   => $orders->new_orders_today,
        ];
    });

    return response()->json([
        'success' => true,
        'data'    => $data,
    ]);
}
}
