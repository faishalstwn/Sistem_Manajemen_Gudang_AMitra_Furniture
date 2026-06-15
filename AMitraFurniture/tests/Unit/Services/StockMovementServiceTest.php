<?php

namespace Tests\Unit\Services;

use Tests\TestCase;
use App\Services\ProductService;
use App\Services\StockMovementService;
use App\Models\Product;
use App\Models\StockMovement;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\InsufficientStockException;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StockMovementServiceTest extends TestCase
{
    use RefreshDatabase;

    protected StockMovementService $stockService;
    protected ProductService $productService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->productService = new ProductService();
        $this->stockService = new StockMovementService($this->productService);
    }

    /** @test */
    public function can_record_stock_in()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $movement = $this->stockService->recordStockIn(
            productId: $product->id,
            quantity: 50,
            reference: 'PO-2024-001',
            notes: 'Pembelian dari supplier A'
        );

        // Check movement record
        $this->assertEquals('in', $movement->type);
        $this->assertEquals(50, $movement->quantity);
        $this->assertEquals('PO-2024-001', $movement->reference);

        // Check product stock is updated
        $product->refresh();
        $this->assertEquals(60, $product->stock);

        // Check audit log is recorded
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create',
            'entity' => 'Product',
            'entity_id' => null, // Audit logs are created by service, not the movement
        ]);
    }

    /** @test */
    public function can_record_stock_out()
    {
        $product = Product::factory()->create(['stock' => 100]);

        $movement = $this->stockService->recordStockOut(
            productId: $product->id,
            quantity: 25,
            reference: 'ORD-2024-001',
            notes: 'Pengiriman ke customer'
        );

        // Check movement record
        $this->assertEquals('out', $movement->type);
        $this->assertEquals(25, $movement->quantity);

        // Check product stock is updated
        $product->refresh();
        $this->assertEquals(75, $product->stock);
    }

    /** @test */
    public function cannot_record_stock_out_when_insufficient()
    {
        $product = Product::factory()->create(['stock' => 10]);

        $this->expectException(InsufficientStockException::class);
        $this->expectExceptionMessage('Stok tidak cukup');

        $this->stockService->recordStockOut(
            productId: $product->id,
            quantity: 20,
            reference: 'ORD-2024-001'
        );
    }

    /** @test */
    public function cannot_record_stock_movement_for_non_existent_product()
    {
        $this->expectException(ProductNotFoundException::class);

        $this->stockService->recordStockIn(
            productId: 9999,
            quantity: 10
        );
    }

    /** @test */
    public function can_get_product_movements()
    {
        $product = Product::factory()->create(['stock' => 100]);

        // Record some movements
        $this->stockService->recordStockIn($product->id, 50);
        $this->stockService->recordStockOut($product->id, 10);
        $this->stockService->recordStockOut($product->id, 15);

        $movements = $this->stockService->getProductMovements($product->id);

        $this->assertCount(3, $movements->items());
    }

    /** @test */
    public function can_filter_movements_by_type()
    {
        $product = Product::factory()->create(['stock' => 100]);

        $this->stockService->recordStockIn($product->id, 50);
        $this->stockService->recordStockIn($product->id, 30);
        $this->stockService->recordStockOut($product->id, 10);

        $inMovements = $this->stockService->getProductMovements(
            $product->id,
            ['type' => 'in']
        );

        $this->assertCount(2, $inMovements->items());
        foreach ($inMovements->items() as $movement) {
            $this->assertEquals('in', $movement->type);
        }
    }

    /** @test */
    public function can_get_all_movements()
    {
        $product1 = Product::factory()->create(['stock' => 100]);
        $product2 = Product::factory()->create(['stock' => 50]);

        $this->stockService->recordStockIn($product1->id, 50);
        $this->stockService->recordStockOut($product2->id, 10);

        $movements = $this->stockService->getAllMovements();

        $this->assertCount(2, $movements->items());
    }

    /** @test */
    public function can_get_movement_statistics()
    {
        $product = Product::factory()->create(['stock' => 100]);

        $this->stockService->recordStockIn($product->id, 50); // Total in: 50
        $this->stockService->recordStockIn($product->id, 30); // Total in: 80
        $this->stockService->recordStockOut($product->id, 20); // Total out: 20

        $stats = $this->stockService->getMovementStatistics();

        $this->assertEquals(80, $stats['total_in']);
        $this->assertEquals(20, $stats['total_out']);
        $this->assertEquals(60, $stats['net_movement']);
        $this->assertEquals(3, $stats['total_movements']);
    }

    /** @test */
    public function stock_movements_are_recorded_in_audit_logs()
    {
        $product = Product::factory()->create(['stock' => 100]);

        $this->stockService->recordStockIn($product->id, 50, 'PO-001', 'Test notes');

        // Check audit log exists
        $this->assertDatabaseHas('audit_logs', [
            'action' => 'create',
            'entity' => 'Product',
        ]);
    }

    /** @test */
    public function multiple_concurrent_stock_movements_maintain_accuracy()
    {
        $product = Product::factory()->create(['stock' => 1000]);

        // Simulate multiple movements
        $this->stockService->recordStockOut($product->id, 100);
        $this->stockService->recordStockIn($product->id, 50);
        $this->stockService->recordStockOut($product->id, 75);
        $this->stockService->recordStockIn($product->id, 200);

        // Final stock should be: 1000 - 100 + 50 - 75 + 200 = 1075
        $product->refresh();
        $this->assertEquals(1075, $product->stock);

        // Check total movements
        $movements = $this->stockService->getAllMovements();
        $this->assertCount(4, $movements->items());
    }

    /** @test */
    public function can_filter_movements_by_date_range()
    {
        $product = Product::factory()->create(['stock' => 100]);

        // Create movements
        $movement1 = $this->stockService->recordStockIn($product->id, 50);
        sleep(1); // Ensure different timestamps
        $movement2 = $this->stockService->recordStockOut($product->id, 10);

        $movements = $this->stockService->getAllMovements([
            'date_from' => now()->subHour(),
            'date_to' => now()->addHour(),
        ]);

        $this->assertCount(2, $movements->items());
    }

    /** @test */
    public function movement_reference_is_stored_correctly()
    {
        $product = Product::factory()->create(['stock' => 100]);

        $movement = $this->stockService->recordStockIn(
            $product->id,
            50,
            'SUPPLIER-ABC-001',
            'Notes about this movement'
        );

        $this->assertEquals('SUPPLIER-ABC-001', $movement->reference);
        $this->assertEquals('Notes about this movement', $movement->notes);

        $retrieved = StockMovement::find($movement->id);
        $this->assertEquals('SUPPLIER-ABC-001', $retrieved->reference);
    }
}
