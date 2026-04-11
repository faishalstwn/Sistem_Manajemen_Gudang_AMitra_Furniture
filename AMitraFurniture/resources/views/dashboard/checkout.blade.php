<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Beli Sekarang</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-light">
    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <button class="btn btn-outline-secondary me-3" onclick="window.history.back()">
                <i class="fas fa-arrow-left"></i>
            </button>
            <h2 class="mb-0">Checkout</h2>
        </div>

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <form action="{{ route('orders.storeDirect') }}" method="POST" id="checkoutForm">
            @csrf
            <input type="hidden" name="product_id" value="{{ $product->id }}">
            
            <div class="row">
                <!-- Left Column - Form -->
                <div class="col-lg-7">
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-box-open text-primary"></i> Detail Produk
                            </h5>
                            
                            <div class="d-flex gap-3 mb-4">
                                <img src="{{ asset($product->image) }}" 
                                     alt="{{ $product->name }}" 
                                     class="rounded"
                                     style="width: 100px; height: 100px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1">{{ $product->name }}</h6>
                                    <p class="text-muted mb-2">{{ Str::limit($product->description, 80) }}</p>
                                    <p class="text-primary fw-bold mb-0">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                </div>
                            </div>

                            <!-- Quantity -->
                            <div class="mb-3">
                                <label class="form-label">Jumlah</label>
                                <div class="input-group" style="width: 150px;">
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(-1)">
                                        <i class="fas fa-minus"></i>
                                    </button>
                                    <input type="number" 
                                           name="quantity" 
                                           id="quantity" 
                                           class="form-control text-center" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $product->stock }}"
                                           readonly>
                                    <button type="button" class="btn btn-outline-secondary" onclick="changeQuantity(1)">
                                        <i class="fas fa-plus"></i>
                                    </button>
                                </div>
                                <small class="text-muted">Stok tersedia: {{ $product->stock }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Alamat Pengiriman -->
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-map-marker-alt text-primary"></i> Alamat Pengiriman
                            </h5>
                            
                            <div class="mb-3">
                                <label for="alamat" class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                                <textarea class="form-control @error('alamat') is-invalid @enderror" 
                                          id="alamat" 
                                          name="alamat" 
                                          rows="3" 
                                          placeholder="Masukkan alamat lengkap..." 
                                          required>{{ old('alamat') }}</textarea>
                                @error('alamat')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="nomor_telepon" class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                                <input type="tel" 
                                       class="form-control @error('nomor_telepon') is-invalid @enderror" 
                                       id="nomor_telepon" 
                                       name="nomor_telepon" 
                                       placeholder="08xx xxxx xxxx" 
                                       value="{{ old('nomor_telepon') }}"
                                       required>
                                @error('nomor_telepon')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Metode Pembayaran -->
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title mb-3">
                                <i class="fas fa-credit-card text-primary"></i> Metode Pembayaran
                            </h5>
                            
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="midtrans" value="midtrans" checked>
                                <label class="form-check-label d-flex align-items-center" for="midtrans">
                                    <i class="fas fa-credit-card text-info me-2"></i>
                                    <div>
                                        <strong>Midtrans Payment Gateway</strong>
                                        <div class="small text-muted">Kartu Kredit, E-Wallet, Transfer Bank</div>
                                        <span class="badge bg-info small mt-1">Recommended</span>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="cod" value="cod">
                                <label class="form-check-label d-flex align-items-center" for="cod">
                                    <i class="fas fa-money-bill-wave text-success me-2"></i>
                                    <div>
                                        <strong>COD (Bayar di Tempat)</strong>
                                        <div class="small text-muted">Bayar saat barang diterima</div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer_bca" value="transfer_bca">
                                <label class="form-check-label d-flex align-items-center" for="transfer_bca">
                                    <i class="fas fa-university text-primary me-2"></i>
                                    <div>
                                        <strong>Transfer Bank BCA</strong>
                                        <div class="small text-muted">Rek: 1234567890 - A Mitra Furniture</div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check mb-3">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer_bri" value="transfer_bri">
                                <label class="form-check-label d-flex align-items-center" for="transfer_bri">
                                    <i class="fas fa-university text-info me-2"></i>
                                    <div>
                                        <strong>Transfer Bank BRI</strong>
                                        <div class="small text-muted">Rek: 9876543210 - A Mitra Furniture</div>
                                    </div>
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="payment_method" id="transfer_mandiri" value="transfer_mandiri">
                                <label class="form-check-label d-flex align-items-center" for="transfer_mandiri">
                                    <i class="fas fa-university text-warning me-2"></i>
                                    <div>
                                        <strong>Transfer Bank Mandiri</strong>
                                        <div class="small text-muted">Rek: 5555666677 - A Mitra Furniture</div>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column - Summary -->
                <div class="col-lg-5">
                    <div class="card shadow-sm sticky-top" style="top: 20px;">
                        <div class="card-body">
                            <h5 class="card-title mb-3">Ringkasan Pesanan</h5>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Harga Satuan</span>
                                <span id="unitPrice">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Jumlah</span>
                                <span id="displayQuantity">1</span>
                            </div>
                            
                            <hr>
                            
                            <div class="d-flex justify-content-between mb-3">
                                <strong>Total</strong>
                                <strong class="text-primary" id="totalPrice">Rp {{ number_format($product->price, 0, ',', '.') }}</strong>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" id="submitBtn">
                                <i class="fas fa-check-circle me-2"></i>
                                <span id="btnText">Buat Pesanan</span>
                                <span id="btnLoading" class="spinner-border spinner-border-sm ms-2" style="display: none;"></span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const productPrice = {{ $product->price }};
        const maxStock = {{ $product->stock }};

        function changeQuantity(change) {
            const quantityInput = document.getElementById('quantity');
            let currentQuantity = parseInt(quantityInput.value);
            let newQuantity = currentQuantity + change;

            // Batasi quantity antara 1 dan stok tersedia
            if (newQuantity >= 1 && newQuantity <= maxStock) {
                quantityInput.value = newQuantity;
                updateTotal(newQuantity);
            }
        }

        function updateTotal(quantity) {
            const total = productPrice * quantity;
            document.getElementById('displayQuantity').textContent = quantity;
            document.getElementById('totalPrice').textContent = 'Rp ' + total.toLocaleString('id-ID');
        }

        // Handle form submit
        document.getElementById('checkoutForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            const btnText = document.getElementById('btnText');
            const btnLoading = document.getElementById('btnLoading');
            
            submitBtn.disabled = true;
            btnText.textContent = 'Memproses...';
            btnLoading.style.display = 'inline-block';
        });

        // Update quantity saat input manual
        document.getElementById('quantity').addEventListener('change', function() {
            let value = parseInt(this.value);
            if (value < 1) value = 1;
            if (value > maxStock) value = maxStock;
            this.value = value;
            updateTotal(value);
        });
    </script>
</body>
</html>
