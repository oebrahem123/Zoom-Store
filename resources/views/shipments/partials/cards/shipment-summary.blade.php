@php
$allDetails = $shipment->orders->flatMap(fn($o) => $o->orderdetails);
$readyCount = $allDetails->whereNull('design_id')->count();
$customCount = $allDetails->whereNotNull('design_id')->count();
$totalCount = $allDetails->count();
$ordersCount = $shipment->orders->count();

$statusDisplay = [
    'action_required' => ['icon' => 'fa-exclamation-triangle', 'label' => 'يتطلب إجراء'],
    'delivered'       => ['icon' => 'fa-check-circle',      'label' => 'تم التوصيل'],
    'shipped'         => ['icon' => 'fa-truck',             'label' => 'تم الشحن'],
    'processing'      => ['icon' => 'fa-cog',               'label' => 'قيد التجهيز'],
    'pending'         => ['icon' => 'fa-hourglass-half',    'label' => 'قيد التنفيذ'],
];
$cur = $statusDisplay[$status['status']] ?? ['icon' => 'fa-circle', 'label' => $status['label']];
@endphp
<div class="detail-card">
    <div class="d-flex justify-content-between align-items-start mb-3">
        <div>
            <h3>شحنة رقم #{{ $shipment->id }}</h3>
            <small class="text-muted">
                بتاريخ {{ $shipment->created_at->format('d-m-Y h:i A') }}
            </small>
        </div>
        <div>
            <span class="badge-status {{ $status['badge'] }}" style="font-size:16px;padding:6px 20px;">
                <i class="fa {{ $cur['icon'] }}"></i> {{ $cur['label'] }}
            </span>
        </div>
    </div>

    <div class="mb-2">
        <span>{{ $totalCount }} عناصر</span>
        <span class="mx-2">•</span>
        <span>{{ $ordersCount }} طلبات</span>
        @if($readyCount > 0)
        <span class="mx-2">•</span>
        <span>{{ $readyCount }} منتجات جاهزة</span>
        @endif
        @if($customCount > 0)
        <span class="mx-2">•</span>
        <span>{{ $customCount }} تصاميم مخصصة</span>
        @endif
    </div>

    @if(isset($showDetails) && $showDetails && !empty($totals) && $totals['shipping_saved'] > 0)
    <div class="text-success mt-1" style="font-size:13px;">
        <i class="fa fa-check-circle"></i> تم توفير {{ number_format($totals['shipping_saved'], 2) }} جنيه بفضل دمج الشحن
    </div>
    @endif
</div>
