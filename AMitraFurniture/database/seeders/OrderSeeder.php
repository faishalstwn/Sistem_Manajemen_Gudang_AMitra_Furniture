<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\User;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::first();

        for ($i = 1; $i <= 10; $i++) {
            Order::create([
                'user_id'        => $user->id,
                'order_code'     => 'ORD-' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'total_price'    => rand(100000, 5000000),
                'status'         => 'pending',
                'payment_status' => 'pending',
                'alamat'         => 'Jl. Contoh No. ' . $i,
                'nomor_telepon'  => '08123456789' . $i,
            ]);
        }
    }
}