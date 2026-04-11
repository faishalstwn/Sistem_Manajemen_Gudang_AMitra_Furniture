@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('admin.lokasi-gudang.peta') }}">Peta Gudang</a></li>
            <li class="breadcrumb-item"><a href="{{ route('admin.lokasi-gudang.index') }}">Lokasi</a></li>
            <li class="breadcrumb-item active">{{ $location->kode }}</li>
        </ol>
    </nav>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="fas fa-times-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">

        {{-- Info Lokasi --}}
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        @php
                            $icon = match($location->tipe) {
                                'palet'  => 'fa-pallet',
                                'lantai' => 'fa-layer-group',
                                default  => 'fa-th-large',
                            };
                        @endphp
                        <i class="fas {{ $icon }} me-2"></i>{{ $location->kode }}
                    </h5>
                    <a href="{{ route('admin.lokasi-gudang.edit', $location) }}" class="btn btn-sm btn-light">
                        <i class="fas fa-edit me-1"></i>Edit
                    </a>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td class="text-muted" style="width:130px;">Zona</td>
                            <td class="fw-semibold">{{ $location->zona }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Posisi Grid</td>
                            <td>Baris {{ $location->baris }}, Kolom {{ $location->kolom }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Tipe</td>
                            <td><i class="fas {{ $icon }} me-1"></i>{{ ucfirst($location->tipe) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Kapasitas</td>
                            <td>{{ number_format($location->kapasitas) }} unit</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Terisi</td>
                            <td>
                                <span class="fw-bold">{{ $location->totalTerisi() }}</span> unit
                                ({{ $location->persentaseTerisi() }}%)
                            </td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sisa</td>
                            <td class="{{ $location->sisaKapasitas() < 10 ? 'text-danger fw-bold' : 'text-success' }}">
                                {{ $location->sisaKapasitas() }} unit tersedia
                            </td>
                        </tr>
                        @if($location->keterangan)
                        <tr>
                            <td class="text-muted">Keterangan</td>
                            <td>{{ $location->keterangan }}</td>
                        </tr>
                        @endif
                    </table>

                    {{-- Bar kapasitas --}}
                    @php $persen = $location->persentaseTerisi(); @endphp
                    <div class="mt-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>Kapasitas Terpakai</span>
                            <span class="fw-bold">{{ $persen }}%</span>
                        </div>
                        <div class="progress" style="height:10px;">
                            <div class="progress-bar {{ $persen >= 90 ? 'bg-danger' : ($persen >= 60 ? 'bg-warning' : 'bg-success') }}"
                                 style="width:{{ min($persen, 100) }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Form Tambah Produk ke Lokasi --}}
            @if($availableProducts->count() > 0 && $location->sisaKapasitas() > 0)
            <div class="card border-0 shadow-sm mt-3">
                <div class="card-header bg-success text-white">
                    <h6 class="mb-0"><i class="fas fa-plus me-2"></i>Taruh Produk di Lokasi Ini</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.lokasi-gudang.assign', $location) }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Pilih Produk</label>
                            <select name="product_id" class="form-select" required>
                                <option value="">-- Pilih Produk --</option>
                                @foreach($availableProducts as $product)
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }} (Stok: {{ $product->stock }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Jumlah</label>
                            <input type="number" name="jumlah" min="1" max="{{ $location->sisaKapasitas() }}"
                                   class="form-control" value="1" required>
                            <small class="text-muted">Sisa kapasitas: {{ $location->sisaKapasitas() }} unit</small>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i>Simpan ke Lokasi
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>

        {{-- Produk yang Tersimpan --}}
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm">
                <div class="card-header">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-boxes me-2"></i>Produk Tersimpan di {{ $location->kode }}
                        <span class="badge bg-secondary ms-1">{{ $location->products->count() }}</span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    @if($location->products->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th>Kategori</th>
                                        <th class="text-center">Jumlah di Rak</th>
                                        <th class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($location->products as $product)
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if($product->image)
                                                        <img src="{{ asset('storage/' . $product->image) }}" alt=""
                                                             style="width:40px;height:40px;object-fit:cover;border-radius:6px;">
                                                    @else
                                                        <div class="bg-light d-flex align-items-center justify-content-center"
                                                             style="width:40px;height:40px;border-radius:6px;">
                                                            <i class="fas fa-image text-muted"></i>
                                                        </div>
                                                    @endif
                                                    <div>
                                                        <div class="fw-semibold">{{ $product->name }}</div>
                                                        <small class="text-muted">Stok total: {{ $product->stock }}</small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-info bg-opacity-10 text-info">{{ $product->category }}</span></td>
                                            <td class="text-center">
                                                <form method="POST" action="{{ route('admin.lokasi-gudang.update-qty', [$location, $product]) }}"
                                                      class="d-inline-flex align-items-center gap-1">
                                                    @csrf @method('PATCH')
                                                    <input type="number" name="jumlah" value="{{ $product->pivot->jumlah }}"
                                                           min="0" max="{{ $location->kapasitas }}"
                                                           class="form-control form-control-sm text-center" style="width:70px;">
                                                    <button type="submit" class="btn btn-sm btn-outline-primary" title="Update jumlah">
                                                        <i class="fas fa-sync-alt"></i>
                                                    </button>
                                                </form>
                                            </td>
                                            <td class="text-center">
                                                <form method="POST" action="{{ route('admin.lokasi-gudang.remove', [$location, $product]) }}"
                                                      onsubmit="return confirm('Keluarkan {{ $product->name }} dari lokasi ini?')">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-outline-danger" title="Keluarkan dari rak">
                                                        <i class="fas fa-times"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5 text-muted">
                            <i class="fas fa-box-open fa-3x mb-3"></i>
                            <p class="mb-0">Lokasi ini masih kosong</p>
                            <small>Tambahkan produk menggunakan form di samping.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
