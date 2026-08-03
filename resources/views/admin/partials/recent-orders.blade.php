@if(isset($recentOrders) && $recentOrders->isNotEmpty())
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">آخر الطلبات</h4>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>العميل</th>
                                <th>الحالة</th>
                                <th>الإجمالي</th>
                                <th>القطع</th>
                                <th>التاريخ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentOrders as $order)
                            <tr>
                                <td>{{ $order['id'] }}</td>
                                <td>{{ $order['customer'] }}</td>
                                <td>
                                    @switch($order['status'])
                                        @case('approved')
                                            <span class="badge badge-success">مقبول</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge badge-danger">ملغي</span>
                                            @break
                                        @case('pending_review')
                                            <span class="badge badge-warning">قيد المراجعة</span>
                                            @break
                                        @default
                                            <span class="badge badge-secondary">{{ $order['status'] }}</span>
                                    @endswitch
                                </td>
                                <td>{{ number_format($order['total'], 2) }} ج.م</td>
                                <td>{{ $order['items_count'] }}</td>
                                <td>{{ \Carbon\Carbon::parse($order['created_at'])->format('Y-m-d') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endif
