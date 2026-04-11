<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\WarehouseLocation;
use Illuminate\Database\Seeder;

class WarehouseLocationSeeder extends Seeder
{
    public function run(): void
    {
        $lokasi = [
            // Zona A - Rak furniture besar
            ['kode' => 'A1', 'zona' => 'Zona A - Furniture Besar', 'baris' => 1, 'kolom' => 1, 'tipe' => 'rak',    'kapasitas' => 50,  'keterangan' => 'Rak meja makan & meja kerja'],
            ['kode' => 'A2', 'zona' => 'Zona A - Furniture Besar', 'baris' => 1, 'kolom' => 2, 'tipe' => 'rak',    'kapasitas' => 40,  'keterangan' => 'Rak lemari & bufet'],
            ['kode' => 'A3', 'zona' => 'Zona A - Furniture Besar', 'baris' => 1, 'kolom' => 3, 'tipe' => 'palet',  'kapasitas' => 30,  'keterangan' => 'Palet sofa & kursi besar'],

            // Zona B - Kursi & bangku
            ['kode' => 'B1', 'zona' => 'Zona B - Kursi',           'baris' => 2, 'kolom' => 1, 'tipe' => 'rak',    'kapasitas' => 80,  'keterangan' => 'Rak kursi makan'],
            ['kode' => 'B2', 'zona' => 'Zona B - Kursi',           'baris' => 2, 'kolom' => 2, 'tipe' => 'rak',    'kapasitas' => 60,  'keterangan' => 'Rak kursi tamu'],
            ['kode' => 'B3', 'zona' => 'Zona B - Kursi',           'baris' => 2, 'kolom' => 3, 'tipe' => 'lantai', 'kapasitas' => 40,  'keterangan' => 'Area kursi tumpuk'],

            // Zona C - Aksesoris & dekorasi
            ['kode' => 'C1', 'zona' => 'Zona C - Aksesoris',       'baris' => 3, 'kolom' => 1, 'tipe' => 'rak',    'kapasitas' => 120, 'keterangan' => 'Rak aksesoris kecil'],
            ['kode' => 'C2', 'zona' => 'Zona C - Aksesoris',       'baris' => 3, 'kolom' => 2, 'tipe' => 'rak',    'kapasitas' => 100, 'keterangan' => 'Rak lampu & dekorasi'],
            ['kode' => 'C3', 'zona' => 'Zona C - Aksesoris',       'baris' => 3, 'kolom' => 3, 'tipe' => 'rak',    'kapasitas' => 80,  'keterangan' => 'Rak bantal & tekstil'],

            // Zona D - Stok baru / incoming
            ['kode' => 'D1', 'zona' => 'Zona D - Staging',         'baris' => 4, 'kolom' => 1, 'tipe' => 'lantai', 'kapasitas' => 200, 'keterangan' => 'Area barang baru masuk'],
            ['kode' => 'D2', 'zona' => 'Zona D - Staging',         'baris' => 4, 'kolom' => 2, 'tipe' => 'palet',  'kapasitas' => 150, 'keterangan' => 'Palet siap kirim'],
        ];

        foreach ($lokasi as $data) {
            WarehouseLocation::firstOrCreate(['kode' => $data['kode']], $data);
        }

        // Assign beberapa produk ke lokasi secara acak
        $products = Product::all();
        if ($products->isEmpty()) return;

        $locations = WarehouseLocation::all();

        foreach ($locations as $loc) {
            // Pilih 1-3 produk acak untuk setiap lokasi
            $selected = $products->random(min(rand(1, 3), $products->count()));
            foreach ($selected as $product) {
                $jumlah = rand(5, (int) ($loc->kapasitas * 0.6));
                $loc->products()->syncWithoutDetaching([
                    $product->id => ['jumlah' => $jumlah],
                ]);
            }
        }
    }
}
