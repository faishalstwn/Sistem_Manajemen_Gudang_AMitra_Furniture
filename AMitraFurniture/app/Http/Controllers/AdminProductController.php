<?php

namespace App\Http\Controllers;

use App\Services\ProductService;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Exceptions\ProductNotFoundException;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminProductController extends Controller
{
    protected ProductService $productService;

    public function __construct(ProductService $productService)
    {
        $this->productService = $productService;
    }

    public function index(Request $request)
    {
        $products = $this->productService->getAllProducts([
            'status' => $request->status,
            'kategori' => $request->kategori,
            'cari' => $request->cari,
            'sort_by' => $request->sort_by ?? 'stock',
            'sort_order' => $request->sort_order ?? 'asc',
            'per_page' => 15,
        ]);

        $stats = $this->productService->getStockStatistics();
        $categories = $this->productService->getCategories();
        $lowStockProducts = $this->productService->getLowStockProducts(10);

        return Inertia::render('Admin/Produk/Index', [
            'products' => $products,
            'stats' => $stats,
            'categories' => $categories,
            'lowStockProducts' => $lowStockProducts,
            'filters' => $request->only(['status', 'kategori', 'cari', 'sort_by', 'sort_order']),
        ]);
    }

    public function create()
    {
        $categories = $this->productService->getCategories();
        return Inertia::render('Admin/Produk/Create', ['categories' => $categories]);
    }

    public function store(StoreProductRequest $request)
    {
        try {
            $data = $request->validated();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('assets/images'), $imageName);
                $data['image'] = 'assets/images/' . $imageName;
            }

            $product = $this->productService->createProduct($data);

            return redirect()->route('admin.produk.index')
                ->with('success', "Produk '{$product->name}' berhasil ditambahkan");
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal menambahkan produk: ' . $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $product = $this->productService->getProductById($id);
            $categories = $this->productService->getCategories();
            return Inertia::render('Admin/Produk/Edit', [
                'product' => $product,
                'categories' => $categories,
            ]);
        } catch (ProductNotFoundException $e) {
            return redirect()->route('admin.produk.index')
                ->withErrors(['error' => $e->getMessage()]);
        }
    }

    public function update(UpdateProductRequest $request, $id)
    {
        try {
            $product = $this->productService->getProductById($id);
            $data = $request->validated();

            if ($request->hasFile('image')) {
                if ($product->image && file_exists(public_path($product->image))) {
                    unlink(public_path($product->image));
                }

                $image = $request->file('image');
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('assets/images'), $imageName);
                $data['image'] = 'assets/images/' . $imageName;
            }

            $updatedProduct = $this->productService->updateProduct($id, $data);

            return redirect()->route('admin.produk.index')
                ->with('success', "Produk '{$updatedProduct->name}' berhasil diperbarui");
        } catch (ProductNotFoundException $e) {
            return redirect()->route('admin.produk.index')
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['error' => 'Gagal mengupdate produk: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        try {
            $product = $this->productService->getProductById($id);
            $productName = $product->name;
            
            $this->productService->deleteProduct($id);

            return redirect()->route('admin.produk.index')
                ->with('success', "Produk '{$productName}' berhasil dihapus");
        } catch (ProductNotFoundException $e) {
            return redirect()->route('admin.produk.index')
                ->withErrors(['error' => $e->getMessage()]);
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['error' => 'Gagal menghapus produk: ' . $e->getMessage()]);
        }
    }
}
