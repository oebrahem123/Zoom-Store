@extends('admin.layout')

@section('content')
    <!-- ✅ تصحيح مسار CSS -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/dataTables.dataTables.min.css') }}">
    <style>

    /* جعل DataTables RTL بشكل كامل */
    #productsTable_wrapper {
        direction: rtl !important;
        text-align: right !important;
    }

    /* تحسين الـ pagination */
    .dataTables_paginate {
        direction: ltr !important; /* Pagination يفضل LTR */
        float: left !important;
        margin-top: 10px !important;
    }

    .dataTables_paginate .paginate_button {
        margin: 2px !important;
    }

    /* تحسين الـ search و length menu */
    .dataTables_filter {
        text-align: left !important; /* Search على اليسار */
        margin-bottom: 15px !important;
    }

    .dataTables_length {
        text-align: right !important; /* Show entries على اليمين */
        margin-bottom: 15px !important;
    }

    /* تحسين الـ table header */
    #productsTable thead th {
        text-align: center !important;
        padding: 12px 15px !important;
    }

    /* تحسين الـ table body */
    #productsTable tbody td {
        text-align: center !important;
        padding: 10px 15px !important;
    }

    /* تحسين الـ info */
    .dataTables_info {
        margin-top: 10px !important;
    }

    /* تحسين الأزرار */
    .control-btn {
        border-radius: 15px !important;
        padding: 8px 15px !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
        border: none !important;
        min-width: 70px !important;
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        gap: 5px !important;
    }

    /* تحسين الـ responsive */
    @media (max-width: 768px) {
        .dataTables_length,
        .dataTables_filter {
            text-align: center !important;
            float: none !important;
        }

        .dataTables_paginate {
            float: none !important;
            text-align: center !important;
        }
    }
    .dataTables_length select{
        text-align: end !important;
            width: auto;
    display: inline-block;
    }


.control-btn {
    border-radius: 15px !important;
    padding: 10px 9px !important;
    font-size: 12px !important;
    font-weight: 600 !important;
    transition: all 0.3s ease !important;
    border: none !important;
    min-width: 80px !important;
    align-items: center !important;
    justify-content: center !important;
    gap: 5px !important;
}

.control-btn:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 4px 8px rgba(0,0,0,0.2) !important;
}

.btn-warning.control-btn {
    background: linear-gradient(135deg, #ffc107, #e0a800) !important;
    color: #000 !important;
}

.btn-danger.control-btn {
    background: linear-gradient(135deg, #dc3545, #c82333) !important;
    color: #fff !important;
}

/* تحسين المسافات بين الأزرار */
.d-flex.gap-2 {
    gap: 12px !important;
}

    </style>
    {{-- عنوان الصفحة --}}
    <div class="contener">
        <div class="col-lg-12 grid-margin stretch-card">
            <div class="card">
                <div class="card-body">

                    <h4 class="card-title text-center mb-4">قائمة المنتجات</h4>
                    <p class="card-description text-center">
                        يمكنك تعديل أو حذف أي منتج من هنا
                    </p>

                    {{-- زر إضافة منتج جديد --}}
                    <div class="mb-3 text-end">
                        <a href="{{ route('admin.products.create') }}" class="btn btn-primary">إضافة منتج جديد</a>
                    </div>

                    {{-- إشعار نجاح --}}
                    @if (session('success'))
                        <div class="alert alert-success text-center">{{ session('success') }}</div>
                    @endif

                    <div class="table-responsive" style="border-radius: 10px">
                        <!-- ✅ إضافة class للجدول -->
                        <table id="productsTable" class="table table-striped">
                            <thead class="table-dark">
                                <tr>
                                    <th>الصورة</th>
                                    <th>اسم المنتج</th>
                                    <th>السعر</th>
                                    <th>الكمية</th>
                                    <th>القسم</th>
                                    <th>الوصف</th>
                                    <th>التحكم</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($products as $product)
                                    <tr>
                                        {{-- صورة المنتج --}}
                                        <td class="py-1">
                                            @if ($product->imagepath && file_exists(public_path($product->imagepath)))
                                                <img src="{{ asset($product->imagepath) }}" alt="صورة المنتج"
                                                    style="width:60px; height:60px; border-radius:50%; object-fit:cover;">
                                            @else
                                                <!-- ✅ صورة افتراضية -->
                                                <img src=""
                                                    alt="لا يوجد صورة"
                                                    style="width:60px; height:60px; border-radius:50%; object-fit:cover; background:#f0f0f0;">
                                            @endif
                                        </td>

                                        {{-- اسم المنتج --}}
                                        <td class="fw-bold">{{ $product->name }}</td>

                                        {{-- السعر --}}
                                        <td>{{ number_format($product->price, 2) }} جنيه</td>

                                        {{-- الكمية --}}
                                        <td>
                                            <span class="badge {{ $product->quantity > 0 ? 'bg-success' : 'bg-danger' }}">
                                                {{ $product->quantity }}
                                            </span>
                                        </td>

                                        {{-- القسم --}}
                                        <td>{{ $product->category->name ?? 'غير محدد' }}</td>

                                        {{-- الوصف --}}
                                        <td style="max-width:200px;">
                                            <div class="text-truncate" title="{{ $product->description }}">
                                                {{ $product->description }}
                                            </div>
                                        </td>

                                        {{-- التحكم --}}
                                        <td>
                                            <div class="d-flex gap-2"style="justify-content: center;">
                                                {{-- زر التعديل --}}
                                                <a href="{{ route('admin.products.edit', $product->id) }}"
                                                    class="btn btn-warning btn-sm control-btn">
                                                    <i class="fas fa-edit"></i> تعديل
                                                </a>
                                                {{-- زر اضافه صورة --}}
                                                <a href="{{ route('admin.products.AddProductImages', $product->id) }}"
                                                    class="btn btn-dark btn-sm control-btn">
                                                    <i class="fas fa-image"></i> إضافه صور المنتج
                                                </a>

                                                {{-- زر الحذف --}}
                                                <form action="{{ route('admin.products.destroy', $product->id) }}"
                                                    method="POST" class="d-inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-danger btn-sm control-btn"
                                                        onclick="return confirm('هل أنت متأكد من حذف هذا المنتج؟');">
                                                        <i class="fas fa-trash"></i> حذف
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">لا توجد منتجات حالياً</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ✅ تحميل المكتبات بالترتيب الصحيح -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            // ✅ تهيئة DataTable بالإعدادات الصحيحة
            $('#productsTable').DataTable({

                "responsive": true,
                "ordering": true,
                "searching": true,
                "pageLength": 10,
                "lengthMenu": [10, 25, 50, 100]
            });
        });
    </script>
@endsection
