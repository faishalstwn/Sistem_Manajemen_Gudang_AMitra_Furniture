<?php

namespace App\Exports;

use App\Models\Product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class StokProdukExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    public function collection()
    {
        return Product::orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Produk',
            'Kategori',
            'Stok',
            'Harga (Rp)',
            'Status Stok',
            'Terakhir Diperbarui',
        ];
    }

    public function map($product): array
    {
        static $no = 0;
        $no++;

        if ($product->stock == 0) {
            $status = 'Habis';
        } elseif ($product->stock < 10) {
            $status = 'Rendah';
        } else {
            $status = 'Aman';
        }

        return [
            $no,
            $product->name,
            $product->category ?? '-',
            $product->stock,
            number_format($product->price, 0, ',', '.'),
            $status,
            $product->updated_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1d6f42']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Stok Produk';
    }
}
