<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouse_locations', function (Blueprint $table) {
            $table->id();
            $table->string('kode')->unique();          // contoh: A1, B2, C3
            $table->string('zona');                     // contoh: Zona A, Zona B
            $table->integer('baris');                   // posisi baris di grid
            $table->integer('kolom');                   // posisi kolom di grid
            $table->string('tipe')->default('rak');     // rak, lantai, palet
            $table->integer('kapasitas')->default(100); // kapasitas maks unit
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // Pivot: satu lokasi bisa simpan banyak produk, satu produk bisa di banyak lokasi
        Schema::create('location_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('warehouse_location_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->integer('jumlah')->default(0);
            $table->timestamps();

            $table->unique(['warehouse_location_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('location_product');
        Schema::dropIfExists('warehouse_locations');
    }
};
