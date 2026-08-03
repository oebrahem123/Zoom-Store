@extends('admin.layout')

@section('content')

<style>
    .order-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 25px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    .group-header {
        background: #e8f0fe;
        padding: 15px 20px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 2px solid #c5d9f2;
    }
    .group-header:hover {
        background: #d4e4fc;
    }
    .group-body {
        padding: 0;
    }
    .order-header {
        background: #f8f9fa;
        padding: 12px 20px 12px 40px;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 1px solid #eee;
    }
    .order-header:hover {
        background: #eef2f5;
    }
    .order-body {
        padding: 25px;
        background: #fff;
    }
    .order-body input,
    .order-body textarea {
        background: #f2f2f2 !important;
        border: 1px solid #ccc !important;
        color: #333 !important;
        font-weight: 500;
    }
    .order-body input:focus,
    .order-body textarea:focus {
        box-shadow: none !important;
    }
    .cart-table img {
        width: 70px;
        height: 70px;
        border-radius: 6px;
        object-fit: cover;
    }
    .cart-table th {
        background: #f7f7f7;
        font-size: 14px;
        font-weight: bold;
    }
    .cart-table td {
        vertical-align: middle;
        font-size: 14px;
    }
    .badge-status {
        padding: 6px 10px;
        border-radius: 6px;
        font-size: 13px;
        margin-right: 10px;
    }
    .badge-pending {
        background: #ffc107;
    }
    .badge-complete {
        background: #28a745;
        color: #fff;
    }
    .badge-processing {
        background: #17a2b8;
        color: #fff;
    }
    .badge-cancelled {
        background: #dc3545;
        color: #fff;
    }
    .badge-approved {
        background: #28a745;
        color: #fff;
    }
    .badge-shipped {
        background: #007bff;
        color: #fff;
    }
    .badge-delivered {
        background: #28a745;
        color: #fff;
    }
    .badge-action_required {
        background: #dc3545;
        color: #fff;
    }
    .cart-table tbody {
        font-size: 16px;
        color: #333;
    }
    .cart-table .total-data {
        background-color: #f2f2f2;
        font-weight: bold;
        color: #f28123;
        font-size: 18px;
        border-top: 2px solid #ddd;
    }
    .cart-table .total-data td {
        padding: 12px 15px;
    }
    .cart-table .total-data td:last-child {
        text-align: right;
    }
    .summary-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        gap: 24px;
        flex-wrap: wrap;
    }
    .summary-list li {
        font-size: 14px;
        color: #555;
    }
    .summary-list li strong {
        color: #222;
    }
    .wf-btn {
        border-radius: 20px;
        padding: 4px 16px;
        font-size: 13px;
        border: none;
        cursor: pointer;
        color: #fff;
        display: inline-block;
    }
</style>

@php
use App\Services\Workflow\ShipmentWorkflowPolicy;
use App\Services\Workflow\WorkflowAggregator;
@endphp

<div class="container mt-5">

    <h3 class="mb-4 text-center">📦 قائمة الطلبات</h3>

    <div class="accordion" id="ordersAccordion">

        {{-- ============ SHIPMENT GROUPS ============ --}}
        @foreach ($shipmentGroups as $bundle)
        @php
            $group = $bundle['group'];
            $gOrders = $bundle['orders'];
            $totalSaved = $gOrders->sum('shipping_saved');
            $aggregate = ShipmentWorkflowPolicy::derive($group);
            $allowedActions = ShipmentWorkflowPolicy::allowedActions($group);
        @endphp

        <div class="order-card">

            {{-- Group header with shipment workflow --}}
            <div class="group-header" style="display:flex;align-items:center;flex-wrap:wrap;gap:8px;">
                <span style="cursor:pointer;" data-toggle="collapse" data-target="#collapseGroup{{ $group->id }}">
                    📦 شحنة #{{ $group->id }}
                </span>
                <span class="badge {{ $aggregate['badge'] }}" style="font-size:12px;padding:3px 10px;">
                    {{ $aggregate['label'] }}
                </span>
                <span class="badge badge-info" style="font-size:12px;padding:3px 10px;">
                    {{ $gOrders->count() }} طلبات
                </span>
                @if($totalSaved > 0)
                <span class="badge badge-success" style="font-size:12px;padding:3px 10px;">
                    💰 توفير {{ number_format($totalSaved, 2) }} ج.م
                </span>
                @endif

                {{-- Shipment workflow action buttons --}}
                @permission(\App\Permissions\Permission::SHIPMENTS_WORKFLOW)
                @if(!empty($allowedActions))
                <span style="margin-left:auto;display:flex;gap:6px;flex-wrap:wrap;">
                    @foreach($allowedActions as $act)
                    @php
                        $btnColor = '#6c757d';
                        if ($act === 'approve') $btnColor = '#28a745';
                        elseif ($act === 'process') $btnColor = '#17a2b8';
                        elseif ($act === 'print') $btnColor = '#6f42c1';
                        elseif ($act === 'ship') $btnColor = '#007bff';
                        elseif ($act === 'deliver') $btnColor = '#28a745';
                        elseif ($act === 'cancel') $btnColor = '#dc3545';
                    @endphp
                    <form action="{{ route('admin.shipments.workflow', $group->id) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="action" value="{{ $act }}">
                        <button type="submit" class="wf-btn" style="background:{{ $btnColor }};">
                            {{ ShipmentWorkflowPolicy::actionLabel($act) }}
                        </button>
                    </form>
                    @endforeach
                </span>
                @endif
                @endpermission
            </div>

            {{-- Group body --}}
            <div id="collapseGroup{{ $group->id }}" class="collapse" data-parent="#ordersAccordion">
                <div class="group-body">

                    {{-- Group summary --}}
                    <div style="padding:15px 20px;background:#fafcff;border-bottom:1px solid #e0e8f0;">
                        <ul class="summary-list">
                            <li><strong>👤 العميل:</strong> {{ $gOrders->first()->name }}</li>
                            <li><strong>📦 رقم الشحنة:</strong> #{{ $group->id }}</li>
                            <li><strong>🔢 عدد الطلبات:</strong> {{ $gOrders->count() }}</li>
                            @if($totalSaved > 0)
                            <li><strong>💰 إجمالي التوفير:</strong> {{ number_format($totalSaved, 2) }} ج.م</li>
                            @endif
                            <li><strong>📅 من:</strong> {{ \Carbon\Carbon::parse($gOrders->min('created_at'))->format('Y-m-d') }}</li>
                        </ul>
                    </div>

                    {{-- Each order inside the group (informational, exception-only actions) --}}
                    @foreach ($gOrders as $item)
                    <div class="order-header" data-toggle="collapse" data-target="#groupOrder{{ $item->id }}">
                        <span>رقم الطلب: #{{ $item->id }}</span>
                        @if($item->status === 'cancelled' && $item->rejected_at)
                        <span class="badge badge-cancelled" style="margin-right:8px;font-size:13px;padding:4px 12px;cursor:help;" title="سبب الرفض: {{ $item->rejection_reason }}">❌ مرفوض</span>
                        @elseif($item->status === 'approved')
                        <span class="badge badge-approved" style="margin-right:8px;font-size:13px;padding:4px 12px;">✓ مقبول</span>
                        @else
                        <span class="badge badge-pending" style="margin-right:8px;font-size:13px;padding:4px 12px;">⏳ قيد التنفيذ</span>
                        @endif
                        @if($item->shipping_saved > 0)
                        <span class="text-success" style="margin-right:8px;font-size:12px;">✅ شحن مجاني (موفر {{ number_format($item->shipping_saved, 2) }} ج.م)</span>
                        @elseif($item->shipping_cost > 0)
                        <span style="margin-right:8px;font-size:12px;color:#666;">🚚 تكلفة الشحن: {{ number_format($item->shipping_cost, 2) }} ج.م</span>
                        @endif
                    </div>
                    <div id="groupOrder{{ $item->id }}" class="collapse" data-parent="#collapseGroup{{ $group->id }}">
                        <div class="order-body">

                            <div class="row">
                                <div class="col-md-12">
                                    <label> تم إنشاء الطلب بتاريخ : </label>
                                    <input type="datetime-local" class="form-control mb-3 text-right" value="{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d\TH:i') }}" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label>الاسم:</label>
                                    <input type="text" class="form-control mb-3" value="{{ $item->name }}" readonly disabled>
                                    <label>البريد الإلكتروني:</label>
                                    <input type="email" class="form-control mb-3" value="{{ $item->email }}" readonly disabled>
                                </div>
                                <div class="col-md-6">
                                    <label>العنوان:</label>
                                    <input type="text" class="form-control mb-3" value="{{ $item->address }}" readonly disabled>
                                    <label>رقم الهاتف:</label>
                                    <input type="text" class="form-control mb-3" value="{{ $item->phone }}" readonly disabled>
                                </div>
                                <div class="col-md-12">
                                    <label>ملاحظات:</label>
                                    <textarea class="form-control mb-4" rows="4" readonly disabled>{{ $item->note }}</textarea>
                                </div>
                            </div>

                            <hr>
                            <h5 class="mt-3 mb-3">🛒 المنتجات المطلوبة</h5>

                            <div class="table-responsive">
                                <table class="table table-bordered cart-table" style="text-align: center;">
                                    <thead>
                                        <tr>
                                            <th>الصورة</th>
                                            <th>اسم المنتج</th>
                                            <th>المقاس</th>
                                            <th>اللون</th>
                                            <th>السعر</th>
                                            <th>الكمية</th>
                                            <th>الإجمالي</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->orderdetails as $detail)
                                        <tr>
                                            <td>
                                                @php $rowImg = $detail->displayImagePath(); @endphp
                                                <img src="{{ $rowImg ? asset($rowImg) : asset('images/default.png') }}" width="50" alt="">
                                            </td>
                                            <td>
                                                {{ $detail->displayName() }}
                                                @if($detail->catalogStatusMessage())
                                                <span style="display:inline-block;font-size:11px;color:#856404;background:#fff3cd;padding:2px 8px;border-radius:4px;margin-right:6px;vertical-align:middle;">{{ $detail->catalogStatusMessage() }}</span>
                                                @endif
                                                @if($detail->design_id)
                                                <br>
                                                <span style="display:inline-block;margin-top:4px;">
                                                    <a href="{{ route('admin.orders.design.show', ['orderId' => $item->id, 'detailId' => $detail->id]) }}" class="btn btn-sm btn-outline-warning" style="border-radius:12px;padding:2px 10px;font-size:12px;">🎨 عرض التصميم</a>
                                                </span>
                                                @endif
                                            </td>
                                            <td>{{ $detail->size ?? '—' }}</td>
                                            <td>{{ $detail->color ?? '—' }}</td>
                                            <td>{{ $detail->price }} ج.م</td>
                                            <td>{{ $detail->quantity }}</td>
                                            <td>{{ number_format($detail->lineTotal(), 2) }} ج.م</td>
                                        </tr>
                                        @endforeach
                                        <tr class="total-data">
                                            <td colspan="6" style="text-align: justify;"><strong> إجمالى المبلغ :</strong></td>
                                            <td>{{ number_format($item->orderdetails->sum(fn($x) => $x->lineTotal()), 2) }} ج.م</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            {{-- Exception actions only (per-order) — normal workflow is at shipment level --}}
                            <div class="mt-3 d-flex align-items-center gap-2" style="gap:8px;">
                                @permission(\App\Permissions\Permission::ORDERS_STATUS)
                                @if(!in_array($item->status, ['delivered', 'cancelled'], true))
                                <form action="{{ route('admin.shipments.workflow', $group->id) }}" method="POST" style="display:inline;">
                                    @csrf
                                    <input type="hidden" name="action" value="cancel">
                                    <button type="submit" class="btn btn-sm" style="background:#dc3545;color:#fff;border-radius:20px;padding:4px 16px;font-size:13px;border:none;cursor:pointer;">
                                        إلغاء الطلب
                                    </button>
                                </form>
                                @endif
                                @endpermission
                                @if($item->isRejected())
                                <span class="text-muted" style="font-size:13px;">⛔ تم الرفض — يتطلب إعادة تقديم التصميم</span>
                                @endif
                            </div>

                        </div>
                    </div>
                    @endforeach

                </div>
            </div>
        </div>
        @endforeach

        {{-- ============ STANDALONE ORDERS ============ --}}
        @foreach ($standaloneOrders as $item)

        <div class="order-card">

            <div class="order-header" data-toggle="collapse" data-target="#standalone{{ $item->id }}">
                <span>رقم الطلب: #{{ $item->id }}</span>
                @if($item->status === 'cancelled' && $item->rejected_at)
                <span class="badge badge-cancelled" style="margin-right:8px;font-size:13px;padding:4px 12px;cursor:help;" title="سبب الرفض: {{ $item->rejection_reason }}">❌ مرفوض</span>
                @elseif($item->status === 'approved')
                <span class="badge badge-approved" style="margin-right:8px;font-size:13px;padding:4px 12px;">✓ مقبول</span>
                @else
                <span class="badge badge-pending" style="margin-right:8px;font-size:13px;padding:4px 12px;">⏳ قيد التنفيذ</span>
                @endif
            </div>

            <div id="standalone{{ $item->id }}" class="collapse" data-parent="#ordersAccordion">
                <div class="order-body">

                    <div class="row">
                        <div class="col-md-12">
                            <label> تم إنشاء الطلب بتاريخ : </label>
                            <input type="datetime-local" class="form-control mb-3 text-right" value="{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d\TH:i') }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label>الاسم:</label>
                            <input type="text" class="form-control mb-3" value="{{ $item->name }}" readonly disabled>
                            <label>البريد الإلكتروني:</label>
                            <input type="email" class="form-control mb-3" value="{{ $item->email }}" readonly disabled>
                        </div>
                        <div class="col-md-6">
                            <label>العنوان:</label>
                            <input type="text" class="form-control mb-3" value="{{ $item->address }}" readonly disabled>
                            <label>رقم الهاتف:</label>
                            <input type="text" class="form-control mb-3" value="{{ $item->phone }}" readonly disabled>
                        </div>
                        <div class="col-md-12">
                            <label>ملاحظات:</label>
                            <textarea class="form-control mb-4" rows="4" readonly disabled>{{ $item->note }}</textarea>
                        </div>
                    </div>

                    <hr>
                    <h5 class="mt-3 mb-3">🛒 المنتجات المطلوبة</h5>

                    <div class="table-responsive">
                        <table class="table table-bordered cart-table" style="text-align: center;">
                            <thead>
                                <tr>
                                    <th>الصورة</th>
                                    <th>اسم المنتج</th>
                                    <th>المقاس</th>
                                    <th>اللون</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>الإجمالي</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($item->orderdetails as $detail)
                                <tr>
                                    <td>
                                        @php $rowImg = $detail->displayImagePath(); @endphp
                                        <img src="{{ $rowImg ? asset($rowImg) : asset('images/default.png') }}" width="50" alt="">
                                    </td>
                                    <td>
                                        {{ $detail->displayName() }}
                                        @if($detail->catalogStatusMessage())
                                        <span style="display:inline-block;font-size:11px;color:#856404;background:#fff3cd;padding:2px 8px;border-radius:4px;margin-right:6px;vertical-align:middle;">{{ $detail->catalogStatusMessage() }}</span>
                                        @endif
                                        @if($detail->design_id)
                                        <br>
                                        <span style="display:inline-block;margin-top:4px;">
                                            <a href="{{ route('admin.orders.design.show', ['orderId' => $item->id, 'detailId' => $detail->id]) }}" class="btn btn-sm btn-outline-warning" style="border-radius:12px;padding:2px 10px;font-size:12px;">🎨 عرض التصميم</a>
                                        </span>
                                        @endif
                                    </td>
                                    <td>{{ $detail->size ?? '—' }}</td>
                                    <td>{{ $detail->color ?? '—' }}</td>
                                    <td>{{ $detail->price }} ج.م</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ number_format($detail->lineTotal(), 2) }} ج.م</td>
                                </tr>
                                @endforeach
                                <tr class="total-data">
                                    <td colspan="6" style="text-align: justify;"><strong> إجمالى المبلغ :</strong></td>
                                    <td>{{ number_format($item->orderdetails->sum(fn($x) => $x->lineTotal()), 2) }} ج.م</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    @include('admin.orders.partials.status-actions', ['order' => $item])

                </div>
            </div>
        </div>
        @endforeach

    </div>

</div>

@endsection
