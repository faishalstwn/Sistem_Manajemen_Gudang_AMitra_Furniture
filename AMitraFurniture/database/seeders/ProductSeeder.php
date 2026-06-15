<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            [
                'name' => 'Sofa Minimalis Modern',
                'slug' => 'sofa-minimalis-modern',
                'description' => 'Sofa minimalis dengan desain modern yang nyaman untuk ruang tamu Anda.',
                'price' => 3500000,
                'category' => 'Sofa',
                'stock' => 15,
                'image' => 'assets/images/dan2.jpg',
            ],
            [
                'name' => 'Meja Makan Kayu Jati',
                'slug' => 'meja-makan-kayu-jati',
                'description' => 'Meja makan dari kayu jati berkualitas tinggi untuk 6 orang.',
                'price' => 5000000,
                'category' => 'Meja',
                'stock' => 10,
                'image' => 'assets/images/meja makan kayu jati.jpg',
            ],
            [
                'name' => 'Kursi Kerja Ergonomis',
                'slug' => 'kursi-kerja-ergonomis',
                'description' => 'Kursi kerja dengan desain ergonomis untuk kenyamanan maksimal.',
                'price' => 1500000,
                'category' => 'Kursi',
                'stock' => 25,
                'image' => 'assets/images/kursi kerja.jpg',
            ],
            [
                'name' => 'Lemari Pakaian 3 Pintu',
                'slug' => 'lemari-pakaian-3-pintu',
                'description' => 'Lemari pakaian minimalis dengan 3 pintu dan cermin.',
                'price' => 4000000,
                'category' => 'Lemari',
                'stock' => 8,
                'image' => 'assets/images/Lemari.png',
            ],
            [
                'name' => 'Rak Buku Minimalis',
                'slug' => 'rak-buku-minimalis',
                'description' => 'Rak buku dengan 3 tingkat untuk menyimpan koleksi buku Anda.',
                'price' => 1200000,
                'category' => 'Rak',
                'stock' => 20,
                'image' => 'assets/images/dan5.jpg',
            ],
            [
                'name' => 'Kursi Elbow Chair',
                'slug' => 'kursi-elbow-chair',
                'description' => 'Kursi elbow dengan desain elegan dan nyaman untuk ruang tamu.',
                'price' => 1800000,
                'category' => 'Kursi',
                'stock' => 12,
                'image' => 'assets/images/Elbow Chair.jpeg',
            ],
            [
                'name' => 'Meja Minimalis',
                'slug' => 'meja-minimalis', // FIXED: menghapus spasi
                'description' => 'Meja minimalis serbaguna untuk berbagai kebutuhan rumah atau kantor.',
                'price' => 2500000,
                'category' => 'Meja',
                'stock' => 18,
                'image' => 'assets/images/meja minimalis.jpeg',
            ],
            [
                'name' => 'Sofa Modern',
                'slug' => 'sofa-modern',
                'description' => 'Sofa modern dengan desain elegant dan material premium.',
                'price' => 4500000,
                'category' => 'Sofa',
                'stock' => 10,
                'image' => 'assets/images/dan7.jpg',
            ],
            [
                'name' => 'Sofa Elegant',
                'slug' => 'sofa-elegant', // FIXED: menambah nama yang lebih spesifik
                'description' => 'Sofa nyaman dengan bahan berkualitas tinggi.',
                'price' => 4200000,
                'category' => 'Sofa',
                'stock' => 12,
                'image' => 'assets/images/dan8.jpg',
            ],
            [
                'name' => 'Ranjang Tingkat Kayu Modern',
                'slug' => 'ranjang-tingkat-kayu-modern', // FIXED: huruf kecil semua
                'description' => 'Ranjang tingkat dari kayu modern yang kuat dan tahan lama.',
                'price' => 4800000,
                'category' => 'Ranjang', // FIXED: kategori lebih sesuai
                'stock' => 6,
                'image' => 'assets/images/dan3.jpg',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }
    }
}