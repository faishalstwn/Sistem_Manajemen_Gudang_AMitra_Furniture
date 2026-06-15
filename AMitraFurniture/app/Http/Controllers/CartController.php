<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class CartController extends Controller
{
    /**
     * Display the cart page
     */
    public function index()
    {
        // Ambil cart items dari database untuk user yang login
        $cartItems = Cart::where('user_id', Auth::id())
            ->with('product')
            ->get();

        // Hitung total
        $total = $cartItems->sum(function($cart) {
            return $cart->product->price * $cart->quantity;
        });

        return Inertia::render('Cart/Index', [
            'cartItems' => $cartItems,
            'total' => $total,
        ]);
    }

    /**
     * Add product to cart
     */
    public function add(Product $product, Request $request)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Anda harus login terlebih dahulu'
                ], 401);
            }
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu');
        }

        $validated = $request->validate([
            'quantity' => 'nullable|integer|min:1'
        ]);

        $quantity = $validated['quantity'] ?? 1;

        try {
            // Cek apakah produk sudah ada di cart user
            $cartItem = Cart::where('user_id', Auth::id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                // Jika sudah ada, tambah quantity
                $cartItem->increment('quantity', $quantity);
                $message = 'Jumlah produk di keranjang berhasil ditambah!';
            } else {
                // Jika belum ada, buat baru
                Cart::create([
                    'user_id' => Auth::id(),
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                ]);
                $message = 'Produk berhasil ditambahkan ke keranjang!';
            }
            
            // Jika request AJAX, return JSON
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => true,
                    'message' => $message,
                    'cart_count' => Cart::where('user_id', Auth::id())->sum('quantity')
                ]);
            }
            
            // Jika bukan AJAX, redirect back dengan flash message
            return redirect()->back()->with('success', $message);
            
        } catch (\Exception $e) {
            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan: ' . $e->getMessage()
                ], 500);
            }
            return redirect()->back()->with('error', 'Gagal menambahkan ke keranjang');
        }
    }

    /**
     * View cart (alias untuk index)
     */
    public function view()
    {
        return $this->index();
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
        
        if ($cartItem) {
            $cartItem->update(['quantity' => $validated['quantity']]);
            return redirect()->back()->with('success', 'Keranjang berhasil diupdate!');
        }

        return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Remove item from cart
     */
    public function remove($id)
    {
        $cartItem = Cart::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();
        
        if ($cartItem) {
            $cartItem->delete();
            return redirect()->back()->with('success', 'Produk berhasil dihapus dari keranjang!');
        }

        return redirect()->back()->with('error', 'Produk tidak ditemukan di keranjang.');
    }

    /**
     * Destroy/delete cart item (alias untuk remove)
     */
    public function destroy($id)
    {
        return $this->remove($id);
    }

    /**
     * Clear all cart items
     */
    public function clear()
    {
        Cart::where('user_id', Auth::id())->delete();
        
        return redirect()->back()->with('success', 'Keranjang berhasil dikosongkan!');
    }

    /**
     * Get cart item count (untuk badge di navbar)
     */
    public function count()
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');
        
        return response()->json(['count' => $count]);
    }
}