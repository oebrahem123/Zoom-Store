@if(isset($overview))
<div class="row">
    <div class="col-md-12">
        <h4 class="mb-3">نظرة سريعة</h4>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-tale">
            <div class="card-body">
                <p class="mb-4">طلبات معلقة</p>
                <p class="fs-30 mb-2">{{ $overview['pendingOrdersCount'] ?? 0 }}</p>
                <p>بانتظار المراجعة أو الموافقة</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-dark-blue">
            <div class="card-body">
                <p class="mb-4">مبيعات اليوم</p>
                <p class="fs-30 mb-2">{{ number_format($overview['revenueToday'] ?? 0, 2) }} ج.م</p>
                <p>إجمالي مبيعات اليوم</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-light-blue">
            <div class="card-body">
                <p class="mb-4">طلبات اليوم</p>
                <p class="fs-30 mb-2">{{ $overview['ordersTodayCount'] ?? 0 }}</p>
                <p>طلبات جديدة اليوم</p>
            </div>
        </div>
    </div>
    <div class="col-md-3 grid-margin stretch-card">
        <div class="card card-light-danger">
            <div class="card-body">
                <p class="mb-4">توزيع الحالات</p>
                <p class="fs-30 mb-2">{{ ($overview['statusDistribution'] ?? collect())->count() }}</p>
                <p>حالة طلب مختلفة</p>
            </div>
        </div>
    </div>
</div>
@endif