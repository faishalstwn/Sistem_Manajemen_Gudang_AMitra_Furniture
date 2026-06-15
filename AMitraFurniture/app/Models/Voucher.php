<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'type',
        'value',
        'min_order',
        'max_discount',
        'usage_limit',
        'used_count',
        'starts_at',
        'expires_at',
        'is_active',
    ];

    protected $casts = [
        'value' => 'decimal:2',
        'min_order' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'starts_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Scope: hanya voucher yang aktif dan dalam periode berlaku.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('starts_at')->orWhere('starts_at', '<=', now());
            })
            ->where(function ($q) {
                $q->whereNull('expires_at')->orWhere('expires_at', '>=', now());
            });
    }

    /**
     * Cek apakah voucher valid untuk digunakan.
     */
    public function isValid(float $subtotal = 0): array
    {
        if (!$this->is_active) {
            return ['valid' => false, 'message' => 'Voucher tidak aktif.'];
        }

        if ($this->starts_at && $this->starts_at->isFuture()) {
            return ['valid' => false, 'message' => 'Voucher belum berlaku.'];
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return ['valid' => false, 'message' => 'Voucher sudah kedaluwarsa.'];
        }

        if ($this->usage_limit !== null && $this->used_count >= $this->usage_limit) {
            return ['valid' => false, 'message' => 'Voucher sudah mencapai batas pemakaian.'];
        }

        if ($this->min_order && $subtotal < $this->min_order) {
            return [
                'valid' => false,
                'message' => 'Minimal belanja Rp ' . number_format($this->min_order, 0, ',', '.') . ' untuk menggunakan voucher ini.',
            ];
        }

        return ['valid' => true, 'message' => 'Voucher berhasil diterapkan!'];
    }

    /**
     * Hitung jumlah diskon berdasarkan subtotal.
     */
    public function calculateDiscount(float $subtotal): float
    {
        if ($this->type === 'percentage') {
            $discount = $subtotal * ($this->value / 100);

            // Terapkan batas maksimum diskon jika ada
            if ($this->max_discount !== null && $discount > $this->max_discount) {
                $discount = (float) $this->max_discount;
            }

            return round($discount);
        }

        // Tipe fixed: diskon langsung, tidak melebihi subtotal
        return min((float) $this->value, $subtotal);
    }

    /**
     * Relasi: orders yang menggunakan voucher ini.
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
