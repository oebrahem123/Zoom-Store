@extends('admin.layout')

@section('content')

<style>
    /* تنسيق عام */
    .order-card {
        border: 1px solid #ddd;
        border-radius: 8px;
        margin-bottom: 25px;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }

    .order-header {
        background: #f8f9fa;
        padding: 15px 20px;
        font-size: 18px;
        font-weight: 600;
        cursor: pointer;
        border-bottom: 1px solid #ddd;
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

    /* tbody كامل */
    .cart-table tbody {
        font-size: 16px;
        color: #333;
    }

    .table,
    .jsgrid .jsgrid-table {
        color: #f28123 !important;
    }

    /* صف المجموع */
    .cart-table .total-data {
        background-color: #f2f2f2;
        /* لون خلفية */
        font-weight: bold;
        color: #f28123;
        /* لون النص */
        font-size: 18px;
        border-top: 2px solid #ddd;
    }

    /* الخلايا */
    .cart-table .total-data td {
        padding: 12px 15px;
    }

    .cart-table .total-data td:last-child {
        text-align: right;
    }
</style>

<div class="container mt-5">

    <h3 class="mb-4 text-center">📦 قائمة الطلبات</h3>

    <div class="accordion" id="ordersAccordion">

        @foreach ($orders as $item)

        <div class="order-card">

            <!-- Header -->
            <div class="order-header" data-toggle="collapse" data-target="#order{{ $item->id }}">
                <span>رقم الطلب: #{{ $item->id }}</span>
            </div>

            <!-- Body -->
            <div id="order{{ $item->id }}" class="collapse show" data-parent="#ordersAccordion">
                <div class="order-body">

                    <div class="row">
                        <div class="col-md-12">
                            <label> تم إنشاء الطلب بتاريخ : </label>
                            <input type="datetime-local" class="form-control mb-3 text-right"
                                value="{{ \Carbon\Carbon::parse($item->created_at)->format('Y-m-d\TH:i') }}" readonly
                                disabled>
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

                    <!-- Products Table -->
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
                                        <img src="{{ asset($detail->product->imagepath) }}" width="50">
                                    </td>
                                    <td>{{ $detail->product->name }}</td>
                                    <td>{{ $detail->size ?? '—' }}</td>
                                    <td>{{ $detail->color ?? '—' }}</td>
                                    <td>{{ $detail->price }} ج.م</td>
                                    <td>{{ $detail->quantity }}</td>
                                    <td>{{ $detail->price * $detail->quantity }} ج.م</td>
                                </tr>
                                @endforeach
                                <tr class="total-data">
                                    <td colspan="4" style="text-align: justify;"><strong> إجمالى المبلغ :</strong></td>
                                    <td>{{ $item->orderdetails->sum(fn($x)=> $x->product->price * $x->quantity) }} ج.م
                                    </td>
                                </tr>
                            </tbody>
                            </tbody>
                        </table>

                    </div>

                </div>
            </div>

        </div>

        @endforeach

    </div>

</div>

@endsection
