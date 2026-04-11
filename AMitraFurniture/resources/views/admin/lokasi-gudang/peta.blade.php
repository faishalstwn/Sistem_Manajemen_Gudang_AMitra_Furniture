@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-map-marked-alt me-2 text-primary"></i>Peta Lokasi Gudang</h3>
                <p class="text-muted mb-0">Denah visual posisi rak & penyimpanan barang di gudang</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.lokasi-gudang.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list me-1"></i>Daftar Lokasi
                </a>
                <a href="{{ route('admin.lokasi-gudang.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Tambah Lokasi
                </a>
            </div>
        </div>
    </div>

    {{-- Statistik --}}
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-2 mb-2">
                        <i class="fas fa-th fa-lg text-primary"></i>
                    </div>
                    <h5 class="fw-bold mb-0">{{ $stats['total_lokasi'] }}</h5>
                    <small class="text-muted">Total Lokasi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-2 mb-2">
                        <i class="fas fa-box fa-lg text-success"></i>
                    </div>
                    <h5 class="fw-bold mb-0">{{ $stats['total_terisi'] }}</h5>
                    <small class="text-muted">Terisi</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-secondary bg-opacity-10 rounded-circle d-inline-flex p-2 mb-2">
                        <i class="fas fa-cube fa-lg text-secondary"></i>
                    </div>
                    <h5 class="fw-bold mb-0">{{ $stats['total_kosong'] }}</h5>
                    <small class="text-muted">Kosong</small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-2 mb-2">
                        <i class="fas fa-exclamation-circle fa-lg text-danger"></i>
                    </div>
                    <h5 class="fw-bold mb-0">{{ $stats['total_penuh'] }}</h5>
                    <small class="text-muted">Penuh</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Legend --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-2">
            <div class="d-flex flex-wrap gap-3 align-items-center">
                <small class="fw-semibold text-muted me-2">Keterangan:</small>
                <span class="d-flex align-items-center gap-1">
                    <span class="d-inline-block rounded" style="width:16px;height:16px;background:#e9ecef;border:1px solid #ccc;"></span>
                    <small>Kosong</small>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="d-inline-block rounded" style="width:16px;height:16px;background:#d1e7dd;"></span>
                    <small>Terisi &lt;50%</small>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="d-inline-block rounded" style="width:16px;height:16px;background:#fff3cd;"></span>
                    <small>Terisi 50-80%</small>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="d-inline-block rounded" style="width:16px;height:16px;background:#f8d7da;"></span>
                    <small>Terisi &gt;80%</small>
                </span>
                <span class="d-flex align-items-center gap-1">
                    <span class="d-inline-block rounded" style="width:16px;height:16px;background:#dc3545;"></span>
                    <small>Penuh 100%</small>
                </span>
            </div>
        </div>
    </div>

    @if($maxBaris === 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-map-marked-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Belum ada lokasi gudang</h5>
                <p class="text-muted">Tambahkan lokasi rak/penyimpanan untuk mulai membuat peta gudang.</p>
                <a href="{{ route('admin.lokasi-gudang.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus me-1"></i>Tambah Lokasi Pertama
                </a>
            </div>
        </div>
    @else
        {{-- Filter Zona --}}
        <div class="mb-3 d-flex gap-2 flex-wrap">
            <button class="btn btn-sm {{ !request('zona') ? 'btn-primary' : 'btn-outline-primary' }} zona-filter" data-zona="all">
                Semua Zona
            </button>
            @foreach($zonaList as $zona)
                <button class="btn btn-sm btn-outline-primary zona-filter" data-zona="{{ $zona }}">
                    {{ $zona }}
                </button>
            @endforeach
        </div>

        {{-- Grid Peta Gudang --}}
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="peta-grid" style="border-collapse:separate;border-spacing:6px;">
                        <thead>
                            <tr>
                                <th style="width:40px;"></th>
                                @for($k = 1; $k <= $maxKolom; $k++)
                                    <th class="text-center text-muted small">Kolom {{ $k }}</th>
                                @endfor
                            </tr>
                        </thead>
                        <tbody>
                            @for($b = 1; $b <= $maxBaris; $b++)
                                <tr>
                                    <td class="text-muted small align-middle fw-semibold pe-2">Baris {{ $b }}</td>
                                    @for($k = 1; $k <= $maxKolom; $k++)
                                        @php $cell = $grid[$b][$k] ?? null; @endphp
                                        @if($cell)
                                            @php
                                                $persen = $cell->persentaseTerisi();
                                                $terisi = $cell->totalTerisi();

                                                if ($terisi === 0) {
                                                    $bg = '#f0f0f0';
                                                    $border = '#ccc';
                                                    $textColor = '#888';
                                                } elseif ($persen >= 100) {
                                                    $bg = '#dc3545';
                                                    $border = '#dc3545';
                                                    $textColor = '#fff';
                                                } elseif ($persen > 80) {
                                                    $bg = '#f8d7da';
                                                    $border = '#f1aeb5';
                                                    $textColor = '#842029';
                                                } elseif ($persen > 50) {
                                                    $bg = '#fff3cd';
                                                    $border = '#ffe69c';
                                                    $textColor = '#664d03';
                                                } else {
                                                    $bg = '#d1e7dd';
                                                    $border = '#a3cfbb';
                                                    $textColor = '#0f5132';
                                                }

                                                $icon = match($cell->tipe) {
                                                    'palet'  => 'fa-pallet',
                                                    'lantai' => 'fa-layer-group',
                                                    default  => 'fa-th-large',
                                                };
                                            @endphp
                                            <td class="rak-cell" data-zona="{{ $cell->zona }}"
                                                style="background:{{ $bg }};border:2px solid {{ $border }};color:{{ $textColor }};
                                                       border-radius:8px;min-width:130px;cursor:pointer;transition:all .15s;"
                                                onclick="window.location='{{ route('admin.lokasi-gudang.show', $cell) }}'">
                                                <div class="p-2 text-center">
                                                    <div class="mb-1">
                                                        <i class="fas {{ $icon }} me-1"></i>
                                                        <strong>{{ $cell->kode }}</strong>
                                                    </div>
                                                    <div style="font-size:11px;">{{ $cell->zona }}</div>
                                                    <div class="mt-1" style="font-size:11px;">
                                                        {{ $terisi }}/{{ $cell->kapasitas }} unit
                                                    </div>
                                                    {{-- Progress bar mini --}}
                                                    <div class="progress mt-1" style="height:4px;">
                                                        <div class="progress-bar {{ $persen >= 100 ? 'bg-danger' : ($persen > 80 ? 'bg-warning' : 'bg-success') }}"
                                                             style="width:{{ min($persen, 100) }}%"></div>
                                                    </div>
                                                    @if($cell->products->count() > 0)
                                                        <div class="mt-1" style="font-size:10px;opacity:.8;">
                                                            {{ $cell->products->count() }} produk
                                                        </div>
                                                    @endif
                                                </div>
                                            </td>
                                        @else
                                            <td style="min-width:130px;background:#fafafa;border:1px dashed #ddd;border-radius:8px;">
                                                <div class="p-2 text-center text-muted" style="font-size:11px;">
                                                    <i class="fas fa-plus-circle"></i><br>
                                                    Kosong
                                                </div>
                                            </td>
                                        @endif
                                    @endfor
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Ringkasan per Zona --}}
        <div class="row g-3 mt-3">
            @foreach($zonaList as $zona)
                @php
                    $lokasiZona = $locations->where('zona', $zona);
                    $totalKap   = $lokasiZona->sum('kapasitas');
                    $totalIsi   = $lokasiZona->sum(fn($l) => $l->totalTerisi());
                    $persenZona = $totalKap > 0 ? round(($totalIsi / $totalKap) * 100, 1) : 0;
                @endphp
                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <h6 class="fw-bold mb-2"><i class="fas fa-map-pin me-1 text-primary"></i>{{ $zona }}</h6>
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $lokasiZona->count() }} lokasi</span>
                                <span>{{ number_format($totalIsi) }} / {{ number_format($totalKap) }} unit</span>
                            </div>
                            <div class="progress" style="height:8px;">
                                <div class="progress-bar {{ $persenZona >= 90 ? 'bg-danger' : ($persenZona >= 60 ? 'bg-warning' : 'bg-success') }}"
                                     style="width:{{ $persenZona }}%"></div>
                            </div>
                            <small class="text-muted">Kapasitas terpakai: {{ $persenZona }}%</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.rak-cell:hover {
    transform: scale(1.04);
    box-shadow: 0 4px 12px rgba(0,0,0,.15);
    z-index: 2;
    position: relative;
}
</style>

<script>
document.querySelectorAll('.zona-filter').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var zona = this.dataset.zona;

        document.querySelectorAll('.zona-filter').forEach(function(b) {
            b.classList.remove('btn-primary');
            b.classList.add('btn-outline-primary');
        });
        this.classList.remove('btn-outline-primary');
        this.classList.add('btn-primary');

        document.querySelectorAll('.rak-cell').forEach(function(cell) {
            if (zona === 'all' || cell.dataset.zona === zona) {
                cell.style.opacity = '1';
            } else {
                cell.style.opacity = '0.2';
            }
        });
    });
});
</script>
@endsection
