<div class="sidebar">
    <h6 class="mb-4 fw-bold">Dashboard Owner</h6>

    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
        <i class="fas fa-chart-line me-2"></i>Ringkasan
    </a>
    <a href="{{ route('admin.pesanan.index') }}" class="{{ request()->routeIs('admin.pesanan.*') ? 'active' : '' }}">
        <i class="fas fa-shopping-cart me-2"></i>Pesanan
    </a>
    <a href="{{ route('admin.produk.index') }}" class="{{ request()->routeIs('admin.produk.*') ? 'active' : '' }}">
        <i class="fas fa-box me-2"></i>Produk
    </a>
    <a href="{{ route('admin.pengiriman') }}" class="{{ request()->routeIs('admin.pengiriman') ? 'active' : '' }}">
        <i class="fas fa-shipping-fast me-2"></i>Pengiriman
    </a>

    <hr>

    {{-- ── WMS Integration ─────────────────────────────── --}}
    <small class="text-muted px-3 d-block mb-1" style="font-size:11px; text-transform:uppercase; letter-spacing:.5px;">Gudang</small>
    <a href="{{ route('admin.gudang') }}" class="{{ request()->routeIs('admin.gudang') ? 'active' : '' }}">
        <i class="fas fa-chart-bar me-2"></i>Monitor Gudang
    </a>
    <a href="{{ route('admin.gudang.kelola') }}" class="{{ request()->routeIs('admin.gudang.kelola') ? 'active' : '' }}">
        <i class="fas fa-warehouse me-2"></i>Manajemen Stok
    </a>
    <a href="{{ route('admin.barang-masuk.index') }}" class="{{ request()->routeIs('admin.barang-masuk.*') ? 'active' : '' }}">
        <i class="fas fa-arrow-circle-down me-2 text-success"></i>Barang Masuk
    </a>
    <a href="{{ route('admin.barang-keluar.index') }}" class="{{ request()->routeIs('admin.barang-keluar.*') ? 'active' : '' }}">
        <i class="fas fa-arrow-circle-up me-2 text-danger"></i>Barang Keluar
    </a>
    <a href="{{ route('admin.gudang.riwayat') }}" class="{{ request()->routeIs('admin.gudang.riwayat') ? 'active' : '' }}">
        <i class="fas fa-history me-2"></i>Riwayat Stok
    </a>
    <a href="{{ route('admin.lokasi-gudang.peta') }}" class="{{ request()->routeIs('admin.lokasi-gudang.*') ? 'active' : '' }}">
        <i class="fas fa-map-marked-alt me-2 text-info"></i>Peta Gudang
    </a>

    <hr>

    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button class="btn btn-danger w-100">
            <i class="fas fa-sign-out-alt me-2"></i>Logout
        </button>
    </form>
</div>
