<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangMasuk extends Model
{
    protected $table = 'barang_masuk';

    protected $fillable = [
        'produk_id',
        'jumlah',
        'tanggal_masuk',
        'supplier',
        'catatan',
    ];

    protected $casts = [
        'jumlah'       => 'integer',
        'tanggal_masuk' => 'date',
    ];

    public function produk()
    {
        return $this->belongsTo(Product::class, 'produk_id');
    }
}
