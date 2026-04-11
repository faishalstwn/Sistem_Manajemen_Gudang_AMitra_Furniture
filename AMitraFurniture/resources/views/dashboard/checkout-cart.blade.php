<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - A Mitra Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg app-header sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">A Mitra Furniture</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <h2 class="mb-4"><i class="fas fa-shopping-cart me-2"></i>Checkout</h2>

                {{-- Display Errors --}}
                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show">
                        <strong>Terjadi kesalahan!</strong>
                        <ul class="mb-0 mt-2">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                {{-- Checkout Form --}}
                <form action="{{ route('orders.store') }}" method="POST" id="checkout-form">
                    @csrf

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>Informasi Pengiriman</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label for="alamat" class="form-label fw-bold">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea 
                                    class="form-control @error('alamat') is-invalid @enderror" 
                                    id="alamat" 
                                    name="alamat" 
                                    rows="3" 
                                    placeholder="Masukkan alamat lengkap pengiriman"
                                    required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nomor_telepon" class="form-label fw-bold">Nomor Telepon <span class="text-danger">*</span></label>
                                <input 
                                    type="tel" 
                                    class="form-control @error('nomor_telepon') is-invalid @enderror" 
                                    id="nomor_telepon" 
                                    name="nomor_telepon" 
                                    placeholder="08xxx"
                                    value="{{ old('nomor_telepon') }}"
                                    required>
                                @error('nomor_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-credit-card me-2"></i>Metode Pembayaran</h5>
                        </div>
                        <div class="card-body">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="midtrans" value="midtrans" checked>
                                <label class="form-check-label" for="midtrans">
                                    <strong><i class="fas fa-credit-card me-2 text-info"></i>Midtrans Payment Gateway</strong>
                                    <p class="text-muted mb-0 small">Kartu Kredit/Debit, E-Wallet (GoPay, OVO, DANA), Transfer Bank</p>
                                    <span class="badge bg-info small">Recommended</span>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                <label class="form-check-label" for="cod">
                                    <strong><i class="fas fa-money-bill-wave me-2 text-success"></i>Cash on Delivery (COD)</strong>
                                    <p class="text-muted mb-0 small">Bayar saat barang diterima</p>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer_bca" value="transfer_bca">
                                <label class="form-check-label" for="transfer_bca">
                                    <strong><i class="fas fa-university me-2 text-primary"></i>Transfer Bank BCA</strong>
                                    <p class="text-muted mb-0 small">Rek: 1234567890 - A Mitra Furniture</p>
                                </label>
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer_bri" value="transfer_bri">
                                <label class="form-check-label" for="transfer_bri">
                                    <strong><i class="fas fa-university me-2 text-info"></i>Transfer Bank BRI</strong>
                                    <p class="text-muted mb-0 small">Rek: 9876543210 - A Mitra Furniture</p>
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer_mandiri" value="transfer_mandiri">
                                <label class="form-check-label" for="transfer_mandiri">
                                    <strong><i class="fas fa-university me-2 text-warning"></i>Transfer Bank Mandiri</strong>
                                    <p class="text-muted mb-0 small">Rek: 5555666677 - A Mitra Furniture</p>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg w-100" id="checkout-btn">
                        <i class="fas fa-check-circle me-2"></i>
                        <span id="btn-text">Buat Pesanan</span>
                        <span id="btn-loading" style="display: none;">
                            <i class="fas fa-spinner fa-spin me-2"></i>Memproses...
                        </span>
                    </button>
                </form>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm sticky-top" style="top: 100px;">
                    <div class="card-header bg-light">
                        <h5 class="mb-0">Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        {{-- Cart Items --}}
                        @foreach($carts as $cart)
                            <div class="d-flex justify-content-between align-items-start mb-3 pb-3 border-bottom">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $cart->product->name }}</h6>
                                    <small class="text-muted">
                                        {{ $cart->quantity }} x Rp {{ number_format($cart->product->price, 0, ',', '.') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <strong>Rp {{ number_format($cart->product->price * $cart->quantity, 0, ',', '.') }}</strong>
                                </div>
                            </div>
                        @endforeach

                        <hr>

                        {{-- Total --}}
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Subtotal:</span>
                            <strong>Rp {{ number_format($total, 0, ',', '.') }}</strong>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span>Ongkir:</span>
                            <strong class="text-success">Gratis</strong>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Total:</h5>
                            <h4 class="text-primary mb-0">Rp {{ number_format($total, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Tambahkan loading state ketika form di-submit
        document.getElementById('checkout-form').addEventListener('submit', function(e) {
            const btn = document.getElementById('checkout-btn');
            const btnText = document.getElementById('btn-text');
            const btnLoading = document.getElementById('btn-loading');
            
            // Disable button dan tampilkan loading
            btn.disabled = true;
            btnText.style.display = 'none';
            btnLoading.style.display = 'inline';
            
            console.log('Form submitted - processing checkout...');
        });
    </script>
</body>
</html>
