@extends('admin.layout.app')

@section('content')
<style>
    /* Custom pagination styles for barang-keluar page */
    .pagination {
        margin-bottom: 0;
        gap: 0.5rem;
        display: flex;
        flex-wrap: wrap;
    }
    
    .pagination .page-item {
        margin: 0 2px;
    }
    
    .pagination .page-link {
        padding: 0.5rem 0.85rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        color: #495057;
        border: 1px solid #dee2e6;
        min-width: 40px;
        text-align: center;
        transition: all 0.2s ease;
    }
    
    .pagination .page-item.active .page-link {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: white;
        font-weight: 600;
        box-shadow: 0 2px 4px rgba(13, 110, 253, 0.25);
    }
    
    .pagination .page-link:hover:not(.disabled) {
        background-color: #e9ecef;
        color: #0d6efd;
        border-color: #0d6efd;
        text-decoration: none;
    }
    
    .pagination .page-item.disabled .page-link {
        color: #adb5bd;
        pointer-events: none;
        background-color: #f8f9fa;
        border-color: #dee2e6;
        cursor: not-allowed;
    }
    
    /* Responsive pagination */
    @media (max-width: 576px) {
        .pagination .page-link {
            padding: 0.4rem 0.65rem;
            font-size: 0.8rem;
            min-width: 35px;
        }
    }
</style>

<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-arrow-circle-up me-2 text-danger"></i>Barang Keluar</h3>
                <p class="text-muted mb-0">Riwayat pengeluaran barang dari gudang</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.export.excel.barang-keluar', array_filter(['dari' => request('dari'), 'sampai' => request('sampai')])) }}"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('admin.export.pdf.barang-keluar', array_filter(['dari' => request('dari'), 'sampai' => request('sampai')])) }}"
                   class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
                <a href="{{ route('admin.barang-keluar.create') }}" class="btn btn-danger">
                    <i class="fas fa-plus me-2"></i>Tambah Barang Keluar
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

    {{-- Stat Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-dolly fa-2x text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($totalJumlah) }}</h4>
                    <small class="text-muted">Total Unit Keluar (Sepanjang Waktu)</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-calendar-day fa-2x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($totalHariIni) }}</h4>
                    <small class="text-muted">Unit Keluar Hari Ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-list-alt fa-2x text-info"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $barangKeluar->total() }}</h4>
                    <small class="text-muted">Total Transaksi Keluar</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Filter --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Produk</label>
                    <select name="produk_id" class="form-select form-select-sm">
                        <option value="">Semua Produk</option>
                        @foreach($products as $id => $name)
                            <option value="{{ $id }}" {{ request('produk_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Tujuan</label>
                    <input type="text" name="tujuan" class="form-control form-control-sm"
                           value="{{ request('tujuan') }}" placeholder="Tujuan pengiriman...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-2 d-flex gap-1">
                    <button class="btn btn-primary btn-sm flex-fill"><i class="fas fa-search me-1"></i>Cari</button>
                    <a href="{{ route('admin.barang-keluar.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fas fa-table me-2"></i>Riwayat Barang Keluar</h6>
            <small class="text-muted">{{ $barangKeluar->total() }} transaksi</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th class="text-center">Jumlah</th>
                            <th>Tujuan</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangKeluar as $item)
                        <tr>
                            <td class="text-nowrap">
                                <div class="fw-semibold">{{ $item->tanggal_keluar->format('d M Y') }}</div>
                                <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->produk->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->produk->category ?? '' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-danger fs-6">-{{ number_format($item->jumlah) }}</span>
                            </td>
                            <td>{{ $item->tujuan ?: '-' }}</td>
                            <td>
                                <span class="text-truncate d-block small" style="max-width:200px"
                                      title="{{ $item->catatan }}">
                                    {{ $item->catatan ?: '-' }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Belum ada data barang keluar
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($barangKeluar->hasPages())
        <div class="card-footer bg-white py-3">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div class="text-muted small">
                    Menampilkan <strong>{{ $barangKeluar->firstItem() }}</strong> - <strong>{{ $barangKeluar->lastItem() }}</strong> dari <strong>{{ $barangKeluar->total() }}</strong> transaksi
                </div>
                <nav aria-label="Page navigation">
                    {{ $barangKeluar->links('pagination::bootstrap-5') }}
                </nav>
            </div>
        </div>
        @endif
    </div>

</div>
@endsection
