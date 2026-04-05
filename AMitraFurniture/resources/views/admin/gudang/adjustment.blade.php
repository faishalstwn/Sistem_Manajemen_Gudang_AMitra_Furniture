@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6">

            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.gudang.kelola') }}">Gudang</a></li>
                    <li class="breadcrumb-item active">Koreksi Stok</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-sliders-h me-2"></i>Koreksi / Adjustment Stok</h5>
                </div>
                <div class="card-body">

                    <div class="alert alert-info border-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Gunakan fitur ini untuk mengoreksi stok fisik yang tidak sesuai dengan data sistem
                        (misalnya setelah stock opname).
                    </div>

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
                                Stok di Sistem:
                                <span class="badge fs-6 bg-secondary">{{ $product->stock }} unit</span>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="{{ route('admin.gudang.adjustment', $product) }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Stok Aktual (Hasil Hitung Fisik) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                <input type="number" name="new_stock" id="newStockInput" min="0"
                                       class="form-control form-control-lg @error('new_stock') is-invalid @enderror"
                                       value="{{ old('new_stock', $product->stock) }}" required>
                                <span class="input-group-text">unit</span>
                            </div>
                            @error('new_stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="diffPreview" class="mt-1 small fw-semibold"></div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Alasan Koreksi <span class="text-danger">*</span></label>
                            <textarea name="note" rows="3" required
                                      class="form-control @error('note') is-invalid @enderror"
                                      placeholder="Contoh: Stock opname tanggal 17 Maret 2026, ditemukan selisih karena produk rusak">{{ old('note') }}</textarea>
                            @error('note')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-info text-white flex-fill"
                                    onclick="return confirm('Yakin ingin mengubah stok? Tindakan ini akan dicatat di riwayat.')">
                                <i class="fas fa-check me-2"></i>Simpan Koreksi
                            </button>
                            <a href="{{ route('admin.gudang.kelola') }}" class="btn btn-outline-secondary">
                                Batal
                            </a>
                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</div>

<script>
const currentStock = {{ $product->stock }};
const input = document.getElementById('newStockInput');
const preview = document.getElementById('diffPreview');

function updatePreview() {
    const val = parseInt(input.value) || 0;
    const diff = val - currentStock;
    if (diff > 0) {
        preview.innerHTML = `<span class="text-success"><i class="fas fa-arrow-up"></i> Stok naik +${diff} unit (${currentStock} &rarr; ${val})</span>`;
    } else if (diff < 0) {
        preview.innerHTML = `<span class="text-danger"><i class="fas fa-arrow-down"></i> Stok turun ${diff} unit (${currentStock} &rarr; ${val})</span>`;
    } else {
        preview.innerHTML = `<span class="text-muted">Tidak ada perubahan stok</span>`;
    }
}

input.addEventListener('input', updatePreview);
updatePreview();
</script>
@endsection
