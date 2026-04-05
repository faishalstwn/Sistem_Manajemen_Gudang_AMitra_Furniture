<?php

namespace App\Http\Controllers;

use App\Exports\BarangKeluarExport;
use App\Exports\BarangMasukExport;
use App\Exports\StokProdukExport;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Product;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class AdminExportController extends Controller
{
    // ──────────────────────────────────────────────
    //  EXCEL
    // ──────────────────────────────────────────────

    public function excelStok()
    {
        $filename = 'laporan-stok-produk-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new StokProdukExport, $filename);
    }

    public function excelBarangMasuk(Request $request)
    {
        $dari   = $request->get('dari');
        $sampai = $request->get('sampai');
        $filename = 'laporan-barang-masuk-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new BarangMasukExport($dari, $sampai), $filename);
    }

    public function excelBarangKeluar(Request $request)
    {
        $dari   = $request->get('dari');
        $sampai = $request->get('sampai');
        $filename = 'laporan-barang-keluar-' . now()->format('Ymd-His') . '.xlsx';
        return Excel::download(new BarangKeluarExport($dari, $sampai), $filename);
    }

    // ──────────────────────────────────────────────
    //  PDF
    // ──────────────────────────────────────────────

    public function pdfStok()
    {
        $products = Product::orderBy('name')->get();
        $pdf = Pdf::loadView('admin.exports.pdf-stok', compact('products'))
                  ->setPaper('a4', 'portrait');
        $filename = 'laporan-stok-produk-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }

    public function pdfBarangMasuk(Request $request)
    {
        $dari   = $request->get('dari');
        $sampai = $request->get('sampai');

        $query = BarangMasuk::with('produk')->latest();
        if ($dari)   $query->whereDate('tanggal_masuk', '>=', $dari);
        if ($sampai) $query->whereDate('tanggal_masuk', '<=', $sampai);
        $items = $query->get();

        $pdf = Pdf::loadView('admin.exports.pdf-barang-masuk', compact('items', 'dari', 'sampai'))
                  ->setPaper('a4', 'landscape');
        $filename = 'laporan-barang-masuk-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }

    public function pdfBarangKeluar(Request $request)
    {
        $dari   = $request->get('dari');
        $sampai = $request->get('sampai');

        $query = BarangKeluar::with('produk')->latest();
        if ($dari)   $query->whereDate('tanggal_keluar', '>=', $dari);
        if ($sampai) $query->whereDate('tanggal_keluar', '<=', $sampai);
        $items = $query->get();

        $pdf = Pdf::loadView('admin.exports.pdf-barang-keluar', compact('items', 'dari', 'sampai'))
                  ->setPaper('a4', 'landscape');
        $filename = 'laporan-barang-keluar-' . now()->format('Ymd-His') . '.pdf';
        return $pdf->download($filename);
    }
}
