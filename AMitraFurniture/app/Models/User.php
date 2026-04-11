<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string $password
 * @property bool $is_admin
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 */
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'is_admin', // ✅ WAJIB
        'google_id',
        'avatar',
        'phone',
        'address',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // helper biar rapi
    public function isAdmin()
    {
        return $this->is_admin == 1;
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
