@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-warehouse me-2 text-primary"></i>Manajemen Stok Gudang</h3>
                <p class="text-muted mb-0">Kelola keluar-masuk stok produk & pantau kondisi gudang secara real-time</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.export.excel.stok') }}" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('admin.export.pdf.stok') }}" class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
                <a href="{{ route('admin.gudang.riwayat') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-history me-1"></i>Riwayat Pergerakan
                </a>
                <a href="{{ route('admin.gudang') }}" class="btn btn-outline-info btn-sm">
                    <i class="fas fa-chart-bar me-1"></i>Monitor Gudang
                </a>
            </div>
        </div>
    </div>

    {{-- Alert --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-boxes fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $stats['total'] }}</h4>
                    <small class="text-muted">Total Produk</small>
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-check-circle fa-2x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $stats['aman'] }}</h4>
                    <small class="text-muted">Stok Aman (&ge;10)</small>
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-exclamation-triangle fa-2x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $stats['rendah'] }}</h4>
                    <small class="text-muted">Stok Rendah (&lt;10)</small>
                </div>
            </div>
        </div>
        <div class="col-md col-6">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-times-circle fa-2x text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $stats['habis'] }}</h4>
                    <small class="text-muted">Stok Habis</small>
                </div>
            </div>
        </div>
        <div class="col-md col-12">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body text-center py-3">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-layer-group fa-2x text-info"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($stats['total_unit']) }}</h4>
                    <small class="text-muted">Total Unit di Gudang</small>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        {{-- Tabel Stok Produk --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white">
                    <h6 class="fw-bold mb-3"><i class="fas fa-list me-2"></i>Daftar Stok Produk</h6>
                    {{-- Filter Form --}}
                    <form method="GET" class="row g-2 align-items-end">
                        <div class="col-md-4">
                            <input type="text" name="cari" value="{{ request('cari') }}"
                                   class="form-control form-control-sm" placeholder="Cari nama produk...">
                        </div>
                        <div class="col-md-3">
                            <select name="kategori" class="form-select form-select-sm">
                                <option value="">Semua Kategori</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat }}" {{ request('kategori') == $cat ? 'selected' : '' }}>
                                        {{ $cat }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-3">
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <option value="aman"   {{ request('status') == 'aman'   ? 'selected' : '' }}>Aman (&ge;10)</option>
                                <option value="rendah" {{ request('status') == 'rendah' ? 'selected' : '' }}>Rendah (&lt;10)</option>
                                <option value="habis"  {{ request('status') == 'habis'  ? 'selected' : '' }}>Habis (=0)</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex gap-1">
                            <button class="btn btn-primary btn-sm flex-fill">
                                <i class="fas fa-search"></i>
                            </button>
                            <a href="{{ route('admin.gudang.kelola') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </form>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Produk</th>
                                    <th>Kategori</th>
                                    <th class="text-center">Stok</th>
                                    <th class="text-center">Status</th>
                                    <th class="text-center" style="min-width:160px">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($products as $product)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            @if($product->image)
                                                <img src="{{ asset($product->image) }}" width="40" height="40"
                                                     class="rounded object-fit-cover">
                                            @else
                                                <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                                     style="width:40px;height:40px">
                                                    <i class="fas fa-image text-muted small"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="fw-semibold" style="font-size:13px">{{ $product->name }}</div>
                                                <small class="text-muted">Rp {{ number_format($product->price,0,',','.') }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark">{{ $product->category ?? '-' }}</span>
                                    </td>
                                    <td class="text-center fw-bold fs-6
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
                                        <div class="btn-group btn-group-sm" role="group">
                                            <a href="{{ route('admin.gudang.stok-masuk.form', $product) }}"
                                               class="btn btn-outline-success" title="Stok Masuk">
                                                <i class="fas fa-plus"></i>
                                            </a>
                                            <a href="{{ route('admin.gudang.stok-keluar.form', $product) }}"
                                               class="btn btn-outline-danger" title="Stok Keluar"
                                               {{ $product->stock == 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-minus"></i>
                                            </a>
                                            <a href="{{ route('admin.gudang.adjustment.form', $product) }}"
                                               class="btn btn-outline-info" title="Koreksi Stok">
                                                <i class="fas fa-sliders-h"></i>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5 text-muted">
                                        <i class="fas fa-box-open fa-3x mb-3 d-block"></i>
                                        Tidak ada produk yang ditemukan
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if($products->hasPages())
                <div class="card-footer bg-white d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Menampilkan {{ $products->firstItem() }}–{{ $products->lastItem() }} dari {{ $products->total() }} produk
                    </small>
                    {{ $products->links('pagination::bootstrap-5') }}
                </div>
                @endif
            </div>
        </div>

        {{-- Pergerakan Stok Terbaru --}}
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-exchange-alt me-2"></i>Aktivitas Terbaru</h6>
                    <a href="{{ route('admin.gudang.riwayat') }}" class="btn btn-sm btn-outline-secondary">
                        Lihat Semua
                    </a>
                </div>
                <div class="card-body p-0">
                    @forelse($recentMovements as $movement)
                    <div class="d-flex align-items-start gap-2 px-3 py-2 border-bottom">
                        <div class="mt-1">
                            <span class="badge {{ $movement->type_badge }} rounded-pill px-2 py-1"
                                  style="font-size:10px">
                                {{ $movement->type_label }}
                            </span>
                        </div>
                        <div class="flex-fill" style="min-width:0">
                            <div class="fw-semibold text-truncate" style="font-size:12px">
                                {{ $movement->product->name }}
                            </div>
                            <div class="d-flex justify-content-between">
                                <small class="text-muted">
                                    {{ $movement->previous_stock }} &rarr; {{ $movement->new_stock }}
                                    @if($movement->type !== 'adjustment')
                                        <span class="{{ $movement->type === 'in' ? 'text-success' : 'text-danger' }} fw-bold">
                                            ({{ $movement->signed_quantity }})
                                        </span>
                                    @endif
                                </small>
                                <small class="text-muted ms-1">{{ $movement->created_at->diffForHumans() }}</small>
                            </div>
                            @if($movement->note)
                                <small class="text-muted fst-italic text-truncate d-block">{{ $movement->note }}</small>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-history fa-2x mb-2 d-block"></i>
                        Belum ada pergerakan stok
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
