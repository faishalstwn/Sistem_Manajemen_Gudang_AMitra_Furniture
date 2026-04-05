@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.gudang.kelola') }}">Gudang</a></li>
                    <li class="breadcrumb-item active">Stok Keluar</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-minus-circle me-2"></i>Stok Keluar</h5>
                </div>
                <div class="card-body">

                    {{-- Info Produk --}}
                    <div class="d-flex align-items-center gap-3 p-3 bg-light rounded mb-4">
                        @if($product->image)
                            <img src="{{ asset($product->image) }}" width="60" height="60"
                                 class="rounded object-fit-cover">
                        @else
                            <div class="bg-secondary bg-opacity-10 rounded d-flex align-items-center justify-content-center"
                                 style="width:60px;height:60px">
                                <i class="fas fa-image fa-2x text-muted"></i>
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold">{{ $product->name }}</div>
                            <div class="text-muted small">{{ $product->category }}</div>
                            <div class="mt-1">
                                Stok Saat Ini:
                                <span class="badge fs-6
                                    {{ $product->stock == 0 ? 'bg-danger' : ($product->stock < 10 ? 'bg-warning text-dark' : 'bg-success') }}">
                                    {{ $product->stock }} unit
                                </span>
                            </div>
                        </div>
                    </div>

                    @if($product->stock == 0)
                        <div class="alert alert-danger">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Stok produk ini sudah habis. Tidak dapat mencatat stok keluar.
                        </div>
                        <a href="{{ route('admin.gudang.kelola') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-2"></i>Kembali
                        </a>
                    @else
                    <form method="POST" action="{{ route('admin.gudang.stok-keluar', $product) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Keluar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white"><i class="fas fa-minus"></i></span>
                                <input type="number" name="quantity" min="1" max="{{ $product->stock }}"
                                       class="form-control form-control-lg @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity') }}" placeholder="0" required>
                                <span class="input-group-text">unit</span>
                            </div>
                            <small class="text-muted">Maksimal: <strong>{{ $product->stock }} unit</strong></small>
                            @error('quantity')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nomor Referensi</label>
                            <input type="text" name="reference"
                                   class="form-control @error('reference') is-invalid @enderror"
                                   value="{{ old('reference') }}" placeholder="contoh: INV-2026-001 (opsional)">
                            @error('reference')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="note" rows="3"
                                      class="form-control @error('note') is-invalid @enderror"
                                      placeholder="Alasan pengeluaran stok (opsional)">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger flex-fill"
                                    onclick="return confirm('Yakin ingin mencatat pengeluaran stok ini?')">
                                <i class="fas fa-check me-2"></i>Catat Stok Keluar
                            </button>
                            <a href="{{ route('admin.gudang.kelola') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>
                    @endif

                </div>
            </div>

        </div>
    </div>
</div>
@endsection
