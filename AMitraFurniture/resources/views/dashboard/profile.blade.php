<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil - A Mitra Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .order-status-card {
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            position: relative;
        }
        .order-status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #429ff7;
        }
        .order-status-icon {
            font-size: 2rem;
            margin-bottom: 10px;
        }
        .badge-count {
            position: absolute;
            top: 5px;
            right: 5px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
        }
        .order-item {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }
        .order-item:hover {
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }
        .nav-tabs .nav-link {
            color: #666;
        }
        .nav-tabs .nav-link.active {
            color: #429ff7;
            font-weight: bold;
        }
    </style>
</head>
<body class="bg-light">
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand fw-bold text-primary" href="{{ route('home') }}">
                <i class="fas fa-couch me-2"></i>A Mitra Furniture
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('cart.index') }}">Keranjang</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="{{ route('profile.show') }}">Profil</a>
                    </li>
                    <li class="nav-item">
                        <form action="{{ route('logout') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="nav-link btn btn-link text-danger">Logout</button>
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Edit Profile Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-user-edit me-2"></i>Edit Profil</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.update') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                   id="name" name="name" value="{{ old('name', $user->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                   id="email" name="email" value="{{ old('email', $user->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                   id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                   placeholder="08xx-xxxx-xxxx">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="address" class="form-label">Alamat</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="3" 
                                      placeholder="Masukkan alamat lengkap">{{ old('address', $user->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Section -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-lock me-2"></i>Ubah Password</h5>
            </div>
            <div class="card-body">
                <form action="{{ route('profile.updatePassword') }}" method="POST">
                    @csrf
                    @method('PATCH')
                    
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="current_password" class="form-label">Password Lama <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('current_password') is-invalid @enderror" 
                                   id="current_password" name="current_password" required>
                            @error('current_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="new_password" class="form-label">Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control @error('new_password') is-invalid @enderror" 
                                   id="new_password" name="new_password" required>
                            @error('new_password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Minimal 8 karakter</small>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <label for="new_password_confirmation" class="form-label">Konfirmasi Password Baru <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" 
                                   id="new_password_confirmation" name="new_password_confirmation" required>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-key me-2"></i>Ubah Password
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- User Info Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                        <i class="fas fa-user fa-2x text-white"></i>
                    </div>
                    <div class="ms-3">
                        <h4 class="mb-0">{{ $user->name }}</h4>
                        <p class="text-muted mb-0">{{ $user->email }}</p>
                        @if($user->phone)
                            <p class="text-muted mb-0"><i class="fas fa-phone me-1"></i>{{ $user->phone }}</p>
                        @endif
                        @if($user->address)
                            <p class="text-muted mb-0"><i class="fas fa-map-marker-alt me-1"></i>{{ $user->address }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Status Cards -->
        <div class="row mb-4">
            <div class="col-12">
                <h5 class="mb-3"><i class="fas fa-shopping-bag me-2"></i>Status Pesanan</h5>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="order-status-card" onclick="showTab('unpaid')">
                    @if($ordersUnpaid->count() > 0)
                        <span class="badge-count">{{ $ordersUnpaid->count() }}</span>
                    @endif
                    <div class="order-status-icon">
                        <i class="fas fa-money-bill-wave text-warning"></i>
                    </div>
                    <small class="fw-bold">Belum Bayar</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="order-status-card" onclick="showTab('paid')">
                    @if($ordersPaid->count() > 0)
                        <span class="badge-count">{{ $ordersPaid->count() }}</span>
                    @endif
                    <div class="order-status-icon">
                        <i class="fas fa-check-circle text-success"></i>
                    </div>
                    <small class="fw-bold">Sudah Dibayar</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="order-status-card" onclick="showTab('processing')">
                    @if($ordersProcessing->count() > 0)
                        <span class="badge-count">{{ $ordersProcessing->count() }}</span>
                    @endif
                    <div class="order-status-icon">
                        <i class="fas fa-box text-info"></i>
                    </div>
                    <small class="fw-bold">Belum Dikirim</small>
                </div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="order-status-card" onclick="showTab('shipping')">
                    @if($ordersShipping->count() > 0)
                        <span class="badge-count">{{ $ordersShipping->count() }}</span>
                    @endif
                    <div class="order-status-icon">
                        <i class="fas fa-shipping-fast text-primary"></i>
                    </div>
                    <small class="fw-bold">Sedang Dikirim</small>
                </div>
            </div>
        </div>

        <!-- Order History Tabs -->
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <ul class="nav nav-tabs card-header-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" data-bs-toggle="tab" href="#yesterday" role="tab">
                            <i class="far fa-calendar me-1"></i>Kemarin
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#unpaid" role="tab" id="unpaid-tab">
                            <i class="fas fa-money-bill-wave me-1"></i>Belum Bayar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#paid" role="tab" id="paid-tab">
                            <i class="fas fa-check-circle me-1"></i>Sudah Dibayar
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#processing" role="tab" id="processing-tab">
                            <i class="fas fa-box me-1"></i>Belum Dikirim
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" data-bs-toggle="tab" href="#shipping" role="tab" id="shipping-tab">
                            <i class="fas fa-shipping-fast me-1"></i>Sedang Dikirim
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <!-- Yesterday Orders -->
                    <div class="tab-pane fade show active" id="yesterday" role="tabpanel">
                        @if($ordersYesterday->count() > 0)
                            @foreach($ordersYesterday as $order)
                                @include('dashboard.partials.order-item', ['order' => $order])
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada pesanan kemarin</p>
                            </div>
                        @endif
                    </div>

                    <!-- Unpaid Orders -->
                    <div class="tab-pane fade" id="unpaid" role="tabpanel">
                        @if($ordersUnpaid->count() > 0)
                            @foreach($ordersUnpaid as $order)
                                @include('dashboard.partials.order-item', ['order' => $order])
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-check-double fa-3x text-success mb-3"></i>
                                <p class="text-muted">Semua pesanan sudah dibayar</p>
                            </div>
                        @endif
                    </div>

                    <!-- Paid Orders -->
                    <div class="tab-pane fade" id="paid" role="tabpanel">
                        @if($ordersPaid->count() > 0)
                            @foreach($ordersPaid as $order)
                                @include('dashboard.partials.order-item', ['order' => $order])
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-money-bill-wave fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Belum ada pesanan yang dibayar</p>
                            </div>
                        @endif
                    </div>

                    <!-- Processing Orders (Belum Dikirim) -->
                    <div class="tab-pane fade" id="processing" role="tabpanel">
                        @if($ordersProcessing->count() > 0)
                            @foreach($ordersProcessing as $order)
                                @include('dashboard.partials.order-item', ['order' => $order])
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada pesanan yang sedang diproses</p>
                            </div>
                        @endif
                    </div>

                    <!-- Shipping Orders (Sedang Dikirim) -->
                    <div class="tab-pane fade" id="shipping" role="tabpanel">
                        @if($ordersShipping->count() > 0)
                            @foreach($ordersShipping as $order)
                                @include('dashboard.partials.order-item', ['order' => $order])
                            @endforeach
                        @else
                            <div class="text-center py-5">
                                <i class="fas fa-truck fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Tidak ada pesanan yang sedang dikirim</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="row mt-4">
            <div class="col-md-6 mb-3">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary w-100">
                    <i class="fas fa-history me-2"></i>Lihat Semua Riwayat
                </a>
            </div>
            <div class="col-md-6 mb-3">
                <a href="{{ route('home') }}" class="btn btn-primary w-100">
                    <i class="fas fa-shopping-bag me-2"></i>Belanja Lagi
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function showTab(tabName) {
            const tabElement = document.getElementById(tabName + '-tab');
            if (tabElement) {
                const tab = new bootstrap.Tab(tabElement);
                tab.show();
            }
        }
    </script>
</body>
</html>
