<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class HomeController extends Controller
{
    /**
     * Display the home page with products
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filter berdasarkan kategori
        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        // Filter berdasarkan pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%')
                  ->orWhere('category', 'like', '%' . $request->search . '%');
            });
        }

        // Ambil produk dengan pagination
        $products = $query->orderBy('created_at', 'desc')->paginate(8);

        // Append query parameters ke pagination links
        $products->appends($request->all());

        return view('dashboard.home', compact('products'));
    }

   
     
    public function category($category)
    {
        // Validasi kategori yang tersedia
        $validCategories = ['Kursi', 'Sofa', 'Meja', 'Lemari'];
        
        if (!in_array($category, $validCategories)) {
            abort(404, 'Kategori tidak ditemukan');
        }

        // Ambil produk berdasarkan kategori
        $products = Product::where('category', $category)
                          ->orderBy('created_at', 'desc')
                          ->paginate(12);

        return view('dashboard.category', compact('products', 'category'));
    }
}