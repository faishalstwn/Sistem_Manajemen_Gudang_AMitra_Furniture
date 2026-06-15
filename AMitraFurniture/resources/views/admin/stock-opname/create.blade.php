@extends('admin.layout.app')

@section('content')
<div class="container-fluid">

    <div class="mb-4">
        <a href="{{ route('admin.stock-opname.index') }}" class="text-decoration-none">
            <i class="fas fa-arrow-left me-1"></i>Kembali
        </a>
    </div>

    <h3 class="fw-bold mb-1"><i class="fas fa-clipboard-check me-2"></i>Buat Stock Opname</h3>
    <p class="text-muted mb-4">Pilih produk dan masukkan jumlah stok fisik hasil penghitungan</p>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.stock-opname.store') }}" id="formOpname">
        @csrf

        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Kode Opname</label>
                        <input type="text" class="form-control" value="{{ $kode }}" readonly>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Tanggal</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ old('tanggal', now()->format('Y-m-d')) }}" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Catatan</label>
                        <input type="text" name="catatan" class="form-control"
                               value="{{ old('catatan') }}" placeholder="Opsional...">
                    </div>
                </div>
            </div>
        </div>

        {{-- Tambah Produk --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0"><i class="fas fa-boxes me-2"></i>Daftar Produk</h6>
                <button type="button" class="btn btn-sm btn-success" id="btnTambahSemua">
                    <i class="fas fa-plus-circle me-1"></i>Tambahkan Semua Produk
                </button>
            </div>
            <div class="card-body">
                <div class="row g-2 mb-3">
                    <div class="col-md-8">
                        <select class="form-select" id="selectProduk">
                            <option value="">-- Pilih produk untuk ditambahkan --</option>
                            @foreach($products as $p)
                                <option value="{{ $p->id }}" data-name="{{ $p->name }}" data-stock="{{ $p->stock }}">
                                    {{ $p->name }} (Stok: {{ $p->stock }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-primary w-100" id="btnTambah">
                            <i class="fas fa-plus me-1"></i>Tambah
                        </button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered mb-0" id="tabelItem">
                        <thead class="table-light">
                            <tr>
                                <th style="width:5%">#</th>
                                <th style="width:30%">Produk</th>
                                <th style="width:15%" class="text-center">Stok Sistem</th>
                                <th style="width:15%" class="text-center">Stok Fisik</th>
                                <th style="width:15%" class="text-center">Selisih</th>
                                <th style="width:15%">Keterangan</th>
                                <th style="width:5%"></th>
                            </tr>
                        </thead>
                        <tbody id="bodyItem">
                            {{-- Dynamic rows --}}
                        </tbody>
                    </table>
                </div>

                <div id="emptyMsg" class="text-center text-muted py-3">
                    <i class="fas fa-info-circle me-1"></i>Belum ada produk ditambahkan
                </div>
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-primary btn-lg" id="btnSimpan" disabled>
                <i class="fas fa-save me-1"></i>Simpan Stock Opname
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const bodyItem   = document.getElementById('bodyItem');
    const emptyMsg   = document.getElementById('emptyMsg');
    const btnSimpan  = document.getElementById('btnSimpan');
    const selectEl   = document.getElementById('selectProduk');
    let rowIndex = 0;
    const addedIds = new Set();

    function updateUI() {
        const rows = bodyItem.querySelectorAll('tr');
        emptyMsg.style.display = rows.length === 0 ? '' : 'none';
        btnSimpan.disabled     = rows.length === 0;

        rows.forEach((row, i) => {
            row.querySelector('.row-num').textContent = i + 1;
        });
    }

    function addRow(id, name, stock) {
        if (addedIds.has(id)) return;
        addedIds.add(id);

        const tr = document.createElement('tr');
        tr.dataset.productId = id;
        tr.innerHTML = `
            <td class="row-num text-center">${++rowIndex}</td>
            <td>
                ${name}
                <input type="hidden" name="items[${rowIndex}][product_id]" value="${id}">
            </td>
            <td class="text-center fw-semibold">${stock}</td>
            <td>
                <input type="number" name="items[${rowIndex}][stok_fisik]" 
                       class="form-control form-control-sm text-center input-fisik" 
                       min="0" value="${stock}" required data-sistem="${stock}">
            </td>
            <td class="text-center selisih-cell">0</td>
            <td>
                <input type="text" name="items[${rowIndex}][keterangan]" 
                       class="form-control form-control-sm" placeholder="Opsional">
            </td>
            <td class="text-center">
                <button type="button" class="btn btn-sm btn-outline-danger btn-hapus">
                    <i class="fas fa-times"></i>
                </button>
            </td>
        `;
        bodyItem.appendChild(tr);
        updateUI();

        // Selisih calculation
        const inputFisik = tr.querySelector('.input-fisik');
        const selisihCell = tr.querySelector('.selisih-cell');

        inputFisik.addEventListener('input', function () {
            const fisik  = parseInt(this.value) || 0;
            const sistem = parseInt(this.dataset.sistem);
            const diff   = fisik - sistem;
            selisihCell.textContent = diff > 0 ? '+' + diff : diff;
            selisihCell.className   = 'text-center selisih-cell fw-bold ' +
                (diff > 0 ? 'text-primary' : diff < 0 ? 'text-danger' : 'text-success');
        });

        // Remove button
        tr.querySelector('.btn-hapus').addEventListener('click', function () {
            addedIds.delete(id);
            tr.remove();
            updateUI();
        });
    }

    document.getElementById('btnTambah').addEventListener('click', function () {
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt.value) return;
        addRow(opt.value, opt.dataset.name, parseInt(opt.dataset.stock));
        selectEl.value = '';
    });

    document.getElementById('btnTambahSemua').addEventListener('click', function () {
        Array.from(selectEl.options).forEach(opt => {
            if (!opt.value) return;
            addRow(opt.value, opt.dataset.name, parseInt(opt.dataset.stock));
        });
    });

    updateUI();
});
</script>
@endsection
