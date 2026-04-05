<?php

namespace App\Exports;

use App\Models\BarangKeluar;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class BarangKeluarExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithTitle, ShouldAutoSize
{
    protected ?string $dari;
    protected ?string $sampai;

    public function __construct(?string $dari = null, ?string $sampai = null)
    {
        $this->dari   = $dari;
        $this->sampai = $sampai;
    }

    public function collection()
    {
        $query = BarangKeluar::with('produk')->latest();

        if ($this->dari) {
            $query->whereDate('tanggal_keluar', '>=', $this->dari);
        }
        if ($this->sampai) {
            $query->whereDate('tanggal_keluar', '<=', $this->sampai);
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal Keluar',
            'Nama Produk',
            'Kategori',
            'Jumlah',
            'Tujuan',
            'Catatan',
            'Dicatat Pada',
        ];
    }

    public function map($item): array
    {
        static $no = 0;
        $no++;

        return [
            $no,
            $item->tanggal_keluar->format('d/m/Y'),
            $item->produk->name ?? '-',
            $item->produk->category ?? '-',
            $item->jumlah,
            $item->tujuan ?? '-',
            $item->catatan ?? '-',
            $item->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'bb2d3b']],
            ],
        ];
    }

    public function title(): string
    {
        return 'Barang Keluar';
    }
}
