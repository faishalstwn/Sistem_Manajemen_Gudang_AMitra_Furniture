<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan - A Mitra Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg app-header sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">A Mitra Furniture</a>
            <div class="d-flex">
                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-arrow-left me-1"></i>Kembali ke Riwayat
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="text-center mb-4">
                    <i class="fas fa-file-invoice fa-4x mb-3" style="color: #429ff7;"></i>
                    <h2>Detail Pesanan</h2>
                </div>

                {{-- Order Info Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Informasi Pesanan</h5>
                            @if($order->payment_status === 'pending')
                                <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Menunggu Pembayaran</span>
                            @elseif($order->payment_status === 'paid')
                                <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Sudah Dibayar</span>
                            @else
                                <span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Gagal</span>
                            @endif
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Kode Pesanan:</strong></p>
                                <p class="text-muted">{{ $order->order_code }}</p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Tanggal Pesanan:</strong></p>
                                <p class="text-muted">{{ $order->created_at->format('d F Y, H:i') }}</p>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Status Pembayaran:</strong></p>
                                @if($order->payment_status === 'pending')
                                    <span class="badge bg-warning text-dark"><i class="fas fa-hourglass-half me-1"></i>Menunggu Pembayaran</span>
                                @elseif($order->payment_status === 'paid')
                                    <span class="badge bg-success"><i class="fas fa-check-double me-1"></i>Lunas</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Gagal</span>
                                @endif
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2"><strong>Status Pesanan:</strong></p>
                                @if($order->status === 'pending')
                                    <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i>Menunggu Konfirmasi</span>
                                @elseif($order->status === 'processing')
                                    <span class="badge bg-info"><i class="fas fa-cog fa-spin me-1"></i>Sedang Diproses</span>
                                @elseif($order->status === 'shipped')
                                    <span class="badge bg-primary"><i class="fas fa-truck me-1"></i>Dalam Pengiriman</span>
                                @elseif($order->status === 'delivered')
                                    <span class="badge bg-success"><i class="fas fa-box-open me-1"></i>Selesai</span>
                                @else
                                    <span class="badge bg-danger"><i class="fas fa-ban me-1"></i>Dibatalkan</span>
                                @endif
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <p class="mb-2"><strong>Metode Pembayaran:</strong></p>
                                @if($order->payment_method === 'cod')
                                    <p class="text-muted"><i class="fas fa-money-bill-wave text-success me-2"></i>Cash on Delivery (COD)</p>
                                @elseif($order->payment_method === 'transfer_bca')
                                    <p class="text-muted"><i class="fas fa-university text-primary me-2"></i>Transfer Bank BCA</p>
                                @elseif($order->payment_method === 'transfer_bri')
                                    <p class="text-muted"><i class="fas fa-university text-info me-2"></i>Transfer Bank BRI</p>
                                @elseif($order->payment_method === 'transfer_mandiri')
                                    <p class="text-muted"><i class="fas fa-university text-warning me-2"></i>Transfer Bank Mandiri</p>
                                @else
                                    <p class="text-muted"><i class="fas fa-credit-card text-info me-2"></i>{{ ucfirst($order->payment_method) }}</p>
                                @endif
                            </div>
                        </div>

                        {{-- Deadline Pembayaran --}}
                        @if($order->payment_deadline && $order->payment_status === 'pending')
                        <div class="alert alert-warning mt-3">
                            <h6 class="alert-heading"><i class="fas fa-clock me-2"></i>Batas Waktu Pembayaran</h6>
                            <hr>
                            <p class="mb-0">Silakan selesaikan pembayaran sebelum:</p>
                            <h5 class="text-danger mb-0 mt-2">
                                <i class="far fa-calendar-alt me-2"></i>{{ \Carbon\Carbon::parse($order->payment_deadline)->format('d F Y, H:i') }} WIB
                            </h5>
                            <small class="text-muted d-block mt-2">
                                @php
                                    $deadline = \Carbon\Carbon::parse($order->payment_deadline);
                                    $now = now();
                                    $diff = $deadline->diff($now);
                                @endphp
                                @if($deadline > $now)
                                    Sisa waktu: {{ $diff->h }} jam {{ $diff->i }} menit
                                @else
                                    <span class="text-danger fw-bold">Waktu pembayaran telah habis</span>
                                @endif
                            </small>
                        </div>
                        @endif

                        {{-- Informasi Pembayaran untuk Transfer Bank BCA --}}
                        @if($order->payment_method === 'transfer_bca' && $order->payment_status === 'pending')
                        <div class="alert alert-info mt-3">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Pembayaran Transfer BCA</h6>
                            <hr>
                            <p class="mb-2"><strong>Transfer ke rekening:</strong></p>
                            <p class="mb-1">Bank: <strong>BCA (Bank Central Asia)</strong></p>
                            <p class="mb-1">No. Rekening: <strong>1234567890</strong></p>
                            <p class="mb-1">Atas Nama: <strong>A Mitra Furniture</strong></p>
                            <p class="mb-3">Jumlah Transfer: <strong class="text-danger">Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
                            <small class="text-muted">Setelah transfer, klik tombol "Konfirmasi Pembayaran" di bawah</small>
                        </div>
                        @endif

                        {{-- Informasi Pembayaran untuk Transfer Bank BRI --}}
                        @if($order->payment_method === 'transfer_bri' && $order->payment_status === 'pending')
                        <div class="alert alert-info mt-3">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Pembayaran Transfer BRI</h6>
                            <hr>
                            <p class="mb-2"><strong>Transfer ke rekening:</strong></p>
                            <p class="mb-1">Bank: <strong>BRI (Bank Rakyat Indonesia)</strong></p>
                            <p class="mb-1">No. Rekening: <strong>9876543210</strong></p>
                            <p class="mb-1">Atas Nama: <strong>A Mitra Furniture</strong></p>
                            <p class="mb-3">Jumlah Transfer: <strong class="text-danger">Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
                            <small class="text-muted">Setelah transfer, klik tombol "Konfirmasi Pembayaran" di bawah</small>
                        </div>
                        @endif

                        {{-- Informasi Pembayaran untuk Transfer Bank Mandiri --}}
                        @if($order->payment_method === 'transfer_mandiri' && $order->payment_status === 'pending')
                        <div class="alert alert-info mt-3">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Informasi Pembayaran Transfer Mandiri</h6>
                            <hr>
                            <p class="mb-2"><strong>Transfer ke rekening:</strong></p>
                            <p class="mb-1">Bank: <strong>Mandiri (Bank Mandiri)</strong></p>
                            <p class="mb-1">No. Rekening: <strong>5555666677</strong></p>
                            <p class="mb-1">Atas Nama: <strong>A Mitra Furniture</strong></p>
                            <p class="mb-3">Jumlah Transfer: <strong class="text-danger">Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
                            <small class="text-muted">Setelah transfer, klik tombol "Konfirmasi Pembayaran" di bawah</small>
                        </div>
                        @endif

                        {{-- Informasi COD --}}
                        @if($order->payment_method === 'cod')
                        <div class="alert alert-success mt-3">
                            <h6 class="alert-heading"><i class="fas fa-check-circle me-2"></i>Cash on Delivery (COD)</h6>
                            <hr>
                            <p class="mb-0">Pembayaran akan dilakukan saat barang diterima.</p>
                            <p class="mb-0"><strong>Total yang harus dibayar: Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong></p>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Shipping Info Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast me-2"></i>Informasi Pengiriman</h5>
                    </div>
                    <div class="card-body">
                        <p class="mb-2"><strong>Alamat:</strong></p>
                        <p class="text-muted">{{ $order->alamat ?? '-' }}</p>
                        
                        <p class="mb-2 mt-3"><strong>Nomor Telepon:</strong></p>
                        <p class="text-muted"><i class="fas fa-phone me-2"></i>{{ $order->nomor_telepon ?? '-' }}</p>
                    </div>
                </div>

                {{-- Order Items Card --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i>Produk yang Dibeli</h5>
                    </div>
                    <div class="card-body">
                        @if($order->orderItems && $order->orderItems->count() > 0)
                            @foreach($order->orderItems as $item)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($item->product && $item->product->image)
                                                <img src="{{ asset($item->product->image) }}" 
                                                     alt="{{ $item->product->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 80px;">
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $item->product->name ?? 'Product Deleted' }}</h6>
                                            <p class="text-muted mb-0">
                                                Qty: {{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            {{-- Fallback untuk old order structure --}}
                            @if($order->product)
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="me-3">
                                            @if($order->product->image)
                                                <img src="{{ asset($order->product->image) }}" 
                                                     alt="{{ $order->product->name }}" 
                                                     class="img-thumbnail" 
                                                     style="width: 80px; height: 80px; object-fit: cover;">
                                            @else
                                                <div class="bg-light d-flex align-items-center justify-content-center" 
                                                     style="width: 80px; height: 80px;">
                                                    <i class="fas fa-image fa-2x text-muted"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div>
                                            <h6 class="mb-1">{{ $order->product->name }}</h6>
                                            <p class="text-muted mb-0">Qty: {{ $order->quantity ?? 1 }}</p>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <strong>Rp {{ number_format($order->product->price * ($order->quantity ?? 1), 0, ',', '.') }}</strong>
                                    </div>
                                </div>
                            @endif
                        @endif

                        <hr>

                        {{-- Total --}}
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Total Pembayaran:</h5>
                            <h4 class="text-primary mb-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                {{-- Review Section (Only for delivered orders) --}}
                @if($order->status === 'delivered' && $order->orderItems && $order->orderItems->count() > 0)
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-light">
                        <h5 class="mb-0"><i class="fas fa-star me-2 text-warning"></i>Ulasan Produk</h5>
                    </div>
                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif
                        
                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <p class="text-muted mb-4">Bagaimana pengalaman Anda dengan produk yang telah diterima? Berikan ulasan untuk membantu pembeli lain!</p>

                        @foreach($order->orderItems as $item)
                            @php
                                // Check if user already reviewed this product for this order
                                $existingReview = \App\Models\Review::where([
                                    'user_id' => Auth::id(),
                                    'product_id' => $item->product_id,
                                    'order_id' => $order->id,
                                ])->first();
                            @endphp

                            <div class="border rounded p-3 mb-3 {{ $existingReview ? 'bg-light' : '' }}">
                                <div class="d-flex align-items-center mb-3">
                                    @if($item->product && $item->product->image)
                                        <img src="{{ asset($item->product->image) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="rounded me-3" 
                                             style="width: 60px; height: 60px; object-fit: cover;">
                                    @endif
                                    <div>
                                        <h6 class="mb-0">{{ $item->product->name ?? 'Product Deleted' }}</h6>
                                        <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                    </div>
                                </div>

                                @if($existingReview)
                                    <div class="alert alert-success mb-0">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <strong><i class="fas fa-check-circle me-2"></i>Ulasan Anda:</strong>
                                                <div class="mt-2">
                                                    <div class="mb-2">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            <i class="fas fa-star {{ $i <= $existingReview->rating ? 'text-warning' : 'text-muted' }}"></i>
                                                        @endfor
                                                        <span class="ms-2 text-muted">({{ $existingReview->rating }}/5)</span>
                                                    </div>
                                                    @if($existingReview->comment)
                                                        <p class="mb-0 text-dark">{{ $existingReview->comment }}</p>
                                                    @endif
                                                    <small class="text-muted">
                                                        <i class="far fa-clock me-1"></i>
                                                        {{ $existingReview->created_at->format('d M Y, H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <form action="{{ route('reviews.store') }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        
                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Rating <span class="text-danger">*</span></label>
                                            <div class="star-rating" data-product-id="{{ $item->product_id }}">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <i class="far fa-star star-icon" data-rating="{{ $i }}" style="font-size: 24px; cursor: pointer; color: #ffc107;"></i>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="rating-{{ $item->product_id }}" required>
                                            @error('rating')
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label fw-bold">Komentar (Opsional)</label>
                                            <textarea class="form-control @error('comment') is-invalid @enderror" 
                                                      name="comment" 
                                                      rows="3" 
                                                      placeholder="Ceritakan pengalaman Anda dengan produk ini..."></textarea>
                                            @error('comment')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>

                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Kirim Ulasan
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                {{-- Action Buttons --}}
                <div class="text-center mb-3">
                    <a href="{{ route('orders.track', $order->id) }}" class="btn btn-primary btn-lg px-5">
                        <i class="fas fa-map-marked-alt me-2"></i>Lacak Pesanan
                    </a>
                </div>

                @if($order->payment_status === 'pending' && $order->payment_method !== 'cod')
                    <div class="text-center">
                        <form action="{{ route('orders.confirmPayment', $order->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin sudah melakukan pembayaran?')">
                            @csrf
                            <button type="submit" class="btn btn-success btn-lg px-5">
                                <i class="fas fa-check-circle me-2"></i>Konfirmasi Pembayaran
                            </button>
                            <p class="text-muted mt-2 small">Klik tombol ini setelah Anda melakukan transfer</p>
                        </form>
                    </div>
                @elseif($order->payment_status === 'paid' && $order->status === 'processing')
                    <div class="alert alert-info text-center">
                        <i class="fas fa-box me-2"></i>
                        <strong>Pesanan Sedang Diproses</strong>
                        <p class="mb-0 mt-2">Pembayaran Anda sudah diterima. Pesanan akan segera dikirim.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Star Rating Functionality
        document.querySelectorAll('.star-rating').forEach(function(ratingContainer) {
            const stars = ratingContainer.querySelectorAll('.star-icon');
            const productId = ratingContainer.getAttribute('data-product-id');
            const ratingInput = document.getElementById('rating-' + productId);
            
            stars.forEach(function(star, index) {
                // Hover effect
                star.addEventListener('mouseenter', function() {
                    highlightStars(stars, index);
                });
                
                // Click to select rating
                star.addEventListener('click', function() {
                    const rating = star.getAttribute('data-rating');
                    ratingInput.value = rating;
                    stars.forEach(function(s, i) {
                        if (i < rating) {
                            s.classList.remove('far');
                            s.classList.add('fas');
                        } else {
                            s.classList.remove('fas');
                            s.classList.add('far');
                        }
                    });
                });
            });
            
            // Reset on mouse leave
            ratingContainer.addEventListener('mouseleave', function() {
                const currentRating = ratingInput.value;
                if (currentRating) {
                    highlightStars(stars, currentRating - 1);
                } else {
                    resetStars(stars);
                }
            });
        });
        
        function highlightStars(stars, upToIndex) {
            stars.forEach(function(star, index) {
                if (index <= upToIndex) {
                    star.classList.remove('far');
                    star.classList.add('fas');
                } else {
                    star.classList.remove('fas');
                    star.classList.add('far');
                }
            });
        }
        
        function resetStars(stars) {
            stars.forEach(function(star) {
                star.classList.remove('fas');
                star.classList.add('far');
            });
        }
    </script>
</body>
</html>
