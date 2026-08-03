@extends('layouts.master')

@section('content')
<style>
    .profile-section {
        display: none;
    }

    .profile-section.active {
        display: block;
    }

    .profile-menu.active {
        background-color: #ff6e26 !important;
        border-color: #ff6e26 !important;
        color: #fff !important;
        font-weight: 600;
        box-shadow: 0 4px 12px rgba(255, 110, 38, .25);
    }
</style>


<div class="container py-5" dir="rtl">

    <div class="row">

        <!-- Sidebar -->

        <div class="col-lg-3">

            <div class="card shadow-sm">

                <div class="card-body text-center">

                    <x-user-avatar :user="$user" class="rounded-circle mb-3" width="120" height="120" style="object-fit: cover;" />

                    <h5>{{ $user->name }}</h5>

                    <p class="text-muted">
                        {{ $user->email }}
                    </p>

                </div>

                <div class="list-group list-group-flush">

                    <a href="#" class="profile-menu active list-group-item list-group-item-action"
                        data-target="account">
                        بيانات الحساب
                    </a>

                    <a href="{{ route('orders.index') }}" class="list-group-item list-group-item-action" style="display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; background:#fafafa; border:1px solid #eee; text-decoration:none; color:#333; transition:all 0.2s;">
                        <span style="font-size:18px;">📦</span>
                        <span style="font-weight:600;">طلباتي</span>
                    </a>

                    <a href="#" class="profile-menu list-group-item list-group-item-action" data-target="password">
                        كلمة المرور
                    </a>

                    <a href="{{ route('designs.my') }}" class="list-group-item list-group-item-action" style="display:flex; align-items:center; gap:10px; padding:12px 16px; border-radius:10px; margin-top:8px; background:#fafafa; border:1px solid #eee; text-decoration:none; color:#333; transition:all 0.2s;">
                        <span style="font-size:20px;">🎨</span>
                        <span style="font-weight:600;">تصميماتى</span>
                        <span style="margin-right:auto; font-size:12px; color:#999;">
                            @php $designCount = \App\Models\CustomDesign::where('user_id', Auth::id())->count(); @endphp
                            ({{ $designCount }})
                        </span>
                    </a>

                </div>

            </div>

        </div>

        <!-- Content -->

        <div class="col-lg-9 mt-4 mt-lg-0">

            <!-- Account -->

            <div id="account" class="profile-section active mb-4">

                <div class="card-header">

                    بيانات الحساب

                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <form method="POST" action="{{ route('profile.update') }}">

                                @csrf

                                <input type="text" name="name" class="form-control" value="{{ $user->name }}">

                                <input type="email" name="email" class="form-control mt-3" value="{{ $user->email }}">

                                <button class="btn btn-success mt-3">
                                    حفظ البيانات
                                </button>

                            </form>
                        </div>

                        <div class="col-md-6">

                            <label>البريد الإلكتروني</label>

                            <input type="email" class="form-control" value="{{ $user->email }}" readonly>

                        </div>

                    </div>

                </div>

            </div>

            <!-- Orders -->

            <div id="orders" class="profile-section mb-4">

                <div class="card-header">

                    آخر الطلبات

                </div>

                <div class="card-body">

                    @php
                    $standaloneOrders = $orders->whereNull('shipment_group_id');
                    @endphp

                    {{-- Shipment groups --}}
                    @foreach($shipmentGroups as $group)
                    @if($group->orders->isEmpty())
                    @continue
                    @endif

                    <div class="border rounded p-3 mb-3" style="background:#f8f9fa;">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div>
                                <strong style="font-size:16px;">📦 شحنة رقم #{{ $group->id }}</strong>
                                <span class="badge badge-info" style="font-size:12px;margin-right:8px;">
                                    {{ $group->orders->count() }} طلبات
                                </span>
                                <span class="badge badge-secondary" style="font-size:12px;">
                                    شحن موحد
                                </span>
                            </div>
                        </div>

                        @php $totalSaved = $group->orders->sum('shipping_saved'); @endphp
                        @if($totalSaved > 0)
                        <div class="alert alert-success py-2 px-3 mb-2" style="font-size:13px;border-radius:8px;">
                            💰 تم توفير {{ number_format($totalSaved, 2) }} جنيه بفضل دمج الطلبات داخل نفس الشحنة.
                        </div>
                        @endif

                        @foreach($group->orders as $order)
                        <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-center" style="background:#fff;">
                            <div>
                                <strong>طلب رقم #{{ $order->id }}</strong>
                                <br>
                                <small>{{ $order->created_at->diffForHumans() }}</small>
                                @if(!$loop->first && $order->shipping_cost == 0)
                                <br>
                                <small class="text-success" style="font-size:12px;">✅ شحن مجاني (موفر ضمن الشحنة)</small>
                                @endif
                            </div>
                            <div>
                                @if($order->status === 'cancelled' && $order->rejected_at)
                                <span class="badge badge-danger" style="font-size:14px;padding:6px 14px;">❌ مرفوض</span>
                                @elseif($order->status === 'approved')
                                <span class="badge badge-success" style="font-size:14px;padding:6px 14px;">✓ مقبول</span>
                                @else
                                <span class="badge badge-warning" style="font-size:14px;padding:6px 14px;">⏳ قيد التنفيذ</span>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach

                    {{-- Standalone orders (no shipment group) --}}
                    @foreach($standaloneOrders as $order)
                    <div class="border rounded p-3 mb-2 d-flex justify-content-between align-items-center">
                        <div>
                            <strong>طلب رقم #{{ $order->id }}</strong>
                            <br>
                            <small>{{ $order->created_at->diffForHumans() }}</small>
                        </div>
                        <div>
                            @if($order->status === 'cancelled' && $order->rejected_at)
                            <span class="badge badge-danger" style="font-size:14px;padding:6px 14px;">❌ مرفوض</span>
                            @elseif($order->status === 'approved')
                            <span class="badge badge-success" style="font-size:14px;padding:6px 14px;">✓ مقبول</span>
                            @else
                            <span class="badge badge-warning" style="font-size:14px;padding:6px 14px;">⏳ قيد التنفيذ</span>
                            @endif
                        </div>
                    </div>
                    @endforeach

                    @if($orders->isEmpty())
                    <div class="alert alert-info">لا توجد طلبات حتى الآن</div>
                    @endif

                </div>

            </div>

            <!-- Password -->

            <div id="password" class="profile-section mb-4">

                <div class="card-header">

                    الأمان

                </div>

                <div class="card-body">

                    <form method="POST" action="{{ route('profile.password') }}">

                        @csrf

                        <input type="password" name="current_password" class="form-control mb-2"
                            placeholder="كلمة المرور الحالية">

                        <input type="password" name="password" class="form-control mb-2"
                            placeholder="كلمة المرور الجديدة">

                        <input type="password" name="password_confirmation" class="form-control mb-3"
                            placeholder="تأكيد كلمة المرور">

                        <button class="btn btn-warning">
                            تغيير كلمة المرور
                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>
<script>
    document.querySelectorAll('.profile-menu').forEach(item => {

    item.addEventListener('click', function(e){

        e.preventDefault();

        document.querySelectorAll('.profile-menu')
            .forEach(x => x.classList.remove('active'));

        this.classList.add('active');

        document.querySelectorAll('.profile-section')
            .forEach(x => x.classList.remove('active'));

        document.getElementById(
            this.dataset.target
        ).classList.add('active');

    });

});

</script>
@endsection