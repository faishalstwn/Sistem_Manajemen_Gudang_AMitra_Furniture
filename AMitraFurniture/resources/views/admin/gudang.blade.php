@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-md-12 d-flex justify-content-between align-items-start">
            <div>
                <h3 class="fw-bold"><i class="fas fa-warehouse me-2"></i>Monitor Gudang</h3>
                <p class="text-muted mb-0">Pantau kondisi stok & pesanan yang perlu dikirim</p>
            </div>
        </div>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-boxes fa-2x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $allProducts->count() }}</h4>
                    <small class="text-muted">Total Produk</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $lowStockProducts->count() }}</h4>
                    <small class="text-muted">Stok Rendah (&lt;10)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $outOfStockProducts->count() }}</h4>
                    <small class="text-muted">Stok Habis</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-truck fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $pendingShipmentOrders->count() }}</h4>
                    <small class="text-muted">Pesanan Perlu Dikirim</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Stok Rendah --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>Stok Perlu Restok
                    </h6>
                    <span class="badge bg-warning text-dark">{{ $lowStockProducts->count() }} produk</span>
                </div>
                <div class="card-body p-0">
                    @if($lowStockProducts->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-0">Semua stok aman</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Stok</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($lowStockProducts as $product)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                @if($product->image)
                                                    <img src="{{ asset($product->image) }}" width="36" height="36"
                                                         class="rounded" style="object-fit:cover">
                                                @endif
                                                <div>
                                                    <div class="fw-semibold" style="font-size:13px">{{ $product->name }}</div>
                                                    <small class="text-muted">{{ $product->category }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center fw-bold
                                            {{ $product->stock == 0 ? 'text-danger' : 'text-warning' }}">
                                            {{ $product->stock }}
                                        </td>
                                        <td class="text-center">
                                            @if($product->stock == 0)
                                                <span class="badge bg-danger">Habis</span>
                                            @else
                                                <span class="badge bg-warning text-dark">Rendah</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Pesanan Perlu Dikirim --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-primary">
                        <i class="fas fa-truck me-2"></i>Pesanan Siap Dikirim
                    </h6>
                    <span class="badge bg-primary">{{ $pendingShipmentOrders->count() }} pesanan</span>
                </div>
                <div class="card-body p-0">
                    @if($pendingShipmentOrders->isEmpty())
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-check-circle fa-2x mb-2 text-success"></i>
                            <p class="mb-0">Tidak ada pesanan menunggu pengiriman</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Kode Pesanan</th>
                                        <th>Pelanggan</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($pendingShipmentOrders as $order)
                                    <tr>
                                        <td>
                                            <span class="fw-semibold text-primary">#{{ $order->order_code }}</span>
                                            <br><small class="text-muted">{{ $order->created_at->diffForHumans() }}</small>
                                        </td>
                                        <td>
                                            {{ $order->user?->name ?? 'Guest' }}
                                            <br><small class="text-muted">{{ $order->nomor_telepon }}</small>
                                        </td>
                                        <td class="text-end fw-semibold">
                                            Rp {{ number_format($order->total_price, 0, ',', '.') }}
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('admin.pesanan.edit', $order) }}"
                                               class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
                <div class="card-footer bg-white text-end">
                    <a href="{{ route('admin.pengiriman') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-shipping-fast me-1"></i>Lihat Semua Pengiriman
                    </a>
                </div>
            </div>
        </div>

    </div>

    {{-- Semua Produk - Tabel Stok Lengkap --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-list me-2"></i>Stok Semua Produk
                    </h6>
                    <div class="d-flex gap-2">
                        <input type="text" id="searchProduct" class="form-control form-control-sm"
                               placeholder="Cari produk..." style="width:200px">
                        <a href="{{ route('admin.produk.create') }}" class="btn btn-sm btn-success">
                            <i class="fas fa-plus me-1"></i>Tambah Produk
                        </a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="productTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Harga</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allProducts as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($product->image)
                                                <img src="{{ asset($product->image) }}" width="40" height="40"
                                                     class="rounded" style="object-fit:cover">
                                            @else
                                                <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                                     style="width:40px;height:40px">
                                                    <i class="fas fa-image text-muted"></i>
                                                </div>
                                            @endif
                                            <span class="fw-semibold">{{ $product->name }}</span>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ $product->category }}</span></td>
                                    <td class="text-end">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td class="text-center fw-bold
                                        {{ $product->stock == 0 ? 'text-danger' : ($product->stock < 10 ? 'text-warning' : 'text-success') }}">
                                        {{ $product->stock }}
                                    </td>
                                    <td class="text-center">
                                        @if($product->stock == 0)
                                            <span class="badge bg-danger">Habis</span>
                                        @elseif($product->stock < 10)
                                            <span class="badge bg-warning text-dark">Rendah</span>
                                        @else
                                            <span class="badge bg-success">Aman</span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <a href="{{ route('admin.produk.edit', $product) }}"
                                           class="btn btn-sm btn-outline-primary" title="Edit">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- ── STATISTIK GRAFIK GUDANG ──────────────────────────────────── --}}
    <div class="row mt-4">
        <div class="col-12">
            <h5 class="fw-bold mb-3"><i class="fas fa-chart-bar me-2 text-primary"></i>Statistik Gudang (12 Bulan Terakhir)</h5>
        </div>
    </div>

    {{-- Grafik Barang Masuk & Keluar per Bulan --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-exchange-alt me-2 text-primary"></i>
                        Barang Masuk vs Keluar per Bulan
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMasukKeluar" height="100"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0">
                        <i class="fas fa-boxes me-2 text-success"></i>
                        Top 10 Produk Stok Terbanyak
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartTopStok" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Barang Masuk saja & Keluar saja --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-success">
                        <i class="fas fa-arrow-circle-down me-2"></i>Barang Masuk per Bulan (Unit)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartMasuk" height="120"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-danger">
                        <i class="fas fa-arrow-circle-up me-2"></i>Barang Keluar per Bulan (Unit)
                    </h6>
                </div>
                <div class="card-body">
                    <canvas id="chartKeluar" height="120"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// ── Data dari server ────────────────────────────────────────────────
const labels       = @json($chartLabels);
const dataMasuk    = @json($chartMasuk);
const dataKeluar   = @json($chartKeluar);
const topStokNames = @json($chartTopStokNames);
const topStokData  = @json($chartTopStokData);

// Warna
const colorMasuk  = 'rgba(25, 135, 84,  0.7)';
const colorKeluar = 'rgba(220, 53, 69,  0.7)';
const colorMasukBorder  = 'rgb(25, 135, 84)';
const colorKeluarBorder = 'rgb(220, 53, 69)';

const defaultFont = { family: 'inherit', size: 12 };

// ── 1. Grafik Gabungan Masuk vs Keluar ──────────────────────────────
new Chart(document.getElementById('chartMasukKeluar'), {
    type: 'bar',
    data: {
        labels,
        datasets: [
            {
                label: 'Barang Masuk (unit)',
                data: dataMasuk,
                backgroundColor: colorMasuk,
                borderColor: colorMasukBorder,
                borderWidth: 1,
                borderRadius: 4,
            },
            {
                label: 'Barang Keluar (unit)',
                data: dataKeluar,
                backgroundColor: colorKeluar,
                borderColor: colorKeluarBorder,
                borderWidth: 1,
                borderRadius: 4,
            },
        ],
    },
    options: {
        responsive: true,
        plugins: {
            legend: { labels: { font: defaultFont } },
            tooltip: { mode: 'index', intersect: false },
        },
        scales: {
            x: { ticks: { font: defaultFont } },
            y: { beginAtZero: true, ticks: { precision: 0, font: defaultFont } },
        },
    },
});

// ── 2. Grafik Top Stok (horizontal bar) ─────────────────────────────
new Chart(document.getElementById('chartTopStok'), {
    type: 'bar',
    data: {
        labels: topStokNames,
        datasets: [{
            label: 'Stok (unit)',
            data: topStokData,
            backgroundColor: [
                '#0d6efd','#198754','#0dcaf0','#ffc107','#6f42c1',
                '#fd7e14','#20c997','#e83e8c','#6c757d','#adb5bd',
            ],
            borderRadius: 4,
        }],
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: {
            legend: { display: false },
        },
        scales: {
            x: { beginAtZero: true, ticks: { precision: 0, font: defaultFont } },
            y: { ticks: { font: { ...defaultFont, size: 11 } } },
        },
    },
});

// ── 3. Grafik Barang Masuk (line) ────────────────────────────────────
new Chart(document.getElementById('chartMasuk'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Unit Masuk',
            data: dataMasuk,
            fill: true,
            backgroundColor: 'rgba(25,135,84,0.15)',
            borderColor: colorMasukBorder,
            pointBackgroundColor: colorMasukBorder,
            tension: 0.3,
        }],
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { font: defaultFont } },
            y: { beginAtZero: true, ticks: { precision: 0, font: defaultFont } },
        },
    },
});

// ── 4. Grafik Barang Keluar (line) ───────────────────────────────────
new Chart(document.getElementById('chartKeluar'), {
    type: 'line',
    data: {
        labels,
        datasets: [{
            label: 'Unit Keluar',
            data: dataKeluar,
            fill: true,
            backgroundColor: 'rgba(220,53,69,0.15)',
            borderColor: colorKeluarBorder,
            pointBackgroundColor: colorKeluarBorder,
            tension: 0.3,
        }],
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: {
            x: { ticks: { font: defaultFont } },
            y: { beginAtZero: true, ticks: { precision: 0, font: defaultFont } },
        },
    },
});

// Filter tabel produk secara real-time
document.getElementById('searchProduct').addEventListener('input', function () {
    const keyword = this.value.toLowerCase();
    const rows    = document.querySelectorAll('#productTable tbody tr');
    rows.forEach(row => {
        const name = row.querySelector('td:first-child')?.textContent.toLowerCase() ?? '';
        row.style.display = name.includes(keyword) ? '' : 'none';
    });
});
</script>
@endsection
