<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int $id
 * @property int $user_id
 * @property int|null $product_id
 * @property string $order_code
 * @property int|null $quantity
 * @property float $total_price
 * @property string|null $payment_method
 * @property string $payment_status
 * @property \Illuminate\Support\Carbon|null $payment_deadline
 * @property string $status
 * @property string|null $snap_token
 * @property string|null $alamat
 * @property string|null $nomor_telepon
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'product_id',
        'order_code',
        'quantity',
        'total_price',
        'payment_method',
        'payment_status',
        'payment_deadline',
        'status',
        'snap_token',
        'alamat',
        'nomor_telepon',
        'tracking_number',
        'confirmed_at',
        'shipped_at',
        'delivered_at',
        'estimated_delivery',
        'tracking_notes',
    ];

    protected $casts = [
        'payment_deadline' => 'datetime',
        'confirmed_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
        'estimated_delivery' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}