<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpnameItem extends Model
{
    protected $fillable = [
        'stock_opname_id',
        'product_id',
        'stok_sistem',
        'stok_fisik',
        'selisih',
        'keterangan',
    ];

    protected $casts = [
        'stok_sistem' => 'integer',
        'stok_fisik'  => 'integer',
        'selisih'     => 'integer',
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getStatusSelisihAttribute(): string
    {
        if ($this->selisih > 0) return 'surplus';
        if ($this->selisih < 0) return 'kurang';
        return 'cocok';
    }
}
