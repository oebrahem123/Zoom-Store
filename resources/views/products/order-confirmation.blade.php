@extends('layouts.master')

@section('content')

<style>
    :root {
        --orange-color: #f28123;
    }

    .btn-outline-custom-orange {
        color: var(--orange-color);
        border: 1px solid var(--orange-color);
        background-color: transparent;
        border-radius: 7px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        display: inline-flex;
        align-items: center;
    }

    .btn-outline-custom-orange .fas {
        margin-left: 8px;
        font-size: 1rem;
    }

    .btn-outline-custom-orange:hover,
    .btn-outline-custom-orange:focus {
        box-shadow: 0 2px 6px rgba(73, 59, 55, 0.6);
        transform: translateY(-3px);
        color: var(--orange-color);
        border-color: var(--orange-color);
        background-color: transparent;
    }

    .order-status-page {
        direction: rtl;
        text-align: right;
        font-family: "Tajawal", sans-serif;
        padding-top: 95px;
    }

    .order-number-title {
        font-weight: 700;
        font-size: 26px;
        margin-bottom: 10px;
    }

    .detail-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 20px;
        background: #fff;
    }

    .detail-card h5 { font-weight: 700; margin-bottom: 16px; }

    .info-label { font-size: 13px; color: #888; margin-bottom: 2px; }
    .info-value { font-size: 16px; font-weight: 500; margin-bottom: 12px; }

    .totals-table { width: auto; margin-right: auto; }
    .totals-table td { padding: 6px 16px; font-size: 15px; }
    .totals-table .grand-total td { font-weight: 700; font-size: 18px; color: #f28123; border-top: 2px solid #ddd; padding-top: 12px; }

    .badge-status { padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-processing { background: #cce5ff; color: #004085; }
    .badge-approved { background: #d4edda; color: #155724; }
    .badge-printing { background: #d1ecf1; color: #0c5460; }
    .badge-shipped { background: #d1ecf1; color: #0c5460; }
    .badge-delivered { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }

    .order-ref-header {
        background: #f9f9f9;
        padding: 8px 16px;
        border-radius: 8px;
        margin-bottom: 12px;
        font-size: 14px;
        font-weight: 600;
        color: #666;
    }
    .order-ref-header .ref-badge {
        font-size: 11px;
        font-weight: 400;
        color: #999;
        background: #eee;
        padding: 1px 8px;
        border-radius: 4px;
        margin-right: 8px;
    }

    .product-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 12px;
        background: #fff;
        display: flex;
        gap: 16px;
    }
    .product-card-image { width: 120px; min-width: 120px; position: relative; }
    .product-card-image img { width: 100%; height: 120px; border-radius: 8px; object-fit: cover; }
    .product-card-body { flex: 1; }
    .product-type-badge { display: inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight: 600; margin-bottom: 8px; }
    .badge-ready { background: #e9e9e9; color: #555; }
    .badge-custom { background: #e8d5f5; color: #6f42c1; }
    .product-card h4 { font-size: 16px; font-weight: 600; margin-bottom: 4px; }
    .product-sku { font-size: 12px; color: #888; margin-bottom: 6px; }
    .product-details { display: flex; gap: 20px; font-size: 13px; color: #555; margin-bottom: 6px; }
    .product-qty-row { display: flex; gap: 20px; font-size: 14px; font-weight: 500; margin-bottom: 6px; }
    .product-subtotal { color: #f28123; font-weight: 700; }
    .product-availability { font-size: 12px; padding: 4px 10px; border-radius: 6px; display: inline-block; margin-bottom: 6px; }
    .product-availability.unavailable { background: #ffeeba; color: #856404; }
    .design-preview-overlay {
        position: absolute; bottom: 4px; right: 4px;
        width: 50px; height: 50px;
        border: 2px solid #fff; border-radius: 6px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15); overflow: hidden;
    }
    .design-preview-overlay img { width: 100%; height: 100%; object-fit: cover; border-radius: 4px; }
    .design-status { margin-bottom: 8px; }
    .design-status-badge { display: inline-block; padding: 2px 12px; border-radius: 12px; font-size: 13px; font-weight: 600; }
    .design-pending { background: #fff3cd; color: #856404; }
    .design-approved { background: #d4edda; color: #155724; }
    .design-rejected { background: #f8d7da; color: #721c24; }
    .rejected-block {
        background: #fff5f5; border: 1px solid #fcc; border-radius: 8px;
        padding: 12px; margin-top: 10px;
    }
    .rejected-header { font-size: 15px; font-weight: 700; color: #c0392b; margin-bottom: 4px; }
    .rejected-reason { font-size: 13px; color: #666; margin-bottom: 4px; }
    .rejected-notes { font-size: 12px; color: #888; margin-bottom: 10px; font-style: italic; }
    .rejected-actions { display: flex; gap: 8px; }
    .rejected-actions .btn { border-radius: 6px; font-size: 13px; }
    .rejected-actions .btn:disabled { opacity: 0.5; cursor: not-allowed; }
</style>

<div class="container order-status-page pb-5">

    {{-- Header --}}
    <div class="row align-items-center mb-5">
        <div class="col-md-6">
            <h2 class="order-number-title">🎉 تم تأكيد طلبك رقم #{{ $order->id }}</h2>
            <small class="text-muted">
                وسيلة الدفع: <strong>الدفع عند الاستلام</strong> —
                بتاريخ {{ $order->created_at->format('d-m-Y h:i A') }}
            </small>
        </div>
        <div class="col-md-6 text-left">
            <a href="tel:000000000" class="btn btn-outline-custom-orange px-4 py-2">
                <i class="fas fa-phone-alt"></i>
                خدمة العملاء
            </a>
        </div>
    </div>

    @if(isset($shipment))

    {{-- 1. Shipment Summary --}}
    @include('shipments.partials.cards.shipment-summary', [
        'shipment' => $shipment,
        'status' => $status,
        'showDetails' => true,
        'totals' => $totals,
    ])

    {{-- 2. Reference Orders --}}
    <div class="detail-card">
        <h5>الطلبات المرجعية</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="info-label">أرقام الطلبات</div>
                <div class="info-value">
                    @foreach($shipment->orders as $o)
                    <span style="display:inline-block;background:#eee;padding:2px 10px;border-radius:4px;margin-left:4px;font-size:13px;">
                        #{{ $o->id }}
                    </span>
                    @endforeach
                </div>
            </div>
            <div class="col-md-6">
                <div class="info-label">تواريخ الطلبات</div>
                <div class="info-value">
                    @foreach($shipment->orders as $o)
                    <div style="font-size:14px;">#{{ $o->id }}: {{ $o->created_at->format('d-m-Y h:i A') }}</div>
                    @endforeach
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-md-6">
                <div class="info-label">وسيلة الدفع</div>
                <div class="info-value">الدفع عند الاستلام</div>
            </div>
            <div class="col-md-6">
                <div class="info-label">تكلفة الشحن</div>
                <div class="info-value">{{ number_format($totals['shipping_cost'], 2) }} ج.م</div>
            </div>
        </div>
    </div>

    {{-- 3. Shipment Status --}}
    @php
    $statusIcons = [
        'action_required' => 'fa-exclamation-triangle',
        'delivered' => 'fa-check-circle',
        'shipped' => 'fa-truck',
        'processing' => 'fa-cog',
        'pending' => 'fa-hourglass-half',
    ];
    $statusIcon = $statusIcons[$status['status']] ?? 'fa-circle';
    @endphp
    <div class="detail-card">
        <h5>حالة الشحنة الحالية</h5>
        <div class="text-center py-3">
            <span class="badge-status {{ $status['badge'] }}" style="font-size:22px;padding:10px 30px;">
                <i class="fa {{ $statusIcon }}"></i>
                {{ $status['label'] }}
            </span>
        </div>
    </div>

    {{-- 4. Shipment Status --}}
    @if(isset($groups) && $groups->isNotEmpty())
    @php $g = $groups->first(); @endphp
    <div class="detail-card">
        <h5>حالة الطلب</h5>
        <div class="text-center py-2">
            <span class="badge-status {{ $g['currentStatus']['class'] }}" style="font-size:18px;padding:8px 24px;">
                {{ $g['currentStatus']['label'] }}
            </span>
        </div>
    </div>
    @endif

    {{-- 5. Tracking Map --}}
    <div class="detail-card">
        <h5>موقع التوصيل</h5>
        <div id="shipment-map" style="height:350px; border:1px solid #ddd; border-radius:12px; overflow:hidden; box-shadow:0 2px 8px rgba(0,0,0,0.08);"></div>
    </div>

    {{-- 6. Shipping Information --}}
    <div class="detail-card">
        <h5>معلومات الشحن</h5>
        <div class="row">
            <div class="col-md-6">
                <div class="info-label">الاسم</div>
                <div class="info-value">{{ $shippingInfo['name'] }}</div>
                <div class="info-label">رقم الهاتف</div>
                <div class="info-value">{{ $shippingInfo['phone'] }}</div>
            </div>
            <div class="col-md-6">
                <div class="info-label">العنوان</div>
                <div class="info-value">{{ $shippingInfo['address'] }}</div>
                @if($shippingInfo['notes'])
                <div class="info-label">ملاحظات</div>
                <div class="info-value">{{ $shippingInfo['notes'] }}</div>
                @endif
            </div>
        </div>
    </div>

    {{-- 7. Products --}}
    <div class="detail-card">
        <h5>المنتجات</h5>
        @include('shipments.partials.products-section', compact('groups'))
    </div>

    {{-- 8. Totals --}}
    <div class="detail-card">
        <h5>إجماليات الشحنة</h5>
        <table class="table totals-table">
            <tr>
                <td>الإجمالي الفرعي</td>
                <td>{{ number_format($totals['subtotal'], 2) }} ج.م</td>
            </tr>
            <tr>
                <td>تكلفة الشحن</td>
                <td>{{ number_format($totals['shipping_cost'], 2) }} ج.م</td>
            </tr>
            @if($totals['shipping_saved'] > 0)
            <tr class="text-success">
                <td>توفير شحن</td>
                <td>- {{ number_format($totals['shipping_saved'], 2) }} ج.م</td>
            </tr>
            @endif
            <tr class="grand-total">
                <td>الإجمالي الكلي</td>
                <td>{{ number_format($totals['grand_total'], 2) }} ج.م</td>
            </tr>
        </table>
        <div class="mt-2" style="font-size:14px;color:#888;">
            <i class="fa fa-credit-card"></i> وسيلة الدفع: الدفع عند الاستلام
        </div>
    </div>

    {{-- 9. Customer Actions --}}
    <div class="text-center mt-4 mb-2">
        <a href="/" class="btn btn-outline-secondary btn-lg px-5 py-3" style="border-radius:12px;font-weight:600;">
            <i class="fas fa-shopping-cart"></i> متابعة التسوق
        </a>
    </div>

    @else
    {{-- Legacy standalone order confirmation --}}
    <div class="text-center my-5 py-4">
        <a href="/" class="btn btn-outline-secondary btn-lg px-5 py-3" style="border-radius:12px;font-weight:600;">
            <i class="fas fa-shopping-cart"></i> متابعة التسوق
        </a>
    </div>
    @endif

</div>

@if(isset($shipment))
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var address = @json($trackingData['address']);
    if (!address) return;
    var map = L.map('shipment-map', {
        center: [30.0444, 31.2357],
        zoom: 12,
        zoomControl: false,
        attributionControl: false
    });
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
    fetch('https://nominatim.openstreetmap.org/search?format=json&q=' + encodeURIComponent(address))
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.length > 0) {
                map.setView([data[0].lat, data[0].lon], 15);
                L.marker([data[0].lat, data[0].lon]).addTo(map).bindPopup('موقع التوصيل').openPopup();
            }
        })
        .catch(function() {});
});
</script>
@endif

@endsection
