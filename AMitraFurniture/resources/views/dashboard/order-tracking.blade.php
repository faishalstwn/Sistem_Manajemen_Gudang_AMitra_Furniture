<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tracking Pesanan - A Mitra Furniture</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .tracking-timeline {
            position: relative;
            padding: 20px 0;
        }
        
        .tracking-item {
            position: relative;
            padding-left: 60px;
            padding-bottom: 40px;
        }
        
        .tracking-item:last-child {
            padding-bottom: 0;
        }
        
        .tracking-item::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 40px;
            bottom: -20px;
            width: 2px;
            background: #e0e0e0;
        }
        
        .tracking-item:last-child::before {
            display: none;
        }
        
        .tracking-item.active::before {
            background: #429ff7;
        }
        
        .tracking-icon {
            position: absolute;
            left: 0;
            top: 0;
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e0e0e0;
            color: #666;
            font-size: 18px;
            z-index: 1;
        }
        
        .tracking-item.active .tracking-icon {
            background: #429ff7;
            color: white;
            box-shadow: 0 0 0 4px rgba(66, 159, 247, 0.2);
        }
        
        .tracking-item.completed .tracking-icon {
            background: #28a745;
            color: white;
        }
        
        .tracking-content {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 10px;
            border-left: 3px solid #e0e0e0;
        }
        
        .tracking-item.active .tracking-content {
            background: #e7f3ff;
            border-left-color: #429ff7;
        }
        
        .tracking-item.completed .tracking-content {
            border-left-color: #28a745;
        }
        
        .order-info-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 30px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
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
            <div class="d-flex">
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-sm me-2">
                    <i class="fas fa-arrow-left me-1"></i>Detail Pesanan
                </a>
                <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="fas fa-list me-1"></i>Riwayat
                </a>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Order Info Card -->
                <div class="order-info-card">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h4 class="mb-1"><i class="fas fa-box me-2"></i>Tracking Pesanan</h4>
                            <p class="mb-2 opacity-75">{{ $order->order_code }}</p>
                            <small class="opacity-75">
                                <i class="far fa-calendar me-1"></i>
                                {{ $order->created_at->format('d M Y, H:i') }}
                            </small>
                        </div>
                        <div class="text-end">
                            @if($order->status === 'pending')
                                <span class="status-badge bg-warning text-dark">
                                    <i class="fas fa-clock me-1"></i>Menunggu
                                </span>
                            @elseif($order->status === 'processing')
                                <span class="status-badge bg-info">
                                    <i class="fas fa-cog fa-spin me-1"></i>Diproses
                                </span>
                            @elseif($order->status === 'shipped')
                                <span class="status-badge bg-primary">
                                    <i class="fas fa-truck me-1"></i>Dikirim
                                </span>
                            @elseif($order->status === 'delivered')
                                <span class="status-badge bg-success">
                                    <i class="fas fa-check-circle me-1"></i>Selesai
                                </span>
                            @else
                                <span class="status-badge bg-danger">
                                    <i class="fas fa-times me-1"></i>Dibatalkan
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if($order->tracking_number)
                    <div class="mt-3 pt-3 border-top border-light border-opacity-25">
                        <small class="opacity-75 d-block mb-1">Nomor Resi:</small>
                        <h5 class="mb-0">{{ $order->tracking_number }}</h5>
                    </div>
                    @endif
                    
                    @if($order->estimated_delivery && in_array($order->status, ['processing', 'shipped']))
                    <div class="mt-3 pt-3 border-top border-light border-opacity-25">
                        <small class="opacity-75 d-block mb-1">Estimasi Tiba:</small>
                        <h5 class="mb-0">
                            <i class="far fa-calendar-check me-2"></i>
                            {{ \Carbon\Carbon::parse($order->estimated_delivery)->format('d M Y') }}
                        </h5>
                    </div>
                    @endif
                </div>

                <!-- Tracking Timeline -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h5 class="mb-4"><i class="fas fa-route me-2 text-primary"></i>Status Pengiriman</h5>
                        
                        <div class="tracking-timeline">
                            <!-- Step 1: Order Placed -->
                            <div class="tracking-item completed">
                                <div class="tracking-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="tracking-content">
                                    <h6 class="mb-1">Pesanan Dibuat</h6>
                                    <p class="text-muted mb-1 small">
                                        Pesanan Anda telah berhasil dibuat
                                    </p>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        {{ $order->created_at->format('d M Y, H:i') }}
                                    </small>
                                </div>
                            </div>

                            <!-- Step 2: Payment Confirmed -->
                            <div class="tracking-item {{ in_array($order->payment_status, ['paid']) ? 'completed' : (in_array($order->status, ['pending']) && $order->payment_status === 'pending' ? 'active' : '') }}">
                                <div class="tracking-icon">
                                    @if($order->payment_status === 'paid')
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-money-bill-wave"></i>
                                    @endif
                                </div>
                                <div class="tracking-content">
                                    <h6 class="mb-1">Pembayaran Dikonfirmasi</h6>
                                    @if($order->payment_status === 'paid')
                                        <p class="text-muted mb-1 small">
                                            Pembayaran Anda telah diterima
                                        </p>
                                        @if($order->confirmed_at)
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->confirmed_at->format('d M Y, H:i') }}
                                        </small>
                                        @endif
                                    @else
                                        <p class="text-muted mb-1 small">
                                            Menunggu konfirmasi pembayaran
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 3: Order Processing -->
                            <div class="tracking-item {{ $order->status === 'processing' ? 'active' : (in_array($order->status, ['shipped', 'delivered']) ? 'completed' : '') }}">
                                <div class="tracking-icon">
                                    @if(in_array($order->status, ['shipped', 'delivered']))
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-box"></i>
                                    @endif
                                </div>
                                <div class="tracking-content">
                                    <h6 class="mb-1">Pesanan Diproses</h6>
                                    @if($order->status === 'processing')
                                        <p class="text-muted mb-1 small">
                                            Pesanan Anda sedang dikemas
                                        </p>
                                        @if($order->confirmed_at)
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->confirmed_at->format('d M Y, H:i') }}
                                        </small>
                                        @endif
                                    @elseif(in_array($order->status, ['shipped', 'delivered']))
                                        <p class="text-muted mb-1 small">
                                            Pesanan telah dikemas
                                        </p>
                                        @if($order->confirmed_at)
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->confirmed_at->format('d M Y, H:i') }}
                                        </small>
                                        @endif
                                    @else
                                        <p class="text-muted mb-1 small">
                                            Menunggu proses pengemasan
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 4: Order Shipped -->
                            <div class="tracking-item {{ $order->status === 'shipped' ? 'active' : ($order->status === 'delivered' ? 'completed' : '') }}">
                                <div class="tracking-icon">
                                    @if($order->status === 'delivered')
                                        <i class="fas fa-check"></i>
                                    @else
                                        <i class="fas fa-truck"></i>
                                    @endif
                                </div>
                                <div class="tracking-content">
                                    <h6 class="mb-1">Pesanan Dalam Pengiriman</h6>
                                    @if($order->status === 'shipped')
                                        <p class="text-muted mb-1 small">
                                            Pesanan sedang dalam perjalanan
                                        </p>
                                        @if($order->shipped_at)
                                        <small class="text-muted d-block">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->shipped_at->format('d M Y, H:i') }}
                                        </small>
                                        @endif
                                        @if($order->tracking_number)
                                        <small class="text-primary d-block mt-1">
                                            <i class="fas fa-barcode me-1"></i>
                                            Resi: {{ $order->tracking_number }}
                                        </small>
                                        @endif
                                    @elseif($order->status === 'delivered')
                                        <p class="text-muted mb-1 small">
                                            Pesanan telah dikirim
                                        </p>
                                        @if($order->shipped_at)
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->shipped_at->format('d M Y, H:i') }}
                                        </small>
                                        @endif
                                    @else
                                        <p class="text-muted mb-1 small">
                                            Menunggu pengiriman
                                        </p>
                                    @endif
                                </div>
                            </div>

                            <!-- Step 5: Order Delivered -->
                            <div class="tracking-item {{ $order->status === 'delivered' ? 'completed active' : '' }}">
                                <div class="tracking-icon">
                                    <i class="fas fa-home"></i>
                                </div>
                                <div class="tracking-content">
                                    <h6 class="mb-1">Pesanan Diterima</h6>
                                    @if($order->status === 'delivered')
                                        <p class="text-success mb-1 small fw-bold">
                                            <i class="fas fa-check-circle me-1"></i>
                                            Pesanan telah sampai di tujuan
                                        </p>
                                        @if($order->delivered_at)
                                        <small class="text-muted">
                                            <i class="far fa-clock me-1"></i>
                                            {{ $order->delivered_at->format('d M Y, H:i') }}
                                        </small>
                                        @endif
                                    @else
                                        <p class="text-muted mb-1 small">
                                            Menunggu penerimaan pesanan
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        @if($order->tracking_notes)
                        <div class="alert alert-info mt-4">
                            <h6 class="alert-heading"><i class="fas fa-info-circle me-2"></i>Catatan Pengiriman</h6>
                            <p class="mb-0">{{ $order->tracking_notes }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Shipping Details -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-map-marker-alt me-2 text-danger"></i>Alamat Pengiriman</h5>
                        <p class="mb-2"><strong>{{ $order->user->name }}</strong></p>
                        <p class="mb-2 text-muted">
                            <i class="fas fa-phone me-2"></i>{{ $order->nomor_telepon }}
                        </p>
                        <p class="mb-0 text-muted">
                            <i class="fas fa-map-marked-alt me-2"></i>{{ $order->alamat }}
                        </p>
                    </div>
                </div>

                <!-- Order Items Summary -->
                <div class="card shadow-sm mt-4">
                    <div class="card-body">
                        <h5 class="mb-3"><i class="fas fa-shopping-bag me-2 text-primary"></i>Produk yang Dibeli</h5>
                        @foreach($order->orderItems as $item)
                        <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                            <div class="d-flex align-items-center">
                                @if($item->product && $item->product->image)
                                <img src="{{ asset($item->product->image) }}" 
                                     alt="{{ $item->product->name }}" 
                                     class="rounded me-3" 
                                     style="width: 60px; height: 60px; object-fit: cover;">
                                @endif
                                <div>
                                    <h6 class="mb-1">{{ $item->product->name ?? 'Product Deleted' }}</h6>
                                    <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                                </div>
                            </div>
                            <div>
                                <strong>Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</strong>
                            </div>
                        </div>
                        @endforeach
                        
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
                            <h5 class="mb-0">Total Pembayaran:</h5>
                            <h4 class="text-primary mb-0">Rp {{ number_format($order->total_price, 0, ',', '.') }}</h4>
                        </div>
                    </div>
                </div>

                <!-- Help Section -->
                <div class="text-center mt-4">
                    <p class="text-muted">Ada pertanyaan tentang pesanan Anda?</p>
                    <a href="{{ route('home') }}" class="btn btn-outline-primary">
                        <i class="fas fa-home me-2"></i>Kembali ke Home
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
