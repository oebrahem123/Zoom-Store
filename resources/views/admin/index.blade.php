@extends('admin.layout')
@section('content')
<!-- partial -->

<div class="row">
    <div class="col-md-12 grid-margin">
        <div class="row">
            <div class="col-12 col-xl-8 mb-4 mb-xl-0">
                <h3 class="font-weight-bold">
                    مرحباً
                    @auth
                    {{ Auth::user()->name }}
                    @else
                    Guest
                    @endauth
                </h3>
                <h6 class="font-weight-normal mb-0">جميع الأنظمة تعمل بسلاسة! لديك <span class="text-primary">3 تنبيهات
                        غير مقروءة!</span></h6>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card tale-bg">
            <div class="card-people mt-auto">
                <img src="{{ asset('assets/admin/images/dashboard/people.svg') }}" alt="people">
                <div class="weather-info">
                    <div class="d-flex">
                        <div>
                            <h2 class="mb-0 font-weight-normal">
                                <i class="icon-sun mr-2"></i>
                                {{ round($temperature) }}<sup>°C</sup>
                            </h2>
                        </div>
                        <div class="ml-2">
                            <h4 class="location font-weight-normal">{{ $city }}</h4>
                            <h6 class="font-weight-normal">{{ $country }}</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 grid-margin transparent">
        <div class="row">
            <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-tale">
                    <div class="card-body">
                        <p class="mb-4">عدد المنتجات</p>
                        <p class="fs-30 mb-2">{{ $productsCount }}</p>
                        <p>إجمالي المبيعات: {{ number_format($totalSales, 2) }} ج.م</p>
                    </div>
                </div>
            </div>
            <div class="col-md-6 mb-4 stretch-card transparent">
                <div class="card card-dark-blue">
                    <div class="card-body">
                        <p class="mb-4">عدد الأقسام</p>
                        <p class="fs-30 mb-2">{{ $categoriesCount }}</p>
                        <p>يحتوي كل قسم على منتجات متعددة</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-4 mb-lg-0 stretch-card transparent">
                <div class="card card-light-blue">
                    <div class="card-body">
                        <p class="mb-4">عدد الطلبات</p>
                        <p class="fs-30 mb-2">{{ $ordersCount }}</p>
                        <p>طلبات تم تنفيذها خلال الشهر</p>
                    </div>
                </div>
            </div>

            <div class="col-md-6 stretch-card transparent">
                <div class="card card-light-danger">
                    <div class="card-body">
                        <p class="mb-4">عدد المستخدمين</p>
                        <p class="fs-30 mb-2">{{ $usersCount }}</p>
                        <p>عملاء مسجلين بالموقع</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <p class="card-title">تفاصيل الطلبات</p>
                <p class="font-weight-500">
                    نظرة عامة على أداء الطلبات وعدد المستخدمين خلال الفترة الأخيرة.
                </p>
                <div class="d-flex flex-wrap mb-5">
                    <div class="mr-5 mt-3">
                        <p class="text-muted">قيمة الطلبات</p>
                        <h3 class="text-primary fs-30 font-weight-medium">
                            {{ number_format($totalSales, 2) }} ج.م</h3>
                        </h3>
                    </div>
                    <div class="mr-5 mt-3">
                        <p class="text-muted">عدد الطلبات</p>
                        <h3 class="text-primary fs-30 font-weight-medium">
                            {{ $ordersCount }}
                        </h3>
                    </div>
                    <div class="mr-5 mt-3">
                        <p class="text-muted">المستخدمين</p>
                        <h3 class="text-primary fs-30 font-weight-medium">
                            {{ $usersCount }}
                        </h3>
                    </div>
                    <div class="mt-3">
                        <p class="text-muted">المنتجات المباعة</p>
                        <h3 class="text-primary fs-30 font-weight-medium">
                            {{ $totalSoldProducts }}
                        </h3>
                    </div>
                </div>

                {{-- رسم بياني للطلبات --}}
                <canvas id="order-chart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <p class="card-title">تقرير المبيعات الأسبوعي</p>
                    <a href="#" class="text-info">عرض التفاصيل</a>
                </div>
                <p class="font-weight-500">
                    يوضح هذا التقرير تطور عدد الطلبات خلال آخر 7 أيام.
                </p>
                <canvas id="sales-chart"></canvas>
            </div>
        </div>
    </div>
</div>

</div>
<!-- content-wrapper ends -->

<!-- Footer مصحح -->
<footer class="footer"
    style="position: relative; margin-top: auto; background: #fff; padding: 20px; border-top: 1px solid #e2e8f0;">
    <div class="d-sm-flex justify-content-center justify-content-sm-between">
        <span class="text-muted text-center text-sm-left d-block d-sm-inline-block">
            جميع الحقوق محفوظة © 2024.
            <a href="#" target="_blank">قالب الأدمن</a>
        </span>
        <span class="float-none float-sm-right d-block mt-1 mt-sm-0 text-center">
            مصنوع يدوياً بـ <i class="ti-heart text-danger ml-1"></i>
        </span>
    </div>
</footer>
</div>
<!-- main-panel ends -->









<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
    const ctx = document.getElementById('order-chart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: @json($dates),
            datasets: [{
                label: 'عدد الطلبات اليومية',
                data: @json($counts),
                backgroundColor: 'rgba(54, 162, 235, 0.6)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: { precision: 0 }
                }
            }
        }
    });
});
</script>


@endsection