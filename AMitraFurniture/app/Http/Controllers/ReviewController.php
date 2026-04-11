<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReviewController extends Controller
{
    /**
     * Store a new review
     */
    public function store(Request $request)
    {
        $request->validate([
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        $order = Order::findOrFail($request->order_id);

        // Pastikan order milik user yang sedang login
        if ($order->user_id !== Auth::id()) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses ke pesanan ini.');
        }

        // Pastikan order sudah delivered
        if ($order->status !== 'delivered') {
            return redirect()->back()->with('error', 'Anda hanya bisa memberikan ulasan setelah pesanan diterima.');
        }

        // Cek apakah product ada di order ini
        $productInOrder = $order->orderItems()->where('product_id', $request->product_id)->exists();
        
        if (!$productInOrder) {
            return redirect()->back()->with('error', 'Produk tidak ada dalam pesanan ini.');
        }

        // Cek apakah user sudah pernah review product ini di order ini
        $existingReview = Review::where([
            'user_id' => Auth::id(),
            'product_id' => $request->product_id,
            'order_id' => $request->order_id,
        ])->first();

        if ($existingReview) {
            return redirect()->back()->with('error', 'Anda sudah memberikan ulasan untuk produk ini.');
        }

        try {
            DB::beginTransaction();

            Review::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
            ]);

            DB::commit();

            return redirect()->back()->with('success', 'Terima kasih! Ulasan Anda berhasil ditambahkan.');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan ulasan. Silakan coba lagi.');
        }
    }
}
