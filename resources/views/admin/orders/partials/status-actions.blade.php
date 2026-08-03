@php
$nextStatuses = \App\Services\Order\OrderStatusService::nextStatuses($order);
$labels = [
    'pending_review' => 'قيد المراجعة',
    'approved' => 'اعتماد',
    'processing' => 'تجهيز',
    'printing' => 'طباعة',
    'shipped' => 'شحن',
    'delivered' => 'توصيل',
    'cancelled' => 'إلغاء',
];
$colors = [
    'pending_review' => '#ffc107',
    'approved' => '#28a745',
    'processing' => '#17a2b8',
    'printing' => '#6f42c1',
    'shipped' => '#007bff',
    'delivered' => '#28a745',
    'cancelled' => '#dc3545',
];
@endphp

@permission(\App\Permissions\Permission::ORDERS_STATUS)
@if(!empty($nextStatuses) && !in_array($order->status, ['cancelled', 'delivered'], true))
<div class="mt-3 d-flex align-items-center gap-2" style="gap:8px;">
    @foreach($nextStatuses as $next)
    <form action="{{ route('admin.orders.status.update', $order->id) }}" method="POST" style="display:inline;">
        @csrf
        <input type="hidden" name="status" value="{{ $next }}">
        <button type="submit"
            class="btn btn-sm"
            style="background:{{ $colors[$next] ?? '#6c757d' }}; color:#fff; border-radius:20px; padding:4px 16px; font-size:13px; border:none; cursor:pointer;">
            {{ $labels[$next] ?? $next }}
        </button>
    </form>
    @endforeach
</div>
@endif
@endpermission
