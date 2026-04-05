<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Stok Produk</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #1d6f42; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; color: #1d6f42; }
        .header p  { margin: 4px 0; font-size: 11px; color: #666; }
        .meta { margin-bottom: 12px; font-size: 11px; }
        .meta span { margin-right: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #1d6f42; color: #fff; padding: 7px 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) td { background-color: #f5f5f5; }
        .badge-habis  { color: #dc3545; font-weight: bold; }
        .badge-rendah { color: #fd7e14; font-weight: bold; }
        .badge-aman   { color: #198754; font-weight: bold; }
        .footer { margin-top: 20px; font-size: 10px; color: #999; text-align: right; }
        .summary { margin-bottom: 12px; display: flex; gap: 16px; }
        .summary-box { border: 1px solid #dee2e6; border-radius: 4px; padding: 8px 14px; }
        .summary-box .label { font-size: 10px; color: #666; }
        .summary-box .value { font-size: 16px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN STOK PRODUK</h2>
        <p>AMitra Furniture</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
    </div>

    <div class="meta">
        <span><strong>Total Produk:</strong> {{ $products->count() }}</span>
        <span><strong>Total Stok:</strong> {{ number_format($products->sum('stock')) }} unit</span>
        <span><strong>Stok Habis:</strong> {{ $products->where('stock', 0)->count() }} produk</span>
        <span><strong>Stok Rendah (&lt;10):</strong> {{ $products->where('stock', '>', 0)->where('stock', '<', 10)->count() }} produk</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th style="text-align:center">Stok</th>
                <th style="text-align:right">Harga</th>
                <th style="text-align:center">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($products as $i => $product)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $product->name }}</td>
                <td>{{ $product->category ?? '-' }}</td>
                <td style="text-align:center">{{ number_format($product->stock) }}</td>
                <td style="text-align:right">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                <td style="text-align:center">
                    @if ($product->stock == 0)
                        <span class="badge-habis">Habis</span>
                    @elseif ($product->stock < 10)
                        <span class="badge-rendah">Rendah</span>
                    @else
                        <span class="badge-aman">Aman</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="6" style="text-align:center;padding:20px;">Tidak ada data produk.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name ?? 'Admin' }} &mdash; {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
