<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['in', 'out', 'adjustment'])->comment('in=stok masuk, out=stok keluar, adjustment=koreksi');
            $table->integer('quantity')->comment('Jumlah unit bergerak (selalu positif)');
            $table->integer('previous_stock')->comment('Stok sebelum pergerakan');
            $table->integer('new_stock')->comment('Stok setelah pergerakan');
            $table->string('reference')->nullable()->comment('Nomor pesanan / referensi');
            $table->text('note')->nullable()->comment('Catatan tambahan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
    }
};
