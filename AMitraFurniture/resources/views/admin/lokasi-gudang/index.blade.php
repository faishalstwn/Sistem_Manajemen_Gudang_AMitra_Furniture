@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-map-marker-alt me-2 text-primary"></i>Daftar Lokasi Gudang</h3>
                <p class="text-muted mb-0">Kelola semua lokasi penyimpanan di gudang</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.lokasi-gudang.peta') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-map me-1"></i>Lihat Peta
                </a>
                <a href="{{ route('admin.lokasi-gudang.create') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-plus me-1"></i>Tambah Lokasi
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body py-3">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Zona</label>
                    <select name="zona" class="form-select form-select-sm">
                        <option value="">Semua Zona</option>
                        @foreach($zonaList as $zona)
                            <option value="{{ $zona }}" {{ request('zona') == $zona ? 'selected' : '' }}>{{ $zona }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Tipe</label>
                    <select name="tipe" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        <option value="rak" {{ request('tipe') == 'rak' ? 'selected' : '' }}>Rak</option>
                        <option value="lantai" {{ request('tipe') == 'lantai' ? 'selected' : '' }}>Lantai</option>
                        <option value="palet" {{ request('tipe') == 'palet' ? 'selected' : '' }}>Palet</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Cari</label>
                    <input type="text" name="cari" class="form-control form-control-sm"
                           value="{{ request('cari') }}" placeholder="Kode atau keterangan...">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-search me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.lokasi-gudang.index') }}" class="btn btn-outline-secondary btn-sm">Reset</a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Kode</th>
                        <th>Zona</th>
                        <th>Posisi</th>
                        <th>Tipe</th>
                        <th>Kapasitas</th>
                        <th>Terisi</th>
                        <th>Produk</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($locations as $loc)
                        @php
                            $terisi = $loc->totalTerisi();
                            $persen = $loc->persentaseTerisi();
                        @endphp
                        <tr>
                            <td>
                                <a href="{{ route('admin.lokasi-gudang.show', $loc) }}" class="fw-semibold text-decoration-none">
                                    {{ $loc->kode }}
                                </a>
                            </td>
                            <td><span class="badge bg-primary bg-opacity-10 text-primary">{{ $loc->zona }}</span></td>
                            <td class="small">B{{ $loc->baris }} / K{{ $loc->kolom }}</td>
                            <td>
                                @php
                                    $tipeIcon = match($loc->tipe) {
                                        'palet'  => 'fa-pallet text-warning',
                                        'lantai' => 'fa-layer-group text-info',
                                        default  => 'fa-th-large text-success',
                                    };
                                @endphp
                                <i class="fas {{ $tipeIcon }} me-1"></i>{{ ucfirst($loc->tipe) }}
                            </td>
                            <td>{{ number_format($loc->kapasitas) }} unit</td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="progress flex-fill" style="height:6px;min-width:60px;">
                                        <div class="progress-bar {{ $persen >= 90 ? 'bg-danger' : ($persen >= 60 ? 'bg-warning' : 'bg-success') }}"
                                             style="width:{{ min($persen, 100) }}%"></div>
                                    </div>
                                    <small class="text-nowrap">{{ $terisi }}/{{ $loc->kapasitas }}</small>
                                </div>
                            </td>
                            <td>{{ $loc->products->count() }} item</td>
                            <td class="text-center">
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.lokasi-gudang.show', $loc) }}" class="btn btn-outline-primary" title="Detail">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.lokasi-gudang.edit', $loc) }}" class="btn btn-outline-warning" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <form method="POST" action="{{ route('admin.lokasi-gudang.destroy', $loc) }}" class="d-inline"
                                          onsubmit="return confirm('Yakin ingin menghapus lokasi {{ $loc->kode }}?')">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted">
                                <i class="fas fa-inbox fa-2x mb-2"></i><br>
                                Belum ada lokasi gudang. <a href="{{ route('admin.lokasi-gudang.create') }}">Tambah sekarang</a>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">{{ $locations->links() }}</div>
</div>
@endsection
