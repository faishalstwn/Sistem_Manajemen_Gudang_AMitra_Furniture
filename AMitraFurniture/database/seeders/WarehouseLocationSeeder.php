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
            // Zona A - Sofa & Ranjang
            ['kode' => 'A1', 'zona' => 'Zona A - Sofa & Ranjang',  'baris' => 1, 'kolom' => 1, 'tipe' => 'palet',  'kapasitas' => 30,  'keterangan' => 'Palet sofa minimalis & sofa modern'],
            ['kode' => 'A2', 'zona' => 'Zona A - Sofa & Ranjang',  'baris' => 1, 'kolom' => 2, 'tipe' => 'palet',  'kapasitas' => 25,  'keterangan' => 'Palet sofa elegant & sofa besar'],
            ['kode' => 'A3', 'zona' => 'Zona A - Sofa & Ranjang',  'baris' => 1, 'kolom' => 3, 'tipe' => 'lantai', 'kapasitas' => 20,  'keterangan' => 'Area ranjang tingkat & kasur'],

            // Zona B - Meja
            ['kode' => 'B1', 'zona' => 'Zona B - Meja',            'baris' => 2, 'kolom' => 1, 'tipe' => 'rak',    'kapasitas' => 50,  'keterangan' => 'Rak meja makan kayu jati'],
            ['kode' => 'B2', 'zona' => 'Zona B - Meja',            'baris' => 2, 'kolom' => 2, 'tipe' => 'rak',    'kapasitas' => 60,  'keterangan' => 'Rak meja minimalis serbaguna'],
            ['kode' => 'B3', 'zona' => 'Zona B - Meja',            'baris' => 2, 'kolom' => 3, 'tipe' => 'lantai', 'kapasitas' => 40,  'keterangan' => 'Area meja display & meja kerja'],

            // Zona C - Kursi
            ['kode' => 'C1', 'zona' => 'Zona C - Kursi',           'baris' => 3, 'kolom' => 1, 'tipe' => 'rak',    'kapasitas' => 80,  'keterangan' => 'Rak kursi kerja ergonomis'],
            ['kode' => 'C2', 'zona' => 'Zona C - Kursi',           'baris' => 3, 'kolom' => 2, 'tipe' => 'rak',    'kapasitas' => 80,  'keterangan' => 'Rak kursi elbow chair & kursi tamu'],
            ['kode' => 'C3', 'zona' => 'Zona C - Kursi',           'baris' => 3, 'kolom' => 3, 'tipe' => 'lantai', 'kapasitas' => 60,  'keterangan' => 'Area kursi makan tumpuk'],

            // Zona D - Lemari & Rak
            ['kode' => 'D1', 'zona' => 'Zona D - Lemari & Rak',    'baris' => 4, 'kolom' => 1, 'tipe' => 'rak',    'kapasitas' => 40,  'keterangan' => 'Rak lemari pakaian 3 pintu'],
            ['kode' => 'D2', 'zona' => 'Zona D - Lemari & Rak',    'baris' => 4, 'kolom' => 2, 'tipe' => 'rak',    'kapasitas' => 100, 'keterangan' => 'Rak buku minimalis & rak serbaguna'],
        ];

        foreach ($lokasi as $data) {
            WarehouseLocation::updateOrCreate(['kode' => $data['kode']], $data);
        }

        // Assign beberapa produk ke lokasi secara acak
        $products = Product::all();
        if ($products->isEmpty()) return;

        $locations = WarehouseLocation::all();

        foreach ($locations as $loc) {
            // Pilih 1-3 produk acak untuk setiap lokasi
            $selected = $products->random(min(rand(1, 3), $products->count()));
            $sisaKapasitas = $loc->kapasitas;
            
            foreach ($selected as $product) {
                if ($sisaKapasitas <= 0) break;
                
                // Set maksimal 60% dari kapasitas awal, tapi jangan melebihi sisa kapasitas saat ini
                $maxRandom = min((int) ($loc->kapasitas * 0.6), $sisaKapasitas);
                if ($maxRandom < 1) break;
                
                $jumlah = rand(1, $maxRandom);
                $loc->products()->syncWithoutDetaching([
                    $product->id => ['jumlah' => $jumlah],
                ]);
                $sisaKapasitas -= $jumlah;
            }
        }
    }
}
