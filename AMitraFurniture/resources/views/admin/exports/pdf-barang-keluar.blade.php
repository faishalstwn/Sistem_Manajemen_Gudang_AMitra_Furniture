<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Barang Keluar</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #bb2d3b; padding-bottom: 10px; }
        .header h2 { margin: 0; font-size: 18px; color: #bb2d3b; }
        .header p  { margin: 4px 0; font-size: 11px; color: #666; }
        .meta { margin-bottom: 12px; font-size: 11px; }
        .meta span { margin-right: 20px; }
        .periode { background: #fff5f5; border: 1px solid #f5c6cb; border-radius: 4px; 
                   padding: 6px 12px; margin-bottom: 12px; font-size: 11px; }
        table { width: 100%; border-collapse: collapse; }
        th { background-color: #bb2d3b; color: #fff; padding: 7px 8px; text-align: left; }
        td { padding: 6px 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) td { background-color: #f5f5f5; }
        .footer { margin-top: 20px; font-size: 10px; color: #999; text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <h2>LAPORAN BARANG KELUAR</h2>
        <p>AMitra Furniture</p>
        <p>Tanggal Cetak: {{ \Carbon\Carbon::now()->isoFormat('D MMMM YYYY, HH:mm') }}</p>
    </div>

    @if ($dari || $sampai)
    <div class="periode">
        <strong>Periode:</strong>
        {{ $dari ? \Carbon\Carbon::parse($dari)->isoFormat('D MMMM YYYY') : 'Awal' }}
        &mdash;
        {{ $sampai ? \Carbon\Carbon::parse($sampai)->isoFormat('D MMMM YYYY') : 'Sekarang' }}
    </div>
    @endif

    <div class="meta">
        <span><strong>Total Entri:</strong> {{ $items->count() }}</span>
        <span><strong>Total Jumlah Keluar:</strong> {{ number_format($items->sum('jumlah')) }} unit</span>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width:30px">No</th>
                <th style="width:90px">Tanggal</th>
                <th>Nama Produk</th>
                <th>Kategori</th>
                <th style="text-align:center">Jumlah</th>
                <th>Tujuan</th>
                <th>Catatan</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->tanggal_keluar->format('d/m/Y') }}</td>
                <td>{{ $item->produk->name ?? '-' }}</td>
                <td>{{ $item->produk->category ?? '-' }}</td>
                <td style="text-align:center">{{ number_format($item->jumlah) }}</td>
                <td>{{ $item->tujuan ?? '-' }}</td>
                <td>{{ $item->catatan ?? '-' }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;padding:20px;">Tidak ada data barang keluar.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Dicetak oleh: {{ auth()->user()->name ?? 'Admin' }} &mdash; {{ \Carbon\Carbon::now()->format('d/m/Y H:i') }}
    </div>
</body>
</html>
