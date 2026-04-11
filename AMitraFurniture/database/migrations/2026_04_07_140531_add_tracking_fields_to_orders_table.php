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
        Schema::table('orders', function (Blueprint $table) {
            $table->string('tracking_number')->nullable()->after('snap_token');
            $table->timestamp('confirmed_at')->nullable()->after('tracking_number');
            $table->timestamp('shipped_at')->nullable()->after('confirmed_at');
            $table->timestamp('delivered_at')->nullable()->after('shipped_at');
            $table->date('estimated_delivery')->nullable()->after('delivered_at');
            $table->text('tracking_notes')->nullable()->after('estimated_delivery');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'tracking_number',
                'confirmed_at',
                'shipped_at',
                'delivered_at',
                'estimated_delivery',
                'tracking_notes'
            ]);
        });
    }
};
