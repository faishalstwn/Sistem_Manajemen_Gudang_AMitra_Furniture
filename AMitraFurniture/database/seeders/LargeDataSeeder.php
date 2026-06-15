<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class LargeDataSeeder extends Seeder
{
    public function run(): void
    {
        // Hapus data lama dulu supaya tidak duplikat
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        DB::table('stock_movements')->truncate();
        DB::table('order_items')->truncate();
        DB::table('orders')->delete();
        DB::table('products')->delete();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->command->info('Seeding 1000 products...');

        $categories = ['Sofa', 'Kursi', 'Meja', 'Lemari', 'Rak'];
        $productNames = [
            'Sofa'   => ['Sofa Minimalis Modern', 'Sofa L-Shape Premium', 'Sofa Bed Multifungsi', 'Sofa Klasik Eropa', 'Sofa Scandinavian'],
            'Kursi'  => ['Kursi Kerja Ergonomis', 'Kursi Makan Kayu', 'Kursi Tamu Mewah', 'Kursi Gaming', 'Kursi Elbow'],
            'Meja'   => ['Meja Makan Kayu Jati', 'Meja Kerja Minimalis', 'Meja Kopi Bundar', 'Meja Belajar Anak', 'Meja Rias'],
            'Lemari' => ['Lemari Pakaian 3 Pintu', 'Lemari Buku Minimalis', 'Lemari Dapur Modern', 'Lemari Sepatu', 'Lemari Sliding'],
            'Rak'    => ['Rak Buku Minimalis', 'Rak Dinding Floating', 'Rak Dapur', 'Rak TV Modern', 'Rak Sudut'],
        ];

        $descriptions = [
            'Sofa'   => 'Sofa berkualitas tinggi dengan desain modern dan nyaman untuk ruang tamu.',
            'Kursi'  => 'Kursi ergonomis dengan material premium dan desain yang elegan.',
            'Meja'   => 'Meja kokoh berbahan kayu pilihan dengan finishing halus dan tahan lama.',
            'Lemari' => 'Lemari spacious dengan desain minimalis dan material berkualitas.',
            'Rak'    => 'Rak multifungsi dengan desain simpel dan daya tampung besar.',
        ];

        // Mapping gambar untuk setiap kategori
        $categoryImages = [
            'Sofa'   => ['assets/images/dan7.jpg', 'assets/images/dan8.jpg', 'assets/images/sofasantai.jpeg', 'assets/images/dan2.jpg'],
            'Kursi'  => ['assets/images/kursi kerja.jpg', 'assets/images/kursikayujati.jpeg', 'assets/images/kursirotan.jpeg', 'assets/images/kursisantai.jpeg', 'assets/images/Elbow Chair.jpeg', 'assets/images/kursigoyang.jpeg', 'assets/images/kursikayumahoni.jpeg'],
            'Meja'   => ['assets/images/meja makan kayu jati.jpg', 'assets/images/meja minimalis.jpeg', 'assets/images/meja kerja minimalis.jpeg', 'assets/images/mejakerja.jpg', 'assets/images/laci meja.jpg'],
            'Lemari' => ['assets/images/Lemari.png', 'assets/images/dan3.jpg', 'assets/images/dan4.jpg'],
            'Rak'    => ['assets/images/dan5.jpg', 'assets/images/dan6.jpg', 'assets/images/dan1.jpg'],
        ];

        $products = [];
        for ($i = 1; $i <= 1000; $i++) {
            $category = $categories[array_rand($categories)];
            $names    = $productNames[$category];
            $name     = $names[array_rand($names)] . ' ' . $i;
            // Tambah timestamp untuk pastikan slug unik
            $slug     = strtolower(str_replace(' ', '-', $name)) . '-' . $i;

            // Ambil gambar yang sesuai kategori
            $categoryImageList = $categoryImages[$category];
            $image = $categoryImageList[array_rand($categoryImageList)];

            $products[] = [
                'name'        => $name,
                'description' => $descriptions[$category],
                'category'    => $category,
                'price'       => rand(500000, 10000000),
                'stock'       => rand(0, 100),
                'image'       => $image,
                'slug'        => $slug,
                'created_at'  => Carbon::now()->subDays(rand(0, 365)),
                'updated_at'  => Carbon::now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($products, 100) as $chunk) {
            DB::table('products')->insert($chunk);
        }

        $this->command->info('✅ 1000 products seeded!');

        $this->command->info('Seeding 500 orders...');

        $userId          = DB::table('users')->first()->id;
        $statuses        = ['pending', 'processing', 'shipped', 'delivered', 'cancelled'];
        $paymentStatuses = ['pending', 'paid', 'failed'];

        $orders = [];
        for ($i = 1; $i <= 500; $i++) {
            $status        = $statuses[array_rand($statuses)];
            $paymentStatus = $paymentStatuses[array_rand($paymentStatuses)];

            $orders[] = [
                'user_id'        => $userId,
                'order_code'     => 'ORD-' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'total_price'    => rand(500000, 20000000),
                'status'         => $status,
                'payment_status' => $paymentStatus,
                'alamat'         => 'Jl. Contoh Alamat No. ' . $i . ', Bandung',
                'nomor_telepon'  => '0812' . str_pad($i, 8, '0', STR_PAD_LEFT),
                'created_at'     => Carbon::now()->subDays(rand(0, 180)),
                'updated_at'     => Carbon::now()->subDays(rand(0, 30)),
            ];
        }

        foreach (array_chunk($orders, 100) as $chunk) {
            DB::table('orders')->insert($chunk);
        }

        $this->command->info('✅ 500 orders seeded!');
        $this->command->info('🎉 Total: 1000 products + 500 orders berhasil dibuat!');
    }
}