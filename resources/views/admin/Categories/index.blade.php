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
        direction: ltr !important;
        /* Pagination يفضل LTR */
        float: left !important;
        margin-top: 10px !important;
    }

    .dataTables_paginate .paginate_button {
        margin: 2px !important;
    }

    /* تحسين الـ search و length menu */
    .dataTables_filter {
        text-align: left !important;
        /* Search على اليسار */
        margin-bottom: 15px !important;
    }

    .dataTables_length {
        text-align: right !important;
        /* Show entries على اليمين */
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

    .dataTables_length select {
        text-align: end !important;
        width: auto;
        display: inline-block;
    }
</style>
{{-- عنوان الصفحة --}}
<div class="contener">
    <div class="col-lg-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">

                <h4 class="card-title text-center mb-4">كل الأقسام</h4>
                <h5 class="card-description text-center">
                    يمكنك تعديل أو حذف أي قسم من هنا
                </h5>


                {{-- زر إضافة قسم جديد --}}
                <div class="mb-3 text-end">
                    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">إضافة قسم جديد</a>
                </div>

                {{-- إشعار نجاح --}}
                @if (session('success'))
                <div class="alert alert-success text-center">{{ session('success') }}</div>
                @endif
                {{-- إشعار الحذف --}}

                @if (session('error'))
                <div class="alert alert-danger text-center w-100%" style="font-weight: bold; border-radius: 10px;">
                    {{ session('error') }}
                </div>
                @endif
                {{-- إنتهاء من شعار خطأ حذف المنتجات --}}
                <div class="table-responsive" style="border-radius: 10px">
                    <table id="productsTable" class="table table-striped">
                        <thead class="table-dark">
                            <tr>
                                <th>#</th>
                                <th>اسم القسم</th>
                                <th>الوصف</th>
                                <th>عدد المنتجات</th>
                                <th>الصورة</th>
                                <th>التحكم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($categories as $category)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td class="fw-bold">{{ $category->name }}</td>
                                <td
                                    style="max-width:200px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">
                                    {{ $category->description ?? '—' }}
                                <td>
                                    <span class="badge badge-info p-2">
                                        {{ $category->products_count }}
                                    </span>
                                </td>
                                <td class="py-1">
                                    @if ($category->imagepath)
                                    <img src="{{ asset($category->imagepath) }}" alt="صورة المنتج"
                                        style="width:60px; height:60px; border-radius:50%; object-fit:cover;">
                                    @else
                                    <img src="" alt="لا يوجد صورة"
                                        style="width:60px; height:60px; border-radius:50%; object-fit:cover;">
                                    @endif
                                </td>
                                {{-- التحكم للأقسام --}}
                                <td>
                                    <div class="d-flex gap-2" style="justify-content: center;">
                                        {{-- زر التعديل --}}
                                        <a href="{{ route('admin.categories.edit', $category->id) }}"
                                            class="btn btn-warning btn-sm control-btn">
                                            <i class="fas fa-edit"></i> تعديل
                                        </a>

                                        {{-- زر الحذف --}}
                                        <button type="button" class="btn btn-danger btn-sm control-btn"
                                            onclick="confirmDelete({{ $category->id }}, '{{ $category->name }}', {{ $category->products_count }})">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>

                                        <form id="delete-form-{{ $category->id }}"
                                            action="{{ route('admin.categories.destroy', $category->id) }}"
                                            method="POST" style="display:none;">
                                            @csrf
                                            @method('DELETE')
                                        </form>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">لا توجد أقسام حالياً</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div> {{-- end table-responsive --}}
            </div> {{-- end card-body --}}
        </div> {{-- end card --}}
    </div> {{-- end col --}}
</div>

<!-- ✅ تحميل المكتبات بالترتيب الصحيح -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function() {
        // ✅ تهيئة DataTable بإعدادات RTL
        $('#productsTable').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/ar.json" // اللغة العربية
            },
            "responsive": true,
            "ordering": true,
            "searching": true,
            "pageLength": 10,
            "lengthMenu": [10, 25, 50, 100],
            "dom": '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row"<"col-sm-12 col-md-6"i><"col-sm-12 col-md-6"p>>',
            "initComplete": function(settings, json) {
                // تحسين التخطيط بعد التحميل
                $('.dataTables_length label').contents().filter(function() {
                    return this.nodeType === 3;
                }).remove();
            }
        });
    });
</script>
@endsection
<script>
    function confirmDelete(id, name, count) {

    // لو فيه منتجات
    if (count > 0) {
        alert("❌ لا يمكن حذف القسم لأنه يحتوي على منتجات");
        return;
    }

    // تأكيد الحذف
    if (confirm("هل أنت متأكد من حذف القسم: " + name + " ؟")) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>