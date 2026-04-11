<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Berhasil - A Mitra Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('CSS/style.css') }}">
    <style>
        .success-animation {
            animation: scaleIn 0.5s ease-out;
        }
        
        @keyframes scaleIn {
            0% {
                transform: scale(0);
                opacity: 0;
            }
            50% {
                transform: scale(1.1);
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }
        
        .success-checkmark {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            color: white;
            font-size: 3rem;
            margin: 0 auto 2rem;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.3);
        }
    </style>
</head>
<body>
    <!-- Header/Navbar -->
    <nav class="navbar navbar-expand-lg app-header sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="{{ route('home') }}">A Mitra Furniture</a>
        </div>
    </nav>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="text-center success-animation">
                    <!-- Success Icon -->
                    <div class="success-checkmark">
                        <i class="fas fa-check"></i>
                    </div>
                    
                    <h2 class="mb-3 text-success">Pembayaran Berhasil!</h2>
                    <p class="text-muted mb-5">Terima kasih atas pembelian Anda. Pesanan sedang diproses.</p>
                </div>
                
                @if(isset($order))
                <!-- Order Summary Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-receipt me-2"></i>Ringkasan Pesanan</h5>
                    </div>
                    <div class="card-body">
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Nomor Pesanan:</strong>
                            </div>
                            <div class="col-6 text-end">
                                <span class="badge bg-primary">#{{ $order->order_code }}</span>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Status Pembayaran:</strong>
                            </div>
                            <div class="col-6 text-end">
                                @if($order->payment_status === 'paid')
                                    <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i>Lunas</span>
                                @elseif($order->payment_status === 'pending')
                                    <span class="badge bg-warning"><i class="fas fa-clock me-1"></i>Menunggu Konfirmasi</span>
                                @else
                                    <span class="badge bg-secondary">{{ ucfirst($order->payment_status) }}</span>
                                @endif
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-6">
                                <strong>Metode Pembayaran:</strong>
                            </div>
                            <div class="col-6 text-end">
                                @if($order->payment_method === 'midtrans')
                                    <span class="badge bg-info"><i class="fas fa-credit-card me-1"></i>Midtrans</span>
                                @elseif($order->payment_method === 'cod')
                                    <span class="badge bg-warning"><i class="fas fa-money-bill-wave me-1"></i>COD</span>
                                @else
                                    <span class="badge bg-primary">{{ strtoupper($order->payment_method) }}</span>
                                @endif
                            </div>
                        </div>

                        <hr>

                        <!-- Produk yang Dibeli -->
                        <h6 class="mb-3"><i class="fas fa-box me-2"></i>Produk yang Dibeli:</h6>
                        @if($order->orderItems && $order->orderItems->count() > 0)
                            @foreach($order->orderItems as $item)
                                <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                                    <div>
                                        <strong>{{ $item->product->name }}</strong>
                                        <p class="text-muted mb-0 small">Jumlah: {{ $item->quantity }} pcs</p>
                                    </div>
                                    <div class="text-end">
                                        <p class="mb-0 fw-bold">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            @endforeach
                        @endif

                        <hr>

                        <!-- Alamat Pengiriman -->
                        <h6 class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>Alamat Pengiriman:</h6>
                        <p class="mb-1">{{ $order->alamat ?? '-' }}</p>
                        <p class="mb-3"><i class="fas fa-phone me-2"></i>{{ $order->nomor_telepon ?? '-' }}</p>

                        <hr>

                        <!-- Total -->
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Total Pembayaran:</h5>
                            <h4 class="text-success mb-0 fw-bold">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Info Next Steps -->
                <div class="alert alert-info mb-4">
                    <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Langkah Selanjutnya</h6>
                    <ul class="mb-0 ps-3">
                        <li>Pesanan Anda akan segera diproses oleh tim kami</li>
                        <li>Anda akan menerima notifikasi ketika pesanan dikirim</li>
                        <li>Lacak status pesanan di halaman "Pesanan Saya"</li>
                    </ul>
                </div>
                @endif
                
                <!-- Action Buttons -->
                <div class="d-grid gap-2">
                    <a href="{{ route('orders.index') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-list-alt me-2"></i>Lihat Semua Pesanan
                    </a>
                    <a href="{{ route('home') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-home me-2"></i>Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation (jika ada) -->
    @auth
    <nav class="bottom-nav fixed-bottom">
        <div class="container">
            <div class="row">
                <div class="col-3 bottom-nav-item">
                    <a href="{{ route('home') }}" class="text-decoration-none text-dark">
                        <i class="fas fa-home"></i>
                        <div class="small">Home</div>
                    </a>
                </div>
                <div class="col-3 bottom-nav-item">
                    <a href="{{ route('cart.index') }}" class="text-decoration-none text-dark">
                        <i class="fas fa-shopping-cart"></i>
                        <div class="small">Cart</div>
                    </a>
                </div>
                <div class="col-3 bottom-nav-item">
                    <a href="{{ route('orders.index') }}" class="text-decoration-none text-success">
                        <i class="fas fa-receipt"></i>
                        <div class="small">Pesanan</div>
                    </a>
                </div>
                <div class="col-3 bottom-nav-item">
                    <a href="{{ route('profile.show') }}" class="text-decoration-none text-dark">
                        <i class="fas fa-user"></i>
                        <div class="small">Profile</div>
                    </a>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>