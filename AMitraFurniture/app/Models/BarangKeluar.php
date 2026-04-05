<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BarangKeluar extends Model
{
    protected $table = 'barang_keluar';

    protected $fillable = [
        'produk_id',
        'jumlah',
        'tujuan',
        'tanggal_keluar',
        'catatan',
    ];

    protected $casts = [
        'jumlah'         => 'integer',
        'tanggal_keluar' => 'date',
    ];

    public function produk()
    {
        return $this->belongsTo(Product::class, 'produk_id');
    }
}
