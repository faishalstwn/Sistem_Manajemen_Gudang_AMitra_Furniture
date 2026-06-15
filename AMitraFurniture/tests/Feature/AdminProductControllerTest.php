<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        // Create admin user
        $this->admin = User::factory()->create(['role' => 'admin']);
    }

    /** @test */
    public function admin_can_view_product_list()
    {
        Product::factory(5)->create();

        $response = $this->actingAs($this->admin)->get('/admin/products');

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    /** @test */
    public function admin_can_view_create_product_form()
    {
        $response = $this->actingAs($this->admin)->get('/admin/products/create');

        $response->assertStatus(200);
        $response->assertViewHas('categories');
    }

    /** @test */
    public function admin_can_create_product()
    {
        $data = [
            'name' => 'Kursi Baru',
            'description' => 'Kursi berkualitas tinggi',
            'category' => 'Kursi',
            'price' => 350000,
            'stock' => 10,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/products', $data);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', ['name' => 'Kursi Baru']);
    }

    /** @test */
    public function admin_cannot_create_product_without_required_fields()
    {
        $response = $this->actingAs($this->admin)
            ->post('/admin/products', [
                'name' => '',
                'category' => 'Kursi',
                'price' => '',
                'stock' => '',
            ]);

        $response->assertSessionHasErrors(['name', 'price', 'stock']);
    }

    /** @test */
    public function admin_cannot_create_product_with_duplicate_name()
    {
        Product::factory()->create(['name' => 'Kursi Kerja']);

        $data = [
            'name' => 'Kursi Kerja',
            'category' => 'Kursi',
            'price' => 350000,
            'stock' => 10,
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/products', $data);

        $response->assertSessionHasErrors('name');
    }

    /** @test */
    public function admin_can_view_edit_product_form()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
            ->get("/admin/products/{$product->id}/edit");

        $response->assertStatus(200);
        $response->assertViewHas('product', $product);
    }

    /** @test */
    public function admin_can_update_product()
    {
        $product = Product::factory()->create([
            'name' => 'Old Name',
            'price' => 100000,
        ]);

        $data = [
            'name' => 'New Name',
            'description' => 'Updated description',
            'category' => 'Kursi',
            'price' => 150000,
            'stock' => 20,
        ];

        $response = $this->actingAs($this->admin)
            ->put("/admin/products/{$product->id}", $data);

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'New Name',
            'price' => 150000,
        ]);
    }

    /** @test */
    public function admin_can_delete_product()
    {
        $product = Product::factory()->create();

        $response = $this->actingAs($this->admin)
            ->delete("/admin/products/{$product->id}");

        $response->assertRedirect('/admin/products');
        $this->assertDatabaseMissing('products', ['id' => $product->id]);
    }

    /** @test */
    public function admin_can_filter_products_by_status()
    {
        Product::factory(5)->create(['stock' => 0]); // habis
        Product::factory(5)->create(['stock' => 15]); // aman

        $response = $this->actingAs($this->admin)
            ->get('/admin/products?status=habis');

        $response->assertStatus(200);
        // Check that products are filtered
    }

    /** @test */
    public function admin_can_search_products()
    {
        Product::factory()->create(['name' => 'Kursi Kerja']);
        Product::factory()->create(['name' => 'Meja Makan']);

        $response = $this->actingAs($this->admin)
            ->get('/admin/products?cari=Kursi');

        $response->assertStatus(200);
    }

    /** @test */
    public function non_admin_cannot_create_product()
    {
        $user = User::factory()->create(['role' => 'customer']);

        $data = [
            'name' => 'Kursi Baru',
            'category' => 'Kursi',
            'price' => 350000,
            'stock' => 10,
        ];

        $response = $this->actingAs($user)
            ->post('/admin/products', $data);

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function unauthenticated_user_cannot_view_products()
    {
        $response = $this->get('/admin/products');

        $response->assertRedirect('/login');
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

        $this->actingAs($this->admin)
            ->post('/admin/products', $data);

        $product = Product::where('name', 'Kursi Kerja Ergonomis')->first();
        $this->assertNotNull($product->slug);
        $this->assertStringContainsString('kursi', strtolower($product->slug));
    }
}
