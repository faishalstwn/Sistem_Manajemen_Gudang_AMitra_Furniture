@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.barang-keluar.index') }}">Barang Keluar</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-minus-circle me-2"></i>Tambah Barang Keluar</h5>
                </div>
                <div class="card-body">

                    <div class="alert alert-warning border-0 small">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Setelah disimpan, stok produk akan <strong>otomatis berkurang</strong> sesuai jumlah yang dimasukkan.
                    </div>

                    <form method="POST" action="{{ route('admin.barang-keluar.store') }}">
                        @csrf

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Produk <span class="text-danger">*</span></label>
                            <select name="produk_id" id="produkSelect"
                                    class="form-select @error('produk_id') is-invalid @enderror" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($products as $product)
                                    <option value="{{ $product->id }}"
                                            data-stock="{{ $product->stock }}"
                                            {{ old('produk_id') == $product->id ? 'selected' : '' }}>
                                        {{ $product->name }}
                                        (Stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('produk_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="stokInfo" class="mt-1 small text-muted"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Keluar <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-danger text-white"><i class="fas fa-minus"></i></span>
                                <input type="number" name="jumlah" id="jumlahInput" min="1"
                                       class="form-control form-control-lg @error('jumlah') is-invalid @enderror"
                                       value="{{ old('jumlah') }}" placeholder="0" required>
                                <span class="input-group-text">unit</span>
                            </div>
                            @error('jumlah')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="stokBaru" class="mt-1 small fw-semibold"></div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tanggal Keluar <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_keluar"
                                       class="form-control @error('tanggal_keluar') is-invalid @enderror"
                                       value="{{ old('tanggal_keluar', date('Y-m-d')) }}" required>
                                @error('tanggal_keluar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Tujuan</label>
                                <input type="text" name="tujuan"
                                       class="form-control @error('tujuan') is-invalid @enderror"
                                       value="{{ old('tujuan') }}" placeholder="Tujuan pengiriman (opsional)">
                                @error('tujuan')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-4 mt-3">
                            <label class="form-label fw-semibold">Catatan</label>
                            <textarea name="catatan" rows="3"
                                      class="form-control @error('catatan') is-invalid @enderror"
                                      placeholder="Keterangan tambahan (opsional)">{{ old('catatan') }}</textarea>
                            @error('catatan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger flex-fill"
                                    onclick="return confirm('Yakin ingin mencatat pengeluaran barang ini?')">
                                <i class="fas fa-save me-2"></i>Simpan Barang Keluar
                            </button>
                            <a href="{{ route('admin.barang-keluar.index') }}" class="btn btn-outline-secondary">
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
const produkSelect = document.getElementById('produkSelect');
const jumlahInput  = document.getElementById('jumlahInput');
const stokInfo     = document.getElementById('stokInfo');
const stokBaru     = document.getElementById('stokBaru');

function updateInfo() {
    const opt   = produkSelect.options[produkSelect.selectedIndex];
    const stock = parseInt(opt?.dataset?.stock ?? 0);
    const qty   = parseInt(jumlahInput.value) || 0;

    if (produkSelect.value) {
        stokInfo.textContent = `Stok saat ini: ${stock} unit`;
        jumlahInput.max = stock;
    } else {
        stokInfo.textContent = '';
    }

    if (produkSelect.value && qty > 0) {
        const sisa = stock - qty;
        if (sisa < 0) {
            stokBaru.className = 'mt-1 small fw-semibold text-danger';
            stokBaru.textContent = `⚠ Jumlah melebihi stok! Stok hanya ${stock} unit.`;
        } else if (sisa < 10) {
            stokBaru.className = 'mt-1 small fw-semibold text-warning';
            stokBaru.textContent = `Stok setelah simpan: ${sisa} unit (peringatan: stok rendah)`;
        } else {
            stokBaru.className = 'mt-1 small fw-semibold text-success';
            stokBaru.textContent = `Stok setelah simpan: ${sisa} unit`;
        }
    } else {
        stokBaru.textContent = '';
    }
}

produkSelect.addEventListener('change', updateInfo);
jumlahInput.addEventListener('input', updateInfo);
updateInfo();
</script>
@endsection
