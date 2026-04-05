@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    {{-- Header --}}
    <div class="row mb-4">
        <div class="col-12 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h3 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>Riwayat Pergerakan Stok</h3>
                <p class="text-muted mb-0">Log semua pergerakan stok masuk, keluar, dan koreksi</p>
            </div>
            <a href="{{ route('admin.gudang.kelola') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Kembali ke Manajemen Stok
            </a>
        </div>
    </div>

    {{-- Ringkasan --}}
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-arrow-circle-down fa-2x text-success"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($summary['total_in']) }}</h4>
                    <small class="text-muted">Total Unit Masuk (Sepanjang Waktu)</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-danger bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-arrow-circle-up fa-2x text-danger"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ number_format($summary['total_out']) }}</h4>
                    <small class="text-muted">Total Unit Keluar (Sepanjang Waktu)</small>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="bg-info bg-opacity-10 rounded-circle d-inline-flex p-3 mb-2">
                        <i class="fas fa-sliders-h fa-2x text-info"></i>
                    </div>
                    <h4 class="fw-bold mb-0">{{ $summary['total_adjustment'] }}</h4>
                    <small class="text-muted">Total Koreksi Stok</small>
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
                    <select name="product_id" class="form-select form-select-sm">
                        <option value="">Semua Produk</option>
                        @foreach($products as $id => $name)
                            <option value="{{ $id }}" {{ request('product_id') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Tipe</label>
                    <select name="type" class="form-select form-select-sm">
                        <option value="">Semua Tipe</option>
                        <option value="in"         {{ request('type') == 'in'         ? 'selected' : '' }}>Stok Masuk</option>
                        <option value="out"        {{ request('type') == 'out'        ? 'selected' : '' }}>Stok Keluar</option>
                        <option value="adjustment" {{ request('type') == 'adjustment' ? 'selected' : '' }}>Koreksi Stok</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Dari Tanggal</label>
                    <input type="date" name="dari" class="form-control form-control-sm" value="{{ request('dari') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-semibold">Sampai Tanggal</label>
                    <input type="date" name="sampai" class="form-control form-control-sm" value="{{ request('sampai') }}">
                </div>
                <div class="col-md-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-fill">
                        <i class="fas fa-filter me-1"></i>Filter
                    </button>
                    <a href="{{ route('admin.gudang.riwayat') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Tabel Riwayat --}}
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fas fa-table me-2"></i>Log Pergerakan Stok</h6>
            <small class="text-muted">{{ $movements->total() }} entri ditemukan</small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Waktu</th>
                            <th>Produk</th>
                            <th class="text-center">Tipe</th>
                            <th class="text-center">Sebelum</th>
                            <th class="text-center">Perubahan</th>
                            <th class="text-center">Sesudah</th>
                            <th>Referensi</th>
                            <th>Catatan</th>
                            <th>Oleh</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($movements as $movement)
                        <tr>
                            <td class="text-nowrap">
                                <div style="font-size:12px">{{ $movement->created_at->format('d M Y') }}</div>
                                <small class="text-muted">{{ $movement->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <div class="fw-semibold" style="font-size:13px">
                                    {{ $movement->product->name ?? '-' }}
                                </div>
                                <small class="text-muted">{{ $movement->product->category ?? '' }}</small>
                            </td>
                            <td class="text-center">
                                <span class="badge {{ $movement->type_badge }}">{{ $movement->type_label }}</span>
                            </td>
                            <td class="text-center fw-semibold text-muted">{{ $movement->previous_stock }}</td>
                            <td class="text-center fw-bold
                                {{ $movement->type === 'in' ? 'text-success' : ($movement->type === 'out' ? 'text-danger' : 'text-info') }}">
                                {{ $movement->signed_quantity }}
                            </td>
                            <td class="text-center fw-semibold">{{ $movement->new_stock }}</td>
                            <td>
                                @if($movement->reference)
                                    <code class="small">{{ $movement->reference }}</code>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="max-width:180px">
                                <span class="text-truncate d-block small" title="{{ $movement->note }}">
                                    {{ $movement->note ?? '-' }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $movement->user->name ?? 'Sistem' }}</small>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                Tidak ada riwayat pergerakan stok yang ditemukan
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($movements->hasPages())
        <div class="card-footer bg-white d-flex justify-content-between align-items-center">
            <small class="text-muted">
                Menampilkan {{ $movements->firstItem() }}–{{ $movements->lastItem() }} dari {{ $movements->total() }} entri
            </small>
            {{ $movements->links('pagination::bootstrap-5') }}
        </div>
        @endif
    </div>

</div>
@endsection
