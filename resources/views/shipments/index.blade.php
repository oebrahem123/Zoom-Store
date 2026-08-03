@extends('layouts.master')

@section('content')
<style>
    .shipments-page { direction: rtl; text-align: right; font-family: "Tajawal", sans-serif; }
    .shipment-card {
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 16px;
        background: #fff;
        transition: box-shadow 0.2s;
    }
    .shipment-card:hover { box-shadow: 0 4px 16px rgba(0,0,0,0.08); }
    .badge-status { padding: 4px 14px; border-radius: 20px; font-size: 13px; font-weight: 600; display: inline-block; }
    .badge-pending { background: #fff3cd; color: #856404; }
    .badge-processing { background: #cce5ff; color: #004085; }
    .badge-shipped { background: #d1ecf1; color: #0c5460; }
    .badge-delivered { background: #d4edda; color: #155724; }
    .badge-cancelled { background: #f8d7da; color: #721c24; }
</style>

@php
$statusDisplay = [
    'action_required' => ['icon' => 'fa-exclamation-triangle', 'label' => 'يتطلب إجراء'],
    'delivered'       => ['icon' => 'fa-check-circle',      'label' => 'تم التوصيل'],
    'shipped'         => ['icon' => 'fa-truck',             'label' => 'تم الشحن'],
    'processing'      => ['icon' => 'fa-cog',               'label' => 'قيد التجهيز'],
    'pending'         => ['icon' => 'fa-hourglass-half',    'label' => 'قيد التنفيذ'],
];
@endphp

<div class="container shipments-page py-5">
    <h2 class="mb-4"><i class="fa fa-truck"></i> شحناتي</h2>

    @if($shipments->isEmpty())
    <div class="alert alert-info">لا توجد شحنات حتى الآن</div>
    @else
    @foreach($shipments as $shipment)
    @php
    $st = $statuses[$shipment->id] ?? null;
    $cur = $st ? ($statusDisplay[$st['status']] ?? ['icon' => 'fa-circle', 'label' => $st['label']]) : null;
    @endphp
    <a href="{{ route('shipments.show', $shipment->id) }}" class="text-decoration-none" style="color:inherit;">
        <div class="shipment-card d-flex justify-content-between align-items-center">
            <div>
                <div style="font-size:18px;font-weight:700;">
                    <i class="fa fa-cube"></i> شحنة رقم #{{ $shipment->id }}
                </div>
                <div style="font-size:13px;color:#888;">{{ $shipment->created_at->format('d-m-Y h:i A') }}</div>
                @if($cur)
                <div class="mt-1">
                    <span class="badge-status {{ $st['badge'] }}">
                        <i class="fa {{ $cur['icon'] }}"></i> {{ $cur['label'] }}
                    </span>
                    @if($st['action_required'])
                    <span style="font-size:12px;color:#721c24;margin-right:8px;">يحتاج إلى مراجعة</span>
                    @endif
                </div>
                @endif
            </div>
            <div class="text-left">
                <span class="btn btn-sm btn-outline-primary" style="border-radius:20px;cursor:default;">
                    عرض التفاصيل
                </span>
            </div>
        </div>
    </a>
    @endforeach

    <div class="mt-4" dir="ltr">
        {{ $shipments->links() }}
    </div>
    @endif
</div>
@endsection
