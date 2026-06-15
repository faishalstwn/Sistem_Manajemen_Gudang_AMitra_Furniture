<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Voucher;

class VoucherSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $vouchers = [
            [
                'code' => 'DISKON50',
                'name' => 'Big Sale 50%',
                'type' => 'percentage',
                'value' => 50,
                'min_order' => 200000,
                'max_discount' => 500000,
                'usage_limit' => 100,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(3),
                'is_active' => true,
            ],
            [
                'code' => 'HEMAT20',
                'name' => 'Hemat 20%',
                'type' => 'percentage',
                'value' => 20,
                'min_order' => 100000,
                'max_discount' => null,
                'usage_limit' => null,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(6),
                'is_active' => true,
            ],
            [
                'code' => 'POTONGAN100K',
                'name' => 'Potongan Rp100.000',
                'type' => 'fixed',
                'value' => 100000,
                'min_order' => 500000,
                'max_discount' => null,
                'usage_limit' => 50,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addMonths(2),
                'is_active' => true,
            ],
            [
                'code' => 'WELCOME10',
                'name' => 'Welcome Discount 10%',
                'type' => 'percentage',
                'value' => 10,
                'min_order' => null,
                'max_discount' => 200000,
                'usage_limit' => null,
                'used_count' => 0,
                'starts_at' => now(),
                'expires_at' => now()->addYear(),
                'is_active' => true,
            ],
        ];

        foreach ($vouchers as $voucher) {
            Voucher::updateOrCreate(
                ['code' => $voucher['code']],
                $voucher
            );
        }
    }
}
