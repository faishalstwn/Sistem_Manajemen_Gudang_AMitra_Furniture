<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add indexes to products table
        Schema::table('products', function (Blueprint $table) {
            $table->index('category');
            $table->index('stock');
            $table->index('created_at');
            $table->index('slug');
        });

        // Add indexes to stock_movements table
        Schema::table('stock_movements', function (Blueprint $table) {
            $table->index('product_id');
            $table->index('type');
            $table->index('created_at');
            $table->index(['product_id', 'type']);
        });

        // Add indexes to orders table
        Schema::table('orders', function (Blueprint $table) {
            $table->index('user_id');
            $table->index('status');
            $table->index('created_at');
        });

        // Add indexes to order_items table
        Schema::table('order_items', function (Blueprint $table) {
            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Use DB statement to safely drop indexes
            try {
                $table->dropIndex(['category']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['stock']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['created_at']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['slug']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
        });

        Schema::table('stock_movements', function (Blueprint $table) {
            try {
                $table->dropIndex(['product_id']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['type']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['created_at']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['product_id', 'type']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
        });

        Schema::table('orders', function (Blueprint $table) {
            try {
                $table->dropIndex(['user_id']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['status']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['created_at']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
        });

        Schema::table('order_items', function (Blueprint $table) {
            try {
                $table->dropIndex(['order_id']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
            
            try {
                $table->dropIndex(['product_id']);
            } catch (\Exception $e) {
                // Index doesn't exist, skip
            }
        });
    }
};
