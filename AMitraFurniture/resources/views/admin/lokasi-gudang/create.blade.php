@extends('admin.layout.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-7">

            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.lokasi-gudang.index') }}">Lokasi Gudang</a></li>
                    <li class="breadcrumb-item active">Tambah Lokasi</li>
                </ol>
            </nav>

            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-plus-circle me-2"></i>Tambah Lokasi Gudang</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.lokasi-gudang.store') }}">
                        @csrf

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Kode Lokasi <span class="text-danger">*</span></label>
                                <input type="text" name="kode" class="form-control @error('kode') is-invalid @enderror"
                                       value="{{ old('kode') }}" placeholder="Contoh: A1, B2, R-01" required>
                                @error('kode')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Zona <span class="text-danger">*</span></label>
                                <input type="text" name="zona" class="form-control @error('zona') is-invalid @enderror"
                                       value="{{ old('zona') }}" placeholder="Contoh: Zona A, Zona Utara" required
                                       list="zonaOptions">
                                <datalist id="zonaOptions">
                                    @foreach($zonaList as $z)
                                        <option value="{{ $z }}">
                                    @endforeach
                                </datalist>
                                @error('zona')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Baris (Posisi Grid) <span class="text-danger">*</span></label>
                                <input type="number" name="baris" min="1" class="form-control @error('baris') is-invalid @enderror"
                                       value="{{ old('baris') }}" placeholder="1, 2, 3..." required>
                                @error('baris')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kolom (Posisi Grid) <span class="text-danger">*</span></label>
                                <input type="number" name="kolom" min="1" class="form-control @error('kolom') is-invalid @enderror"
                                       value="{{ old('kolom') }}" placeholder="1, 2, 3..." required>
                                @error('kolom')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-semibold">Kapasitas (unit) <span class="text-danger">*</span></label>
                                <input type="number" name="kapasitas" min="1" class="form-control @error('kapasitas') is-invalid @enderror"
                                       value="{{ old('kapasitas', 100) }}" required>
                                @error('kapasitas')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-semibold">Tipe Penyimpanan <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe" value="rak" id="tipeRak"
                                           {{ old('tipe', 'rak') == 'rak' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tipeRak">
                                        <i class="fas fa-th-large text-success me-1"></i>Rak
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe" value="lantai" id="tipeLantai"
                                           {{ old('tipe') == 'lantai' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tipeLantai">
                                        <i class="fas fa-layer-group text-info me-1"></i>Lantai
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="tipe" value="palet" id="tipePalet"
                                           {{ old('tipe') == 'palet' ? 'checked' : '' }}>
                                    <label class="form-check-label" for="tipePalet">
                                        <i class="fas fa-pallet text-warning me-1"></i>Palet
                                    </label>
                                </div>
                            </div>
                            @error('tipe')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control @error('keterangan') is-invalid @enderror"
                                      placeholder="Catatan tambahan (opsional)">{{ old('keterangan') }}</textarea>
                            @error('keterangan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary flex-fill">
                                <i class="fas fa-save me-2"></i>Simpan Lokasi
                            </button>
                            <a href="{{ route('admin.lokasi-gudang.index') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
