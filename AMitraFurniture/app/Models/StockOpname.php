<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    protected $fillable = [
        'kode',
        'user_id',
        'tanggal',
        'status',
        'catatan',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(StockOpnameItem::class);
    }

    public function totalSelisih(): int
    {
        return $this->items->sum(fn($item) => abs($item->selisih));
    }

    public function itemBerselisih(): int
    {
        return $this->items->where('selisih', '!=', 0)->count();
    }

    public static function generateKode(): string
    {
        $today = now()->format('Ymd');
        $last  = static::where('kode', 'like', "SO-{$today}-%")
                       ->orderByDesc('kode')
                       ->value('kode');

        if ($last) {
            $seq = (int) substr($last, -3) + 1;
        } else {
            $seq = 1;
        }

        return "SO-{$today}-" . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }
}
