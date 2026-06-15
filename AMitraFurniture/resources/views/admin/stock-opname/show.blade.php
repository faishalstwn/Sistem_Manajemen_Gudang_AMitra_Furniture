@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <a href="{{ route('admin.stock-opname.index') }}" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i>Kembali ke Daftar
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header Info --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-8">
                    <h4 class="fw-bold mb-1">
                        <i class="fas fa-clipboard-check me-2"></i>{{ $stockOpname->kode }}
                    </h4>
                    <p class="text-muted mb-2">{{ $stockOpname->tanggal->format('d F Y') }}</p>
                    @if($stockOpname->catatan)
                        <p class="mb-0"><small class="text-muted">Catatan:</small> {{ $stockOpname->catatan }}</p>
                    @endif
                </div>
                <div class="col-md-4 text-md-end">
                    @if($stockOpname->status === 'draft')
                        <span class="badge bg-warning text-dark fs-6 mb-2">Draft</span>
                    @else
                        <span class="badge bg-success fs-6 mb-2">Selesai</span>
                    @endif
                    <br>
                    <small class="text-muted">Oleh: {{ $stockOpname->user?->name ?? '-' }}</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Ringkasan --}}
    @php
        $totalItem    = $stockOpname->items->count();
        $itemCocok    = $stockOpname->items->where('selisih', 0)->count();
        $itemSurplus  = $stockOpname->items->where('selisih', '>', 0)->count();
        $itemKurang   = $stockOpname->items->where('selisih', '<', 0)->count();
        $totalSelisih = $stockOpname->items->sum(fn($i) => abs($i->selisih));
    @endphp

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h4 class="fw-bold mb-0">{{ $totalItem }}</h4>
                    <small class="text-muted">Total Produk Dicek</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h4 class="fw-bold text-success mb-0">{{ $itemCocok }}</h4>
                    <small class="text-muted">Stok Cocok</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h4 class="fw-bold text-primary mb-0">{{ $itemSurplus }}</h4>
                    <small class="text-muted">Surplus</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm text-center">
                <div class="card-body">
                    <h4 class="fw-bold text-danger mb-0">{{ $itemKurang }}</h4>
                    <small class="text-muted">Kurang</small>
                </div>
            </div>
        </div>
    </div>

    {{-- Tabel Detail --}}
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-header bg-white">
            <h6 class="fw-bold mb-0"><i class="fas fa-list me-2"></i>Detail Pencocokan Stok</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:5%">#</th>
                            <th>Produk</th>
                            <th class="text-center">Stok Sistem</th>
                            <th class="text-center">Stok Fisik</th>
                            <th class="text-center">Selisih</th>
                            <th class="text-center">Status</th>
                            <th>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($stockOpname->items as $i => $item)
                        <tr>
                            <td class="text-center">{{ $i + 1 }}</td>
                            <td>
                                <div class="fw-semibold">{{ $item->product?->name }}</div>
                                <small class="text-muted">{{ $item->product?->category }}</small>
                            </td>
                            <td class="text-center">{{ $item->stok_sistem }}</td>
                            <td class="text-center fw-bold">{{ $item->stok_fisik }}</td>
                            <td class="text-center fw-bold
                                {{ $item->selisih > 0 ? 'text-primary' : ($item->selisih < 0 ? 'text-danger' : 'text-success') }}">
                                {{ $item->selisih > 0 ? '+' : '' }}{{ $item->selisih }}
                            </td>
                            <td class="text-center">
                                @if($item->selisih === 0)
                                    <span class="badge bg-success">Cocok</span>
                                @elseif($item->selisih > 0)
                                    <span class="badge bg-primary">Surplus</span>
                                @else
                                    <span class="badge bg-danger">Kurang</span>
                                @endif
                            </td>
                            <td>{{ $item->keterangan ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="4" class="text-end fw-bold">Total Selisih Absolut:</td>
                            <td class="text-center fw-bold">{{ $totalSelisih }} unit</td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    {{-- Approve Button --}}
    @if($stockOpname->status === 'draft' && $itemKurang + $itemSurplus > 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1">Setujui & Koreksi Stok</h6>
                    <p class="text-muted mb-0 small">
                        Stok {{ $itemKurang + $itemSurplus }} produk akan dikoreksi sesuai hasil hitung fisik.
                        Pergerakan stok akan tercatat sebagai adjustment.
                    </p>
                </div>
                <form method="POST" action="{{ route('admin.stock-opname.approve', $stockOpname) }}"
                      onsubmit="return confirm('Yakin ingin menyetujui opname ini? Stok produk akan dikoreksi sesuai stok fisik.')">
                    @csrf
                    <button class="btn btn-success btn-lg">
                        <i class="fas fa-check me-1"></i>Setujui Opname
                    </button>
                </form>
            </div>
        </div>
    @elseif($stockOpname->status === 'draft' && $itemKurang + $itemSurplus === 0)
        <div class="card border-0 shadow-sm">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="fw-bold mb-1 text-success">Semua stok cocok!</h6>
                    <p class="text-muted mb-0 small">Tidak ada koreksi yang perlu dilakukan.</p>
                </div>
                <form method="POST" action="{{ route('admin.stock-opname.approve', $stockOpname) }}">
                    @csrf
                    <button class="btn btn-success">
                        <i class="fas fa-check me-1"></i>Tandai Selesai
                    </button>
                </form>
            </div>
        </div>
    @endif
</div>
@endsection
