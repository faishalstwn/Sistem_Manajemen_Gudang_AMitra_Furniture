@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.barang-masuk.index') }}">Barang Masuk</a></li>
                    <li class="breadcrumb-item active">Tambah Baru</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah Barang Masuk</h5>
                </div>
                <div class="card-body">

                    <form method="POST" action="{{ route('admin.barang-masuk.store') }}">
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
                                        (Stok Saat Ini: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                            @error('produk_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="stokInfo" class="mt-1 small text-muted"></div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah Masuk <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-success text-white"><i class="fas fa-plus"></i></span>
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
                                <label class="form-label fw-semibold">Tanggal Masuk <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal_masuk"
                                       class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                       value="{{ old('tanggal_masuk', date('Y-m-d')) }}" required>
                                @error('tanggal_masuk')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Supplier</label>
                                <input type="text" name="supplier"
                                       class="form-control @error('supplier') is-invalid @enderror"
                                       value="{{ old('supplier') }}" placeholder="Nama supplier (opsional)">
                                @error('supplier')
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
                            <button type="submit" class="btn btn-success flex-fill"
                                    onclick="return confirm('Yakin ingin menyimpan data barang masuk ini?')">
                                <i class="fas fa-save me-2"></i>Simpan Barang Masuk
                            </button>
                            <a href="{{ route('admin.barang-masuk.index') }}" class="btn btn-outline-secondary">
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
    } else {
        stokInfo.textContent = '';
    }

    if (produkSelect.value && qty > 0) {
        const total = stock + qty;
        stokBaru.className = 'mt-1 small fw-semibold text-success';
        stokBaru.textContent = `Stok setelah simpan: ${total} unit`;
    } else {
        stokBaru.textContent = '';
    }
}

produkSelect.addEventListener('change', updateInfo);
jumlahInput.addEventListener('input', updateInfo);
updateInfo();
</script>
@endsection
