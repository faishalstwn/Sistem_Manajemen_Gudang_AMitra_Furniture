<div class="order-item">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h6 class="mb-1">{{ $order->order_code }}</h6>
            <small class="text-muted">
                <i class="far fa-calendar me-1"></i>
                {{ $order->created_at->format('d M Y, H:i') }}
            </small>
        </div>
        <div class="text-end">
            @if($order->payment_status === 'pending')
                <span class="badge bg-warning text-dark">
                    <i class="fas fa-clock me-1"></i>Belum Bayar
                </span>
            @elseif($order->payment_status === 'paid')
                <span class="badge bg-success">
                    <i class="fas fa-check-circle me-1"></i>Sudah Dibayar
                </span>
            @else
                <span class="badge bg-danger">
                    <i class="fas fa-times-circle me-1"></i>Gagal
                </span>
            @endif
            
            @if($order->status === 'pending')
                <span class="badge bg-secondary ms-1">Pending</span>
            @elseif($order->status === 'processing')
                <span class="badge bg-info ms-1">
                    <i class="fas fa-cog fa-spin me-1"></i>Diproses
                </span>
            @elseif($order->status === 'shipped')
                <span class="badge bg-primary ms-1">
                    <i class="fas fa-shipping-fast me-1"></i>Dikirim
                </span>
            @elseif($order->status === 'delivered')
                <span class="badge bg-success ms-1">
                    <i class="fas fa-check-double me-1"></i>Selesai
                </span>
            @else
                <span class="badge bg-danger ms-1">Dibatalkan</span>
            @endif
        </div>
    </div>
    
    <div class="row">
        @foreach($order->orderItems->take(3) as $item)
            <div class="col-12 mb-2">
                <div class="d-flex align-items-center">
                    @if($item->product && $item->product->image)
                        <img src="{{ asset($item->product->image) }}" 
                             alt="{{ $item->product->name }}" 
                             class="rounded me-3" 
                             style="width: 50px; height: 50px; object-fit: cover;">
                    @else
                        <div class="bg-light rounded me-3 d-flex align-items-center justify-content-center" 
                             style="width: 50px; height: 50px;">
                            <i class="fas fa-image text-muted"></i>
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <p class="mb-0 small fw-bold">{{ $item->product->name ?? 'Product Deleted' }}</p>
                        <small class="text-muted">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</small>
                    </div>
                </div>
            </div>
        @endforeach
        
        @if($order->orderItems->count() > 3)
            <div class="col-12">
                <small class="text-muted">+{{ $order->orderItems->count() - 3 }} produk lainnya</small>
            </div>
        @endif
    </div>
    
    <hr>
    
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <strong class="text-primary">Total: Rp {{ number_format($order->total_price, 0, ',', '.') }}</strong>
        </div>
        <div>
            <a href="{{ route('orders.track', $order->id) }}" class="btn btn-sm btn-primary" title="Lacak Pesanan">
                <i class="fas fa-map-marked-alt me-1"></i>Lacak
            </a>
            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-outline-primary">
                <i class="fas fa-eye me-1"></i>Detail
            </a>
            
            @if($order->payment_status === 'pending' && $order->payment_method !== 'cod')
                <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-success">
                    <i class="fas fa-check-circle me-1"></i>Bayar
                </a>
            @endif
        </div>
    </div>
</div>
