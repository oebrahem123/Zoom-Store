@extends('layouts.master')

@section('title', 'طلباتي')

@section('content')

<style>
:root { --orange-color: #f28123; }
.order-status-page { direction: rtl; text-align: right; font-family: "Tajawal", sans-serif; padding-top: 95px; }

.order-number-title { font-weight: 700; font-size: 26px; margin-bottom: 10px; }

.order-timeline-wrap { position: relative; padding-right: 30px; margin-top: 10px; }
.order-timeline-wrap::before {
    content: '';
    position: absolute;
    top: 0;
    right: 39px;
    width: 2px;
    height: 90%;
    background-color: #e0e0e0;
}
.timeline-item { position: relative; padding-right: 40px; margin-bottom: 45px; }
.timeline-dot {
    position: absolute; top: 0; right: -1px; width: 22px; height: 22px;
    border-radius: 50%; background-color: #cfcfcf;
    display: flex; align-items: center; justify-content: center;
    color: white; font-size: 11px; font-weight: bold;
    border: 2px solid #fff; box-shadow: 0 0 3px #888;
}
.timeline-item.completed .timeline-dot { background-color: #28a745; }
.timeline-item.active .timeline-dot { background-color: #ffc107; color: #333; }
.timeline-content h5 { font-size: 1.1rem; font-weight: bold; margin-bottom: 4px; }
.timeline-content p { font-size: 0.9rem; color: #666; margin: 0; }
.timeline-date { color: #777; font-size: 0.85rem; margin-top: 5px; }

.map-container { height: 400px; border: 1px solid #ddd; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 8px rgba(0,0,0,0.08); }

.shipment-section { margin-bottom: 60px; }

.badge-status { padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; }
.badge-pending { background: #fff3cd; color: #856404; }
.badge-approved { background: #d4edda; color: #155724; }
.badge-cancelled { background: #f8d7da; color: #721c24; }
.badge-processing { background: #cce5ff; color: #004085; }
.badge-printing { background: #e2d5f1; color: #6f42c1; }
.badge-shipped { background: #d1ecf1; color: #0c5460; }
.badge-delivered { background: #d4edda; color: #155724; }
.badge-action_required { background: #f8d7da; color: #721c24; }

.btn-outline-custom-orange {
    color: var(--orange-color); border: 1px solid var(--orange-color);
    background-color: transparent; border-radius: 7px;
    padding: 0.5rem 1rem; font-weight: 500; text-decoration: none;
    display: inline-flex; align-items: center; transition: all 0.3s;
}
.btn-outline-custom-orange:hover { box-shadow: 0 2px 6px rgba(73,59,55,0.6); transform: translateY(-3px); color: var(--orange-color); border-color: var(--orange-color); background-color: transparent; }
</style>

<div class="container order-status-page pb-5">

    @if($shipments->isEmpty())
    <div class="alert alert-info">لا توجد شحنات نشطة حالياً</div>
    @endif

    @php
    $prio = ['pending'=>0,'pending_review'=>1,'approved'=>2,'processing'=>3,'printing'=>4,'shipped'=>5,'delivered'=>6];
    @endphp

    @foreach($shipments as $shipment)
    @php
    $d = $prepared[$shipment->id];
    $status = $d['status'];
    $tl = $d['aggregatedTimeline'];
    $groups = $d['groups'];
    $totals = $d['totals'];
    $hasDesign = $groups->contains(fn($g) => $g['hasDesign']);
    $allSt = $groups->pluck('order')->pluck('status');
    $highest = $allSt->max(fn($s) => $prio[$s] ?? -1);

    $steps = $hasDesign
        ? [['key'=>'pending_review','title'=>'تم استلام الطلب','icon'=>'fa-clipboard-check'],['key'=>'approved','title'=>'اعتماد التصميم','icon'=>'fa-check-circle'],['key'=>'processing','title'=>'قيد التجهيز','icon'=>'fa-box-open'],['key'=>'printing','title'=>'جاري الطباعة','icon'=>'fa-print'],['key'=>'shipped','title'=>'تم الشحن','icon'=>'fa-truck'],['key'=>'delivered','title'=>'تم التوصيل','icon'=>'fa-home']]
        : [['key'=>'pending','title'=>'تم استلام الطلب','icon'=>'fa-clipboard-check'],['key'=>'processing','title'=>'قيد التجهيز','icon'=>'fa-box-open'],['key'=>'shipped','title'=>'تم الشحن','icon'=>'fa-truck'],['key'=>'delivered','title'=>'تم التوصيل','icon'=>'fa-home']];

    $completed = 0;
    foreach ($steps as $i => $st) {
        if (($prio[$st['key']] ?? -1) <= $highest) $completed = $i + 1;
    }
    $tlDates = [];
    if ($tl) { foreach ($tl as $e) { $tlDates[$e->to_status] = $e->created_at; } }
    @endphp

    <div class="shipment-section">
        {{-- Header --}}
        <div class="row align-items-center mb-4">
            <div class="col-md-6">
                <h2 class="order-number-title">شحنة رقم #{{ $shipment->id }}</h2>
                <small class="text-muted">
                    <span class="badge-status {{ $status['badge'] }}" style="font-size:15px;padding:6px 18px;">{{ $status['label'] }}</span>
                    —
                    {{ $shipment->created_at->format('d-m-Y h:i A') }}
                    —
                    {{ $totals['orders_count'] }} طلب • {{ $totals['products_count'] }} منتج
                </small>
            </div>
            <div class="col-md-6 text-left">
                <a href="tel:000000000" class="btn btn-outline-custom-orange px-4 py-2">
                    <i class="fas fa-phone-alt"></i> خدمة العملاء
                </a>
            </div>
        </div>

        {{-- Timeline + Map --}}
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="order-timeline-wrap">
                    @foreach($steps as $i => $st)
                    @php
                    $c = $i < $completed;
                    $cls = $c ? 'completed' : ($i === $completed ? 'active' : 'pending');
                    $desc = $c ? 'تم بنجاح' : ($i === $completed ? 'جارٍ التنفيذ حالياً' : '');
                    $isFirst = $i === 0;
                    $icon = $c ? 'fa-check' : ($i === $completed ? $st['icon'] : $st['icon']);
                    @endphp
                    <div class="timeline-item {{ $cls }}">
                        <div class="timeline-dot"><i class="fas {{ $icon }}"></i></div>
                        <div class="timeline-content">
                            <h5>{{ $st['title'] }}</h5>
                            <p>{{ $desc }}</p>
                            @if($isFirst && isset($shipment->created_at))
                            <div class="timeline-date">{{ $shipment->created_at->format('d-m-Y h:i A') }}</div>
                            @elseif(isset($tlDates[$st['key']]))
                            <div class="timeline-date">{{ $tlDates[$st['key']]->format('d-m-Y h:i A') }}</div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-6">
                <div id="map-{{ $shipment->id }}" class="map-container"></div>
            </div>
        </div>
    </div>
    @endforeach

</div>

<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

<script>
document.addEventListener("DOMContentLoaded", function() {
    var mapsData = @json($shipments->mapWithKeys(fn($s) => [$s->id => $prepared[$s->id]['trackingData']['address'] ?? '']));

    Object.keys(mapsData).forEach(function(id) {
        var address = mapsData[id];
        if (!address) return;
        var el = document.getElementById('map-' + id);
        if (!el) return;

        var map = L.map('map-' + id, {
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
                    L.marker([data[0].lat, data[0].lon]).addTo(map)
                        .bindPopup("موقع التوصيل")
                        .openPopup();
                }
            });
    });
});
</script>

@endsection
