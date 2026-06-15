<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminOrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index()
    {
        $orders = Order::with(['user', 'orderItems.product'])->latest()->paginate(15);
        return Inertia::render('Admin/Pesanan/Index', ['orders' => $orders]);
    }

    
    public function edit(Order $order)
    {
        $order->load('user', 'orderItems.product');
        return Inertia::render('Admin/Pesanan/Edit', ['order' => $order]);
    }

    
    public function update(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
        ]);

        $order->load('orderItems.product');

        $newStatus = $validated['status'];
        $oldStatus = $order->status;

        
        $isShippingNow = in_array($newStatus, ['shipped', 'delivered']) && !in_array($oldStatus, ['shipped', 'delivered']);

        if ($isShippingNow) {
           
            foreach ($order->orderItems as $item) {
                if ($item->quantity > $item->product->stock) {
                    return redirect()->back()
                        ->with('error', "Stok produk '{$item->product->name}' tidak mencukupi. Sisa: {$item->product->stock}, Butuh: {$item->quantity}");
                }
            }

          
            $stockService = app(\App\Services\StockMovementService::class);
            foreach ($order->orderItems as $item) {
                $stockService->recordStockOut(
                    $item->product_id,
                    $item->quantity,
                    $order->order_code,
                    "Penjualan dari Pesanan {$order->order_code}"
                );
            }
        }

        $order->update([
            'status' => $validated['status'],
            'payment_status' => $validated['payment_status'],
        ]);

        return redirect()->route('admin.pesanan.index')
            ->with('success', 'Status pesanan berhasil diperbarui');
    }

   
    public function shipping()
    {
        $waitingShipment = Order::where('status', 'processing')
            ->where('payment_status', 'paid')
            ->count();
        
        $shipping = Order::where('status', 'shipped')->count();
        
        $delivered = Order::where('status', 'delivered')->count();
        
        $recentOrders = Order::with(['user', 'orderItems.product'])
            ->whereIn('status', ['processing', 'shipped', 'delivered'])
            ->latest()
            ->take(10)
            ->get();
        
        return Inertia::render('Admin/Pengiriman', [
            'waitingShipment' => $waitingShipment,
            'shipping' => $shipping,
            'delivered' => $delivered,
            'recentOrders' => $recentOrders,
        ]);
    }
}
