<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WarehouseLocation extends Model
{
    protected $fillable = [
        'kode',
        'zona',
        'baris',
        'kolom',
        'tipe',
        'kapasitas',
        'keterangan',
    ];

    protected $casts = [
        'baris'     => 'integer',
        'kolom'     => 'integer',
        'kapasitas' => 'integer',
    ];

    protected $appends = ['total_terisi', 'sisa_kapasitas'];

    public function getTotalTerisiAttribute()
    {
        return $this->totalTerisi();
    }

    public function getSisaKapasitasAttribute()
    {
        return $this->sisaKapasitas();
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'location_product')
                    ->withPivot('jumlah')
                    ->withTimestamps();
    }

    /**
     * Hitung total unit yang tersimpan di lokasi ini
     */
    public function totalTerisi(): int
    {
        return (int) $this->products->sum('pivot.jumlah');
    }

    /**
     * Sisa kapasitas yang tersedia
     */
    public function sisaKapasitas(): int
    {
        return max(0, $this->kapasitas - $this->totalTerisi());
    }

    /**
     * Persentase terisi
     */
    public function persentaseTerisi(): float
    {
        if ($this->kapasitas <= 0) return 0;
        return round(($this->totalTerisi() / $this->kapasitas) * 100, 1);
    }
}
