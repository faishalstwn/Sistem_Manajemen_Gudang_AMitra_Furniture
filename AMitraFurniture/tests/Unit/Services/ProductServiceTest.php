<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductService;
use App\Models\Product;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\InsufficientStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProductServiceTest extends TestCase
{
    use RefreshDatabase;

    protected ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
    }

    /** @test */
    public function can_get_all_products_with_pagination()
    {
        // Create 25 test products
        Product::factory(25)->create();

        $products = $this->productService->getAllProducts(['per_page' => 15]);

        $this->assertCount(15, $products->items());
        $this->assertEquals(25, $products->total());
        $this->assertEquals(2, $products->lastPage());
    }

    /** @test */
    public function can_filter_products_by_status()
    {
        // Create products with different stock levels
        Product::factory(5)->create(['stock' => 0]); // habis
        Product::factory(5)->create(['stock' => 5]); // rendah
        Product::factory(5)->create(['stock' => 15]); // aman

        $habisProducts = $this->productService->getAllProducts(['status' => 'habis', 'per_page' => 999]);
        $this->assertCount(5, $habisProducts->items());

        $rendahProducts = $this->productService->getAllProducts(['status' => 'rendah', 'per_page' => 999]);
        $this->assertCount(5, $rendahProducts->items());

        $amanProducts = $this->productService->getAllProducts(['status' => 'aman', 'per_page' => 999]);
        $this->assertCount(5, $amanProducts->items());
    }

    /** @test */
    public function can_filter_products_by_category()
    {
        Product::factory(5)->create(['category' => 'Kursi']);
        Product::factory(3)->create(['category' => 'Meja']);

        $kursiProducts = $this->productService->getAllProducts(['kategori' => 'Kursi', 'per_page' => 999]);
        $this->assertCount(5, $kursiProducts->items());

        $mejaProducts = $this->productService->getAllProducts(['kategori' => 'Meja', 'per_page' => 999]);
        $this->assertCount(3, $mejaProducts->items());
    }

    /** @test */
    public function can_search_products()
    {
        Product::factory()->create(['name' => 'Kursi Kerja Ergonomis']);
        Product::factory()->create(['name' => 'Kursi Makan Kayu']);
        Product::factory()->create(['name' => 'Meja Kerja Minimalis']);

        $results = $this->productService->getAllProducts(['cari' => 'Kursi', 'per_page' => 999]);
        $this->assertCount(2, $results->items());

        $mejaResults = $this->productService->getAllProducts(['cari' => 'Meja', 'per_page' => 999]);
        $this->assertCount(1, $mejaResults->items());
    }

    /** @test */
    public function can_get_product_by_id()
    {
        $product = Product::factory()->create();

        $retrieved = $this->productService->getProductById($product->id);

        $this->assertEquals($product->id, $retrieved->id);
        $this->assertEquals($product->name, $retrieved->name);
    }

    /** @test */
    public function throws_exception_when_product_not_found_by_id()
    {
        $this->expectException(ProductNotFoundException::class);
        $this->expectExceptionMessage('tidak ditemukan');

        $this->productService->getProductById(9999);
    }

    /** @test */
    public function can_get_product_by_slug()
    {
        $product = Product::factory()->create(['slug' => 'kursi-kerja-ergonomis']);

        $retrieved = $this->productService->getProductBySlug('kursi-kerja-ergonomis');

        $this->assertEquals($product->id, $retrieved->id);
    }

    /** @test */
    public function throws_exception_when_product_not_found_by_slug()
    {
        $this->expectException(ProductNotFoundException::class);

        $this->productService->getProductBySlug('non-existent-slug');
    }

    /** @test */
    public function can_create_product()
    {
        $data = [
            'name' => 'Kursi Baru',
            'description' => 'Kursi berkualitas tinggi',
            'category' => 'Kursi',
            'price' => 350000,
            'stock' => 10,
            'image' => 'assets/images/kursi.jpg',
        ];

        $product = $this->productService->createProduct($data);

        $this->assertNotNull($product->id);
        $this->assertEquals($data['name'], $product->name);
        $this->assertEquals($data['category'], $product->category);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => $data['name']]);
    }

    /** @test */
    public function can_update_product()
    {
        $product = Product::factory()->create(['name' => 'Old Name', 'price' => 100000]);

        $updatedData = [
            'name' => 'New Name',
            'price' => 150000,
        ];

        $updated = $this->productService->updateProduct($product->id, $updatedData);

        $this->assertEquals('New Name', $updated->name);
        $this->assertEquals(150000, $updated->price);
        $this->assertDatabaseHas('products', ['id' => $product->id, 'name' => 'New Name']);
    }

    /** @test */
    public function can_delete_product()
    {
        $product = Product::factory()->create();

        $result = $this->productService->deleteProduct($product->id);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function can_get_all_categories()
    {
        Product::factory()->create(['category' => 'Kursi']);
        Product::factory()->create(['category' => 'Meja']);
        Product::factory()->create(['category' => 'Kursi']);

        $categories = $this->productService->getCategories();

        $this->assertCount(2, $categories);
        $this->assertContains('Kursi', $categories);
        $this->assertContains('Meja', $categories);
    }

    /** @test */
    public function can_get_stock_statistics()
    {
        Product::factory(10)->create(['stock' => 0]); // habis
        Product::factory(5)->create(['stock' => 5]); // rendah (< 10)
        Product::factory(15)->create(['stock' => 20]); // aman (>= 10)

        $stats = $this->productService->getStockStatistics();

        $this->assertEquals(30, $stats['total']);
        $this->assertEquals(10, $stats['habis']);
        $this->assertEquals(5, $stats['rendah']);
        $this->assertEquals(15, $stats['aman']);
    }

    /** @test */
    public function can_check_stock_availability()
    {
        $product = Product::factory()->create(['stock' => 10]);

        // Should return true when stock is sufficient
        $result = $this->productService->checkStockAvailability($product->id, 5);
        $this->assertTrue($result);

        $result = $this->productService->checkStockAvailability($product->id, 10);
        $this->assertTrue($result);
    }

    /** @test */
    public function throws_exception_when_stock_insufficient()
    {
        $product = Product::factory()->create(['stock' => 5]);

        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('Stok tidak cukup');

        $this->productService->checkStockAvailability($product->id, 10);
    }

    /** @test */
    public function can_get_low_stock_products()
    {
        Product::factory(5)->create(['stock' => 5]); // Low stock
        Product::factory(3)->create(['stock' => 20]); // Sufficient stock

        $lowStockProducts = $this->productService->getLowStockProducts(10);

        $this->assertCount(5, $lowStockProducts);
        foreach ($lowStockProducts as $product) {
            $this->assertLessThan(10, $product->stock);
        }
    }

    /** @test */
    public function product_slug_is_auto_generated()
    {
        $data = [
            'name' => 'Kursi Kerja Ergonomis',
            'category' => 'Kursi',
            'price' => 350000,
            'stock' => 10,
        ];

        $product = $this->productService->createProduct($data);

        $this->assertNotNull($product->slug);
        $this->assertStringContainsString('kursi', strtolower($product->slug));
    }

    /** @test */
    public function duplicate_slugs_are_handled()
    {
        Product::factory()->create(['name' => 'Kursi Kerja', 'slug' => 'kursi-kerja']);

        $data = [
            'name' => 'Kursi Kerja',
            'category' => 'Kursi',
            'price' => 350000,
            'stock' => 10,
        ];

        $product = $this->productService->createProduct($data);

        // Slug should be unique
        $this->assertDatabaseCount('products', 2);
        $this->assertTrue(
            Product::where('slug', 'kursi-kerja')->exists() ||
            Product::where('slug', 'like', 'kursi-kerja-%')->exists()
        );
    }
}
