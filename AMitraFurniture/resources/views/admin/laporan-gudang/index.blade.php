@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-start mb-4">
        <div>
            <h3 class="fw-bold"><i class="fas fa-chart-pie me-2"></i>Laporan & Analisis Gudang</h3>
            <p class="text-muted mb-0">Ringkasan pergerakan stok, trend, dan analisis inventori</p>
        </div>
        <form method="GET" class="d-flex gap-2 align-items-center">
            <label class="small text-muted text-nowrap">Periode:</label>
            <select name="periode" class="form-select form-select-sm" onchange="this.form.submit()" style="width:auto">
                <option value="6" {{ $periode == 6 ? 'selected' : '' }}>6 Bulan</option>
                <option value="12" {{ $periode == 12 ? 'selected' : '' }}>12 Bulan</option>
                <option value="24" {{ $periode == 24 ? 'selected' : '' }}>24 Bulan</option>
            </select>
        </form>
    </div>

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-boxes fa-lg text-primary mb-1"></i>
                    <h5 class="fw-bold mb-0">{{ number_format($totalProduk) }}</h5>
                    <small class="text-muted">Jenis Produk</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-cubes fa-lg text-success mb-1"></i>
                    <h5 class="fw-bold mb-0">{{ number_format($totalStok) }}</h5>
                    <small class="text-muted">Total Unit</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-money-bill-wave fa-lg text-info mb-1"></i>
                    <h5 class="fw-bold mb-0">Rp {{ number_format($nilaiInventori, 0, ',', '.') }}</h5>
                    <small class="text-muted">Nilai Inventori</small>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-arrow-circle-down fa-lg text-success mb-1"></i>
                    <h5 class="fw-bold mb-0">{{ number_format($totalMasuk30) }}</h5>
                    <small class="text-muted">Masuk (30 Hari)</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center h-100">
                <div class="card-body py-3">
                    <i class="fas fa-arrow-circle-up fa-lg text-danger mb-1"></i>
                    <h5 class="fw-bold mb-0">{{ number_format($totalKeluar30) }}</h5>
                    <small class="text-muted">Keluar (30 Hari)</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Trend Masuk vs Keluar --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>Trend Barang Masuk vs Keluar</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartTrend" height="110"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-doughnut me-2 text-info"></i>Distribusi Pergerakan Stok</h6>
                </div>
                <div class="card-body d-flex align-items-center justify-content-center">
                    <canvas id="chartPie" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Grafik Net Flow + Kategori --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="fas fa-balance-scale me-2 text-warning"></i>Net Flow per Bulan (Masuk - Keluar)</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartNetflow" height="140"></canvas>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="fas fa-th-large me-2 text-success"></i>Stok per Kategori</h6>
                </div>
                <div class="card-body">
                    <canvas id="chartKategori" height="140"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Rata-rata + Opname Terakhir --}}
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="fas fa-calculator me-2"></i>Rata-rata Bulanan</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Avg. Barang Masuk</span>
                        <span class="fw-bold text-success">{{ $avgMasukBulanan }} unit/bulan</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span class="text-muted">Avg. Barang Keluar</span>
                        <span class="fw-bold text-danger">{{ $avgKeluarBulanan }} unit/bulan</span>
                    </div>
                    <div class="d-flex justify-content-between py-2">
                        <span class="text-muted">Net Avg.</span>
                        @php $netAvg = $avgMasukBulanan - $avgKeluarBulanan; @endphp
                        <span class="fw-bold {{ $netAvg >= 0 ? 'text-success' : 'text-danger' }}">
                            {{ $netAvg >= 0 ? '+' : '' }}{{ $netAvg }} unit/bulan
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="fas fa-clipboard-check me-2"></i>Stock Opname Terakhir</h6>
                </div>
                <div class="card-body">
                    @if($lastOpname)
                        <p class="mb-1"><strong>{{ $lastOpname->kode }}</strong></p>
                        <p class="text-muted mb-1">{{ $lastOpname->tanggal->format('d M Y') }}</p>
                        <p class="mb-0 small">Oleh: {{ $lastOpname->user?->name ?? '-' }}</p>
                    @else
                        <p class="text-muted mb-0">Belum ada stock opname diselesaikan</p>
                    @endif
                    <hr>
                    <a href="{{ route('admin.stock-opname.index') }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-clipboard-check me-1"></i>Lihat Opname
                    </a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0"><i class="fas fa-file-export me-2"></i>Export Laporan</h6>
                </div>
                <div class="card-body d-flex flex-column gap-2">
                    <a href="{{ route('admin.export.excel.stok') }}" class="btn btn-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i>Export Stok (Excel)
                    </a>
                    <a href="{{ route('admin.export.pdf.stok') }}" class="btn btn-danger btn-sm">
                        <i class="fas fa-file-pdf me-1"></i>Export Stok (PDF)
                    </a>
                    <a href="{{ route('admin.export.excel.barang-masuk') }}" class="btn btn-outline-success btn-sm">
                        <i class="fas fa-file-excel me-1"></i>Export Barang Masuk
                    </a>
                    <a href="{{ route('admin.export.excel.barang-keluar') }}" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-file-excel me-1"></i>Export Barang Keluar
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Top Movers & Slow Movers --}}
    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between">
                    <h6 class="fw-bold mb-0 text-danger">
                        <i class="fas fa-fire me-2"></i>Top 10 Produk Paling Laris Keluar
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($topKeluar->isEmpty())
                        <div class="text-center py-4 text-muted">Belum ada data barang keluar</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Produk</th>
                                        <th class="text-end">Total Keluar</th>
                                        <th class="text-center">Stok Sisa</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topKeluar as $i => $row)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $row->produk?->name }}</span>
                                            <br><small class="text-muted">{{ $row->produk?->category }}</small>
                                        </td>
                                        <td class="text-end fw-bold text-danger">{{ number_format($row->total_keluar) }}</td>
                                        <td class="text-center">
                                            <span class="badge {{ ($row->produk?->stock ?? 0) < 10 ? 'bg-warning text-dark' : 'bg-success' }}">
                                                {{ $row->produk?->stock ?? 0 }}
                                            </span>
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
        <div class="col-md-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-0 text-secondary">
                        <i class="fas fa-snowflake me-2"></i>Slow Movers (Tidak Keluar 3 Bulan Terakhir)
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($slowMovers->isEmpty())
                        <div class="text-center py-4 text-muted">Semua produk aktif bergerak</div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Stok</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($slowMovers as $i => $product)
                                    <tr>
                                        <td>{{ $i + 1 }}</td>
                                        <td class="fw-semibold">{{ $product->name }}</td>
                                        <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ $product->category }}</span></td>
                                        <td class="text-center fw-bold">{{ $product->stock }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const labels     = @json($months);
const dataMasuk  = @json($dataMasuk);
const dataKeluar = @json($dataKeluar);
const dataSelisih = @json($dataSelisih);
const kategoriLabels = @json($stokPerKategori->pluck('category'));
const kategoriData   = @json($stokPerKategori->pluck('total_stok'));
const mvMasuk   = {{ $movementSummary['masuk'] }};
const mvKeluar  = {{ $movementSummary['keluar'] }};
const mvKoreksi = {{ $movementSummary['koreksi'] }};

const fontCfg = { family: 'inherit', size: 12 };
const green   = 'rgba(25,135,84,0.7)';
const red     = 'rgba(220,53,69,0.7)';
const blue    = 'rgba(13,110,253,0.7)';

// 1. Trend Masuk vs Keluar
new Chart(document.getElementById('chartTrend'), {
    type: 'line',
    data: {
        labels,
        datasets: [
            { label: 'Masuk', data: dataMasuk, borderColor: 'rgb(25,135,84)', backgroundColor: 'rgba(25,135,84,0.1)', fill: true, tension: 0.3 },
            { label: 'Keluar', data: dataKeluar, borderColor: 'rgb(220,53,69)', backgroundColor: 'rgba(220,53,69,0.1)', fill: true, tension: 0.3 },
        ],
    },
    options: {
        responsive: true,
        plugins: { legend: { labels: { font: fontCfg } }, tooltip: { mode: 'index', intersect: false } },
        scales: { x: { ticks: { font: fontCfg } }, y: { beginAtZero: true, ticks: { precision: 0, font: fontCfg } } },
    },
});

// 2. Pie Distribusi Pergerakan
new Chart(document.getElementById('chartPie'), {
    type: 'doughnut',
    data: {
        labels: ['Masuk', 'Keluar', 'Koreksi'],
        datasets: [{
            data: [mvMasuk, mvKeluar, mvKoreksi],
            backgroundColor: [green, red, blue],
        }],
    },
    options: {
        responsive: true,
        plugins: { legend: { position: 'bottom', labels: { font: fontCfg } } },
    },
});

// 3. Net Flow Bar
new Chart(document.getElementById('chartNetflow'), {
    type: 'bar',
    data: {
        labels,
        datasets: [{
            label: 'Net Flow',
            data: dataSelisih,
            backgroundColor: dataSelisih.map(v => v >= 0 ? green : red),
            borderRadius: 4,
        }],
    },
    options: {
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { ticks: { font: fontCfg } }, y: { ticks: { precision: 0, font: fontCfg } } },
    },
});

// 4. Stok per Kategori
const catColors = ['#0d6efd','#198754','#ffc107','#0dcaf0','#6f42c1','#fd7e14','#20c997','#e83e8c','#6c757d','#adb5bd'];
new Chart(document.getElementById('chartKategori'), {
    type: 'bar',
    data: {
        labels: kategoriLabels,
        datasets: [{
            label: 'Stok',
            data: kategoriData,
            backgroundColor: catColors.slice(0, kategoriLabels.length),
            borderRadius: 4,
        }],
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        plugins: { legend: { display: false } },
        scales: { x: { beginAtZero: true, ticks: { precision: 0, font: fontCfg } }, y: { ticks: { font: fontCfg } } },
    },
});
</script>
@endsection
