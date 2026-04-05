@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-start flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-arrow-circle-down me-2 text-success"></i>Barang Masuk</h3>
                <p class="text-muted mb-0">Riwayat penerimaan barang dari supplier & stok masuk ke gudang</p>
            </div>
            <div class="d-flex gap-2 flex-wrap">
                <a href="{{ route('admin.export.excel.barang-masuk', array_filter(['dari' => request('dari'), 'sampai' => request('sampai')])) }}"
                   class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel me-1"></i>Export Excel
                </a>
                <a href="{{ route('admin.export.pdf.barang-masuk', array_filter(['dari' => request('dari'), 'sampai' => request('sampai')])) }}"
                   class="btn btn-danger btn-sm">
                    <i class="fas fa-file-pdf me-1"></i>Export PDF
                </a>
                <a href="{{ route('admin.barang-masuk.create') }}" class="btn btn-success">
                    <i class="fas fa-plus me-2"></i>Tambah Barang Masuk
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
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-boxes fa-2x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($totalJumlah) }}</h4>
                    <small class="text-muted">Total Unit Diterima (Sepanjang Waktu)</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-calendar-day fa-2x text-primary"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($totalHariIni) }}</h4>
                    <small class="text-muted">Unit Masuk Hari Ini</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-3">
                    <div class="bg-warning bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-list-alt fa-2x text-warning"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $barangMasuk->total() }}</h4>
                    <small class="text-muted">Total Transaksi Masuk</small>
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
                    <label class="form-label small fw-semibold">Supplier</label>
                    <input type="text" name="supplier" class="form-control form-control-sm"
                           value="{{ request('supplier') }}" placeholder="Nama supplier...">
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
                    <a href="{{ route('admin.barang-masuk.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fas fa-table me-2"></i>Riwayat Barang Masuk</h6>
            <small class="text-muted">{{ $barangMasuk->total() }} transaksi</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Tanggal</th>
                            <th>Produk</th>
                            <th class="text-center">Jumlah</th>
                            <th>Supplier</th>
                            <th>Catatan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($barangMasuk as $item)
                        <tr>
                            <td class="text-nowrap">
                                <div class="fw-semibold">{{ $item->tanggal_masuk->format('d M Y') }}</div>
                                <small class="text-muted">{{ $item->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $item->produk->name ?? '-' }}</div>
                                <small class="text-muted">{{ $item->produk->category ?? '' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success fs-6">+{{ number_format($item->jumlah) }}</span>
                            </td>
                            <td>{{ $item->supplier ?: '-' }}</td>
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
                                Belum ada data barang masuk
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($barangMasuk->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $barangMasuk->firstItem() }}–{{ $barangMasuk->lastItem() }} dari {{ $barangMasuk->total() }}
            </small>
            {{ $barangMasuk->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection
