<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'product_id',
        'user_id',
        'type',
        'quantity',
        'previous_stock',
        'new_stock',
        'reference',
        'note',
    ];

    protected $casts = [
        'quantity'       => 'integer',
        'previous_stock' => 'integer',
        'new_stock'      => 'integer',
    ];

    // ── Relations ──────────────────────────────────────────────────────

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ── Scopes ─────────────────────────────────────────────────────────

    public function scopeIn($query)
    {
        return $query->where('type', 'in');
    }

    public function scopeOut($query)
    {
        return $query->where('type', 'out');
    }

    public function scopeAdjustment($query)
    {
        return $query->where('type', 'adjustment');
    }

    // ── Helpers ────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'in'         => 'Stok Masuk',
            'out'        => 'Stok Keluar',
            'adjustment' => 'Koreksi Stok',
            default      => ucfirst($this->type),
        };
    }

    public function getTypeBadgeAttribute(): string
    {
        return match ($this->type) {
            'in'         => 'bg-success',
            'out'        => 'bg-danger',
            'adjustment' => 'bg-info',
            default      => 'bg-secondary',
        };
    }

    public function getSignedQuantityAttribute(): string
    {
        return match ($this->type) {
            'in'  => '+' . $this->quantity,
            'out' => '-' . $this->quantity,
            default => ($this->new_stock >= $this->previous_stock ? '+' : '-') . abs($this->new_stock - $this->previous_stock),
        };
    }
}
