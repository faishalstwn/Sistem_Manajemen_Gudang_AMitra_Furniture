<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Cart;
use App\Models\OrderItem;
use App\Helpers\MidtransHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Config;
use Midtrans\Snap;

class OrderController extends Controller
{
    public function __construct()
    {
        // Set your Midtrans configuration
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production', false);
        Config::$isSanitized = config('midtrans.is_sanitized', true);
        Config::$is3ds = config('midtrans.is_3ds', true);
    }

    /* ==============================
       HALAMAN CHECKOUT (Dari Cart)
    ============================== */
    public function checkout()
    {
        $carts = Cart::where('user_id', Auth::id())->with('product')->get();
        
        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        $total = $carts->sum(function($cart) {
            return $cart->product->price * $cart->quantity;
        });

        return view('dashboard.checkout-cart', compact('carts', 'total'));
    }

    /* ==============================
       PROSES CHECKOUT & BUAT ORDER
    ============================== */
    public function store(Request $request)
    {
        $request->validate([
            'alamat' => 'required|string|max:500',
            'nomor_telepon' => 'required|string|max:20',
            'payment_method' => 'required|in:cod,midtrans,transfer_bca,transfer_bri,transfer_mandiri',
        ]);

        $user = Auth::user();
        $carts = Cart::where('user_id', $user->id)->with('product')->get();

        if ($carts->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang Anda kosong.');
        }

        DB::beginTransaction();

        try {
            $totalPrice = 0;
            $orderCode = 'ORD-' . time() . '-' . $user->id;

            // Hitung total dari semua item di cart
            foreach ($carts as $cart) {
                $totalPrice += $cart->product->price * $cart->quantity;
            }

            // Set payment deadline 24 jam dari sekarang untuk non-COD
            $paymentDeadline = null;
            if ($request->payment_method !== 'cod') {
                $paymentDeadline = now()->addHours(24);
            }

            // Buat order utama
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'payment_deadline' => $paymentDeadline,
                'status' => 'pending',
                'alamat' => $request->alamat,
                'nomor_telepon' => $request->nomor_telepon,
            ]);

            // Buat order items untuk setiap produk di cart
            foreach ($carts as $cart) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $cart->product_id,
                    'quantity' => $cart->quantity,
                    'price' => $cart->product->price,
                ]);
            }

            // Generate Snap Token jika payment method adalah Midtrans
            if ($request->payment_method === 'midtrans') {
                try {
                    // Prepare item details untuk Midtrans
                    $itemDetails = [];
                    foreach ($carts as $cart) {
                        $itemDetails[] = [
                            'id' => $cart->product_id,
                            'price' => (int) $cart->product->price,
                            'quantity' => $cart->quantity,
                            'name' => $cart->product->name,
                        ];
                    }

                    $params = [
                        'transaction_details' => [
                            'order_id' => $orderCode,
                            'gross_amount' => (int) $totalPrice,
                        ],
                        'customer_details' => [
                            'first_name' => $user->name,
                            'email' => $user->email,
                            'phone' => $request->nomor_telepon,
                        ],
                        'item_details' => $itemDetails,
                    ];

                    // Generate Snap Token
                    $result = MidtransHelper::getSnapToken($params);
                    $order->update(['snap_token' => $result['token']]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to generate Midtrans token', ['error' => $e->getMessage()]);
                    return redirect()->back()->withInput()
                        ->with('error', 'Gagal menghubungi payment gateway: ' . $e->getMessage());
                }
            }

            // Hapus cart setelah order dibuat
            Cart::where('user_id', $user->id)->delete();

            DB::commit();

            // Redirect berdasarkan payment method
            if ($request->payment_method === 'midtrans') {
                return redirect()->route('payment.page', $order->id);
            } else {
                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create order', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat order: ' . $e->getMessage());
        }
    }

    /* ==============================
       HALAMAN PAYMENT (Snap Midtrans)
    ============================== */
    public function paymentPage($orderId)
    {
        $order = Order::with('orderItems.product')->findOrFail($orderId);

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Update status order menjadi 'processing' (Sedang Dibuat)
        // ketika user masuk ke halaman payment
        if ($order->status === 'pending') {
            $order->update(['status' => 'processing']);
        }

        return view('dashboard.payment-page', compact('order'));
    }

    /* ==============================
       PROSES PEMBAYARAN DIRECT (Beli Langsung)
    ============================== */
    public function pay(Request $request, $id)
    {
        $user = Auth::user();
        $product = Product::findOrFail($id);

        $request->validate([
            'quantity' => 'required|integer|min:1',
            'alamat' => 'required|string|max:500',
            'nomor_telepon' => 'required|string|max:20',
        ]);

        DB::beginTransaction();

        try {
            $quantity = $request->quantity;
            $totalPrice = $product->price * $quantity;
            $orderCode = 'ORD-' . time() . '-' . $user->id;

            // Buat order
            $order = Order::create([
                'user_id' => $user->id,
                'product_id' => $product->id,
                'order_code' => $orderCode,
                'quantity' => $quantity,
                'total_price' => $totalPrice,
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
                'status' => 'pending',
                'alamat' => $request->alamat,
                'nomor_telepon' => $request->nomor_telepon,
            ]);

            // Buat order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'price' => $product->price,
            ]);

            $params = [
                'transaction_details' => [
                    'order_id' => $orderCode,
                    'gross_amount' => (int) $totalPrice,
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                    'phone' => $request->nomor_telepon,
                ],
                'item_details' => [
                    [
                        'id' => $product->id,
                        'price' => (int) $product->price,
                        'quantity' => $quantity,
                        'name' => $product->name,
                    ]
                ],
            ];

            // Generate Snap Token with SSL fix
            try {
                $result = MidtransHelper::getSnapToken($params);
                $snapToken = $result['token'];
            } catch (\Exception $e) {
                DB::rollBack();
                return response()->json(['error' => 'Gagal generate token: ' . $e->getMessage()], 500);
            }

            $order->update(['snap_token' => $snapToken]);

            DB::commit();

            return response()->json([
                'snap_token' => $snapToken,
                'order_id' => $order->id
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /* ==============================
       CALLBACK MIDTRANS (Webhook)
    ============================== */
    public function callback(Request $request)
    {
        try {
            $serverKey = config('midtrans.server_key');
            $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

            // Verify signature
            if ($hashed !== $request->signature_key) {
                Log::warning('Invalid Midtrans signature', [
                    'order_id' => $request->order_id,
                    'expected' => $hashed,
                    'received' => $request->signature_key
                ]);
                return response()->json(['message' => 'Invalid signature'], 403);
            }

            $order = Order::where('order_code', $request->order_id)->first();

            if (!$order) {
                Log::warning('Order not found in callback', ['order_code' => $request->order_id]);
                return response()->json(['message' => 'Order not found'], 404);
            }

            $transactionStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status ?? null;

            Log::info('Midtrans callback received', [
                'order_code' => $request->order_id,
                'transaction_status' => $transactionStatus,
                'fraud_status' => $fraudStatus,
            ]);

            // Update order status based on transaction status
            if ($transactionStatus == 'capture') {
                if ($fraudStatus == 'challenge') {
                    $order->update(['payment_status' => 'pending', 'status' => 'pending']);
                } else if ($fraudStatus == 'accept') {
                    $order->update(['payment_status' => 'paid', 'status' => 'processing']);
                }
            } else if ($transactionStatus == 'settlement') {
                $order->update(['payment_status' => 'paid', 'status' => 'processing']);
            } else if ($transactionStatus == 'pending') {
                $order->update(['payment_status' => 'pending', 'status' => 'pending']);
            } else if ($transactionStatus == 'deny') {
                $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
            } else if ($transactionStatus == 'expire') {
                $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
            } else if ($transactionStatus == 'cancel') {
                $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
            }

            return response()->json(['status' => 'success']);

        } catch (\Exception $e) {
            Log::error('Midtrans callback error', [
                'error' => $e->getMessage(),
                'order_id' => $request->order_id ?? null
            ]);
            return response()->json(['message' => 'Internal server error'], 500);
        }
    }

    /* ==============================
       FINISH PAYMENT (Redirect dari Midtrans)
    ============================== */
    public function finishPayment(Request $request, $orderId)
    {
        $order = Order::findOrFail($orderId);

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Cek status pembayaran dari Midtrans
        // Biasanya callback sudah update status, tapi kita cek ulang untuk keamanan
        
        return redirect()->route('payment.success', $order->id);
    }

    /* ==============================
       HALAMAN PAYMENT SUCCESS
    ============================== */
    public function paymentSuccess($orderId)
    {
        $order = Order::with('orderItems.product')->findOrFail($orderId);

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        return view('dashboard.payment-success', compact('order'));
    }

    /* ==============================
       BELI SEKARANG (Direct Checkout)
    ============================== */
    public function storeDirect(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'alamat' => 'required|string|max:500',
            'nomor_telepon' => 'required|string|max:20',
            'payment_method' => 'required|in:cod,midtrans,transfer_bca,transfer_bri,transfer_mandiri',
        ]);

        $user = Auth::user();
        $product = Product::findOrFail($request->product_id);

        // Validasi stok
        if ($request->quantity > $product->stock) {
            return redirect()->back()->with('error', 'Stok tidak mencukupi.');
        }

        DB::beginTransaction();

        try {
            $totalPrice = $product->price * $request->quantity;
            $orderCode = 'ORD-' . time() . '-' . $user->id;

            // Set payment deadline 24 jam dari sekarang untuk non-COD
            $paymentDeadline = null;
            if ($request->payment_method !== 'cod') {
                $paymentDeadline = now()->addHours(24);
            }

            // Buat order langsung
            $order = Order::create([
                'user_id' => $user->id,
                'order_code' => $orderCode,
                'total_price' => $totalPrice,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'payment_deadline' => $paymentDeadline,
                'status' => 'pending',
                'alamat' => $request->alamat,
                'nomor_telepon' => $request->nomor_telepon,
            ]);

            // Buat order item
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $product->id,
                'quantity' => $request->quantity,
                'price' => $product->price,
            ]);

            // Generate Snap Token jika payment method adalah Midtrans
            if ($request->payment_method === 'midtrans') {
                try {
                    $params = [
                        'transaction_details' => [
                            'order_id' => $orderCode,
                            'gross_amount' => (int) $totalPrice,
                        ],
                        'customer_details' => [
                            'first_name' => $user->name,
                            'email' => $user->email,
                            'phone' => $request->nomor_telepon,
                        ],
                        'item_details' => [
                            [
                                'id' => $product->id,
                                'price' => (int) $product->price,
                                'quantity' => $request->quantity,
                                'name' => $product->name,
                            ]
                        ],
                    ];

                    // Generate Snap Token
                    $result = MidtransHelper::getSnapToken($params);
                    $order->update(['snap_token' => $result['token']]);

                } catch (\Exception $e) {
                    DB::rollBack();
                    Log::error('Failed to generate Midtrans token', ['error' => $e->getMessage()]);
                    return redirect()->back()->withInput()
                        ->with('error', 'Gagal menghubungi payment gateway: ' . $e->getMessage());
                }
            }

            DB::commit();

            // Redirect berdasarkan payment method
            if ($request->payment_method === 'midtrans') {
                return redirect()->route('payment.page', $order->id);
            } else {
                return redirect()->route('orders.show', $order->id)
                    ->with('success', 'Pesanan berhasil dibuat! Silakan lakukan pembayaran sesuai metode yang dipilih.');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to create direct order', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->with('error', 'Gagal membuat pesanan: ' . $e->getMessage());
        }
    }

    /* ==============================
       KONFIRMASI PEMBAYARAN
    ============================== */
    public function confirmPayment(Order $order)
    {
        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        // Validasi hanya untuk order yang pending
        if ($order->payment_status !== 'pending') {
            return redirect()->route('orders.show', $order->id)
                ->with('error', 'Pesanan ini sudah dikonfirmasi sebelumnya.');
        }

        try {
            // Update status pembayaran dan pesanan
            $order->update([
                'payment_status' => 'paid',
                'status' => 'processing', // Ubah ke processing (menunggu dikirim)
            ]);

            return redirect()->route('orders.show', $order->id)
                ->with('success', 'Pembayaran berhasil dikonfirmasi! Pesanan Anda akan segera diproses.');

        } catch (\Exception $e) {
            Log::error('Failed to confirm payment', ['error' => $e->getMessage()]);
            return redirect()->back()->with('error', 'Gagal mengkonfirmasi pembayaran.');
        }
    }

    
    public function index()
    {
        $orders = Order::where('user_id', Auth::id())
                       ->with('orderItems.product')
                       ->latest()
                       ->paginate(10); // Ganti get() dengan paginate()
        return view('dashboard.order-history', compact('orders'));
    }

    public function show(Order $order)
    {
        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $order->load('orderItems.product');
        return view('dashboard.order-detail', compact('order'));
    }

    /**
     
     */
    public function filter($status = null)
    {
        $query = Order::where('user_id', Auth::id())
                     ->with('orderItems.product')
                     ->latest();
        
        // Filter by status if provided
        if ($status) {
            $query->where('payment_status', $status);
        }
        
        $orders = $query->paginate(10);
        
        // Available status options
        $statuses = ['pending', 'paid', 'failed', 'expired'];
        
        return view('dashboard.order-filter', compact('orders', 'status', 'statuses'));
    }

    /**
     * Track order - Show order tracking page with timeline
     */
    public function track(Order $order)
    {
        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized access');
        }

        $order->load('orderItems.product', 'user');
        return view('dashboard.order-tracking', compact('order'));
    }
}
