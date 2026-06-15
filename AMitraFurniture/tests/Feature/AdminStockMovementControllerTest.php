<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AdminStockMovementControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create(['role' => 'admin']);
        $this->product = Product::factory()->create(['stock' => 100]);
    }

    /** @test */
    public function admin_can_view_barang_masuk_list()
    {
        $response = $this->actingAs($this->admin)->get('/admin/barang-masuk');

        $response->assertStatus(200);
        $response->assertViewHas('movements');
    }

    /** @test */
    public function admin_can_view_barang_masuk_create_form()
    {
        $response = $this->actingAs($this->admin)->get('/admin/barang-masuk/create');

        $response->assertStatus(200);
        $response->assertViewHas('products');
    }

    /** @test */
    public function admin_can_record_barang_masuk()
    {
        $initialStock = $this->product->stock;

        $data = [
            'product_id' => $this->product->id,
            'quantity' => 50,
            'type' => 'in',
            'reference' => 'PO-2024-001',
            'notes' => 'Pembelian dari supplier',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/barang-masuk', $data);

        $response->assertRedirect('/admin/barang-masuk');

        $this->product->refresh();
        $this->assertEquals($initialStock + 50, $this->product->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 50,
        ]);
    }

    /** @test */
    public function admin_can_view_barang_keluar_list()
    {
        $response = $this->actingAs($this->admin)->get('/admin/barang-keluar');

        $response->assertStatus(200);
        $response->assertViewHas('movements');
    }

    /** @test */
    public function admin_can_view_barang_keluar_create_form()
    {
        $response = $this->actingAs($this->admin)->get('/admin/barang-keluar/create');

        $response->assertStatus(200);
        $response->assertViewHas('productsWithStock');
    }

    /** @test */
    public function admin_can_record_barang_keluar()
    {
        $initialStock = $this->product->stock;

        $data = [
            'product_id' => $this->product->id,
            'quantity' => 25,
            'type' => 'out',
            'reference' => 'ORD-2024-001',
            'notes' => 'Pengiriman ke customer',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/barang-keluar', $data);

        $response->assertRedirect('/admin/barang-keluar');

        $this->product->refresh();
        $this->assertEquals($initialStock - 25, $this->product->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 25,
        ]);
    }

    /** @test */
    public function admin_cannot_record_barang_keluar_with_insufficient_stock()
    {
        $data = [
            'product_id' => $this->product->id,
            'quantity' => 150, // Product only has 100
            'type' => 'out',
            'reference' => 'ORD-2024-001',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/barang-keluar', $data);

        $response->assertSessionHasErrors('error');
        $this->assertDatabaseMissing('stock_movements', [
            'product_id' => $this->product->id,
            'quantity' => 150,
        ]);
    }

    /** @test */
    public function admin_cannot_record_movement_for_invalid_product()
    {
        $data = [
            'product_id' => 9999,
            'quantity' => 50,
            'type' => 'in',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/barang-masuk', $data);

        $response->assertSessionHasErrors();
    }

    /** @test */
    public function admin_cannot_record_movement_without_required_fields()
    {
        $data = [
            'product_id' => '',
            'quantity' => '',
            'type' => '',
        ];

        $response = $this->actingAs($this->admin)
            ->post('/admin/barang-masuk', $data);

        $response->assertSessionHasErrors(['product_id', 'quantity', 'type']);
    }

    /** @test */
    public function admin_can_filter_movements_by_date()
    {
        $response = $this->actingAs($this->admin)
            ->get('/admin/barang-masuk?dari=2024-01-01&sampai=2024-12-31');

        $response->assertStatus(200);
    }

    /** @test */
    public function warehouse_officer_can_record_barang_masuk()
    {
        $officer = User::factory()->create(['role' => 'warehouse_officer']);

        $data = [
            'product_id' => $this->product->id,
            'quantity' => 30,
            'type' => 'in',
            'reference' => 'PO-2024-002',
        ];

        $response = $this->actingAs($officer)
            ->post('/admin/barang-masuk', $data);

        // Should be allowed for warehouse officer
        $response->assertStatus(302); // Redirect on success
    }

    /** @test */
    public function non_warehouse_user_cannot_record_movements()
    {
        $customer = User::factory()->create(['role' => 'customer']);

        $data = [
            'product_id' => $this->product->id,
            'quantity' => 30,
            'type' => 'in',
        ];

        $response = $this->actingAs($customer)
            ->post('/admin/barang-masuk', $data);

        $response->assertStatus(403); // Forbidden
    }

    /** @test */
    public function can_get_barang_masuk_statistics()
    {
        // Record some barang masuk
        $data = [
            'product_id' => $this->product->id,
            'quantity' => 50,
            'type' => 'in',
        ];
        $this->actingAs($this->admin)->post('/admin/barang-masuk', $data);

        $response = $this->actingAs($this->admin)
            ->get('/admin/barang-masuk/statistics');

        $response->assertStatus(200);
        $response->assertJsonStructure(['status', 'data']);
    }

    /** @test */
    public function stock_movements_create_audit_logs()
    {
        $data = [
            'product_id' => $this->product->id,
            'quantity' => 50,
            'type' => 'in',
            'reference' => 'PO-2024-001',
        ];

        $this->actingAs($this->admin)->post('/admin/barang-masuk', $data);

        // Check audit log is created
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create',
        ]);
    }
}
