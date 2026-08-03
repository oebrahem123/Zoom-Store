@if(isset($actions))
<div class="row">
    <div class="col-md-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">إجراءات سريعة</h4>
                <div class="row">
                    @if(($actions['pendingReviewsCount'] ?? 0) > 0)
                    <div class="col-md-4 mb-3">
                        <div class="alert alert-warning mb-0 d-flex align-items-center justify-content-between">
                            <span>
                                <i class="mdi mdi-eye mr-2"></i>
                                <strong>{{ $actions['pendingReviewsCount'] }}</strong> طلب بانتظار المراجعة
                            </span>
                            <a href="{{ route('admin.orders.previousorder') }}" class="btn btn-sm btn-outline-warning">عرض</a>
                        </div>
                    </div>
                    @endif
                    @if(($actions['lowStockCount'] ?? 0) > 0)
                    <div class="col-md-4 mb-3">
                        <div class="alert alert-danger mb-0 d-flex align-items-center justify-content-between">
                            <span>
                                <i class="mdi mdi-alert-circle mr-2"></i>
                                <strong>{{ $actions['lowStockCount'] }}</strong> منتج منخفض المخزون
                            </span>
                            <a href="{{ route('admin.products.index') }}" class="btn btn-sm btn-outline-danger">عرض</a>
                        </div>
                    </div>
                    @endif
                    @if(($actions['lateOrdersCount'] ?? 0) > 0)
                    <div class="col-md-4 mb-3">
                        <div class="alert alert-info mb-0 d-flex align-items-center justify-content-between">
                            <span>
                                <i class="mdi mdi-clock-alert mr-2"></i>
                                <strong>{{ $actions['lateOrdersCount'] }}</strong> طلب متأخر
                            </span>
                            <a href="{{ route('admin.orders.previousorder') }}" class="btn btn-sm btn-outline-info">عرض</a>
                        </div>
                    </div>
                    @endif
                    @if(($actions['pendingReviewsCount'] ?? 0) === 0 && ($actions['lowStockCount'] ?? 0) === 0 && ($actions['lateOrdersCount'] ?? 0) === 0)
                    <div class="col-12">
                        <p class="text-muted mb-0">لا توجد إجراءات مطلوبة حالياً. كل شيء على ما يرام.</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endif
