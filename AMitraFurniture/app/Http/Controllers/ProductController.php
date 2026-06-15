<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::orderBy('created_at', 'desc')->paginate(12);
        return Inertia::render('Product/Index', ['products' => $products]);
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return Inertia::render('Product/Show', ['product' => $product]);
    }

    /**
     * Show product detail page
     */
    public function detail($id)
    {
        $product = Product::findOrFail($id);
        
        // Ambil produk sejenis (kategori yang sama, exclude produk ini)
        $relatedProducts = Product::where('category', $product->category)
                                  ->where('id', '!=', $product->id)
                                  ->limit(4)
                                  ->get();
        
        // Load reviews with user data
        $reviews = $product->reviews()->with('user')->latest()->get();
        $averageRating = $product->reviews()->avg('rating');
        $totalReviews = $product->reviews()->count();
        
        return Inertia::render('Product/Detail', [
            'product' => $product,
            'relatedProducts' => $relatedProducts,
            'reviews' => $reviews,
            'averageRating' => $averageRating,
            'totalReviews' => $totalReviews,
        ]);
    }
    
    public function checkout($id)
    {
        $product = Product::findOrFail($id);
        return Inertia::render('Checkout/Direct', ['product' => $product]);
    }

    /**
     * Search products with optional keyword
     */
    public function search($keyword = null)
    {
        if ($keyword) {
            $products = Product::where('name', 'like', "%{$keyword}%")
                              ->orWhere('description', 'like', "%{$keyword}%")
                              ->orderBy('created_at', 'desc')
                              ->paginate(12);
        } else {
            $products = Product::orderBy('created_at', 'desc')->paginate(12);
        }
        
        return Inertia::render('Product/Search', [
            'products' => $products,
            'keyword' => $keyword,
        ]);
    }

}