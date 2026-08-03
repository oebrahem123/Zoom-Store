@php
$img = $detail->displayImagePath();
@endphp
<div class="product-card">
    <div class="product-card-image">
        <img src="{{ $img ? asset($img) : asset('images/default.png') }}" alt="{{ $detail->displayName() }}">
    </div>
    <div class="product-card-body">
        <div class="product-type-badge badge-ready">📦 منتج جاهز</div>
        <h4>{{ $detail->displayName() }}</h4>
        @if($detail->product && $detail->product->sku)
        <div class="product-sku">SKU: {{ $detail->product->sku }}</div>
        @endif
        @if($detail->catalogStatusMessage())
        <div class="product-availability unavailable">{{ $detail->catalogStatusMessage() }}</div>
        @endif
        <div class="product-details">
            <span><strong>المقاس:</strong> {{ $detail->size ?? '—' }}</span>
            <span><strong>اللون:</strong> {{ $detail->color ?? '—' }}</span>
        </div>
        <div class="product-qty-row">
            <span>{{ $detail->quantity }} × {{ number_format($detail->price, 2) }} ج.م</span>
            <span class="product-subtotal">{{ number_format($detail->lineTotal(), 2) }} ج.م</span>
        </div>
    </div>
</div>
