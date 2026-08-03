<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title> Admin Store</title>
    <!-- plugins:css -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css">
    <!-- Feather Icons -->
    <script src="https://unpkg.com/feather-icons"></script>

    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font/css/materialdesignicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/feather/feather.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/css/vendor.bundle.base.css') }}">
    <!-- endinject -->
    <!-- Plugin css for this page -->
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/ti-icons/css/themify-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/admin/vendors/datatables.net-bs4/dataTables.bootstrap4.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/admin/js/select.dataTables.min.css') }}">
    <!-- End plugin css for this page -->
    <!-- inject:css -->
    <link rel="stylesheet" href="{{ asset('assets/admin/css/vertical-layout-light/style.css') }}">
    <!-- endinject -->
    <link rel="shortcut icon" href="{{ asset('assets/frontend/images/logo/icon.png') }}" />
    <link rel="stylesheet" href="{{ asset('assets/admin/css/style.css') }}">
</head>

<body>
    <div class="container-scroller">
        <!-- partial:partials/_navbar.html -->
        <nav class="navbar col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
            <div class="text-center navbar-brand-wrapper d-flex align-items-center justify-content-center">
                <a class="navbar-brand brand-logo-mini" href="index.html"><img
                        src="{{ asset('assets/frontend/images/logo/icon.png') }}" alt="logo" /></a>
                <a class="navbar-brand brand-logo" href="index.html"><img
                        src="{{ asset('assets/frontend/images/logo/logo-white.png') }}" class="mr-2" alt="logo" /></a>
            </div>
            <div class="navbar-menu-wrapper d-flex align-items-center justify-content-end">
                <button class="navbar-toggler navbar-toggler align-self-center" type="button" data-toggle="minimize">
                    <span class="icon-menu"></span>
                </button>

                <ul class="navbar-nav navbar-nav-right">

                    @php $adminUser = Auth::guard('admin')->user(); @endphp
                    @guest('admin')
                    {{-- Admin غير مسجل --}}
                    @if (Route::has('admin.login'))
                    <li class="nav-item">
                        <a class="nav-link text-white" href="{{ route('admin.login') }}">{{ __('تسجيل الدخول') }}</a>
                    </li>
                    @endif
                    @else
                    {{-- Admin مسجل الدخول --}}
                    <li class="nav-item nav-profile dropdown d-flex align-items-center">
                        <a class="nav-link dropdown-toggle d-flex align-items-center justify-content-end" href="#"
                            data-toggle="dropdown" id="profileDropdown"
                            style="gap: 8px; color: #fff; text-decoration: none;">

                            <span class="fw-bold" style="color: #fff;">{{ $adminUser->name }}</span>

                            @if ($adminUser->profile_image)
                            <img src="{{ asset($adminUser->profile_image) }}" alt="profile" class="rounded-circle"
                                style="width:32px; height:32px; object-fit:cover; margin-right:8px;">
                            @endif

                            <i class="ti-angle-down text-white" style="font-size:12px; margin-right:6px;"></i>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right navbar-dropdown" aria-labelledby="profileDropdown"
                            style="background-color: #fff; border-radius: 8px; min-width: 160px;">

                            <a class="dropdown-item text-dark" href="{{ route('admin.logout') }}"
                                onclick="event.preventDefault(); document.getElementById('admin-logout-form').submit();">
                                <i class="ti-power-off text-primary"></i> تسجيل الخروج
                            </a>

                            <form id="admin-logout-form" action="{{ route('admin.logout') }}" method="POST"
                                class="d-none">
                                @csrf
                            </form>
                        </div>
                    </li>
                    @endguest




                </ul>
                <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
                    data-toggle="offcanvas">
                    <span class="icon-menu"></span>
                </button>
            </div>
        </nav>

        <!-- partial -->
        <div class="container-fluid page-body-wrapper">
            <nav class="sidebar sidebar-offcanvas" id="sidebar">
                <ul class="nav">


                    @permission(\App\Permissions\Permission::DASHBOARD_VIEW)
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('admin.index') }}">
                            <i class="icon-grid menu-icon"></i>
                            <span class="menu-title">Dashboard</span>
                        </a>
                    </li>
                    @endpermission

                    @anypermission([\App\Permissions\Permission::EMPLOYEES_VIEW, \App\Permissions\Permission::EMPLOYEES_CREATE])
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#employeesMenu" aria-controls="employeesMenu"
                            data-menu="employees">
                            <i class="mdi mdi-account-multiple menu-icon"></i>
                            <span class="menu-title">الموظفين</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="employeesMenu">
                            <ul class="nav flex-column sub-menu">
                                @permission(\App\Permissions\Permission::EMPLOYEES_CREATE)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.employees.create') }}">إضافة موظف</a>
                                </li>
                                @endpermission
                                @permission(\App\Permissions\Permission::EMPLOYEES_VIEW)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.employees.index') }}">كل الموظفين</a>
                                </li>
                                @endpermission
                            </ul>
                        </div>
                    </li>
                    @endanypermission

                    @anypermission([\App\Permissions\Permission::CATEGORIES_VIEW, \App\Permissions\Permission::CATEGORIES_CREATE])
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#categoriesMenu" aria-controls="categoriesMenu"
                            data-menu="categories">
                            <i class="mdi mdi-folder-multiple-outline menu-icon"></i>
                            <span class="menu-title">الأقسام</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse " id="categoriesMenu">
                            <ul class="nav flex-column sub-menu">
                                @permission(\App\Permissions\Permission::CATEGORIES_CREATE)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.categories.create') }}">إضافة قسم جديد</a>
                                </li>
                                @endpermission
                                @permission(\App\Permissions\Permission::CATEGORIES_VIEW)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.categories.index') }}">كل الأقسام</a>
                                </li>
                                @endpermission
                            </ul>
                        </div>
                    </li>
                    @endanypermission

                    @anypermission([\App\Permissions\Permission::PRODUCTS_VIEW, \App\Permissions\Permission::PRODUCTS_CREATE])
                    <li class="nav-item ">
                        <a class="nav-link" data-toggle="collapse" href="#productsMenu" aria-controls="productsMenu"
                            data-menu="products">
                            <i class="icon-bag menu-icon"></i>
                            <span class="menu-title">المنتجات</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="productsMenu">
                            <ul class="nav flex-column sub-menu">
                                @permission(\App\Permissions\Permission::PRODUCTS_CREATE)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.products.create') }}">إضافة منتج عادي</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link fw-bold" href="{{ route('admin.products.custom.create') }}">إضافة منتج مخصص</a>
                                </li>
                                @endpermission
                                @permission(\App\Permissions\Permission::PRODUCTS_VIEW)
                                <li class="nav-item">
                                    <a class="nav-link " href="{{ route('admin.products.index') }}">كل المنتجات</a>
                                </li>
                                @endpermission
                            </ul>
                        </div>
                    </li>
                    @endanypermission

                    @anypermission([\App\Permissions\Permission::DELETE_LOGS_VIEW, \App\Permissions\Permission::AUDIT_LOGS_VIEW])
                    <li class="nav-item ">
                        <a class="nav-link" data-toggle="collapse" href="#logsMenu" aria-controls="logsMenu"
                            data-menu="logs">
                            <i class="mdi mdi-clipboard-text menu-icon"></i>
                            <span class="menu-title">السجلات</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="logsMenu">
                            <ul class="nav flex-column sub-menu">
                                @permission(\App\Permissions\Permission::DELETE_LOGS_VIEW)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.delete_logs.index') }}">سجل الحذف</a>
                                </li>
                                @endpermission
                                @permission(\App\Permissions\Permission::AUDIT_LOGS_VIEW)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.audit_logs.index') }}">سجل النشط</a>
                                </li>
                                @endpermission
                            </ul>
                        </div>
                    </li>
                    @endanypermission

                    @anypermission([\App\Permissions\Permission::DESIGNS_VIEW, \App\Permissions\Permission::DESIGNS_CREATE])
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#designsMenu" aria-controls="designsMenu"
                            data-menu="designs">
                            <i class="mdi mdi-palette menu-icon"></i>
                            <span class="menu-title">التصميمات</span>
                            <i class="menu-arrow"></i>
                        </a>
                        <div class="collapse" id="designsMenu">
                            <ul class="nav flex-column sub-menu">
                                @permission(\App\Permissions\Permission::DESIGNS_CREATE)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.designs.create') }}">إضافة تصميم</a>
                                </li>
                                @endpermission
                                @permission(\App\Permissions\Permission::DESIGNS_VIEW)
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.designs.index') }}">مكتبة التصميمات</a>
                                </li>
                                @endpermission
                            </ul>
                        </div>
                    </li>
                    @endanypermission

                    @permission(\App\Permissions\Permission::ORDERS_VIEW)
                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#ordersMenu" aria-controls="ordersMenu">
                            <i class="mdi mdi-cart-plus menu-icon"></i>
                            <span class="menu-title">عمليات الشراء</span>
                            <i class="menu-arrow"></i>
                        </a>

                        <div class="collapse" id="ordersMenu">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('admin.orders.previousorder') }}">
                                        كل الطلبات
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link"
                                        href="{{ route('admin.orders.previousorder', ['status' => 'delivered']) }}">
                                        الطلبات المكتملة
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link "
                                        href="{{ route('admin.orders.previousorder', ['status' => 'active']) }}">
                                        قيد التنفيذ
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    @endpermission

                    <li class="nav-item">
                        <a class="nav-link" data-toggle="collapse" href="#homeMenu" aria-controls="homeMenu">
                            <i class="mdi mdi-home menu-icon"></i>
                            <span class="menu-title">الموقع الرئيسي</span>
                            <i class="menu-arrow"></i>
                        </a>

                        <div class="collapse" id="homeMenu">
                            <ul class="nav flex-column sub-menu">
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ url('/') }}">
                                        الذهاب إلى الموقع
                                    </a>
                                </li>
                            </ul>
                        </div>
                    </li>



                    {{-- //////// --}}

                </ul>
            </nav>




            <div class="main-panel" style="min-height: 100vh; display: flex; flex-direction: column;">
                <div class="content-wrapper" style="flex: 1;">
                    @yield('content')
                </div>
            </div>
        </div>



        <!-- page-body-wrapper ends -->
    </div>
    <!-- container-scroller -->

    <!-- plugins:js -->
    <script src="{{ asset('assets/admin/vendors/js/vendor.bundle.base.js') }}"></script>
    <!-- endinject -->
    <!-- Plugin js for this page -->
    <script src="{{ asset('assets/admin/vendors/chart.js/Chart.min.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/datatables.net/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('assets/admin/vendors/datatables.net-bs4/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('assets/admin/js/dataTables.select.min.js') }}"></script>

    <!-- End plugin js for this page -->
    <!-- inject:js -->
    <script src="{{ asset('assets/admin/js/off-canvas.js') }}"></script>
    <script src="{{ asset('assets/admin/js/hoverable-collapse.js') }}"></script>
    <script src="{{ asset('assets/admin/js/template.js') }}"></script>
    <script src="{{ asset('assets/admin/js/settings.js') }}"></script>
    <script src="{{ asset('assets/admin/js/todolist.js') }}"></script>
    <!-- endinject -->
    <!-- Custom js for this page-->
    <script src="{{ asset('assets/admin/js/dashboard.js') }}"></script>
    <script src="{{ asset('assets/admin/js/Chart.roundedBarCharts.js') }}"></script>
    <!-- End custom js for this page-->

</body>

</html>