<!DOCTYPE html>
<html lang="ar">

<head>
    <title>HOM Store- Custom Hoodies</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="{{ asset('assets/frontend/images/logo/icon.png') }}" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/fonts/iconic/css/material-design-iconic-font.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/fonts/linearicons-v1.0.0/icon-font.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/vendor/animate/animate.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/vendor/css-hamburgers/hamburgers.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/vendor/animsition/css/animsition.min.css') }}">
    <!--===============================================================================================-->
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/vendor/daterangepicker/daterangepicker.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/vendor/slick/slick.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/vendor/MagnificPopup/magnific-popup.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/vendor/perfect-scrollbar/perfect-scrollbar.css') }}">
    <!--===============================================================================================-->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@300;400;500;700&display=swap" rel="stylesheet">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/util.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/css/main.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="/vendor/select2/select2.min.css">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

</head>


<body>
    <style>
        /* زر تابع طلبك */
        .floating-track-btn {
            position: fixed;
            bottom: 25px;
            right: 25px;
            /* لليمين */
            background-color: #f28123;
            color: #fff;
            padding: 7px 24px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            transition: all 0.25s ease-in-out;
            animation: pulseAnim 1.8s infinite;
        }

        .floating-track-btn i {
            font-size: 18px;
        }

        .floating-track-btn:hover {
            transform: scale(1.07);
            background-color: #e57a00;
            color: #eee;
        }

        /* أنيميشن */
        @keyframes pulseAnim {
            0% {
                box-shadow: 0 0 0 0 rgba(255, 140, 0, 0.6);
            }

            70% {
                box-shadow: 0 0 0 18px rgba(255, 140, 0, 0);
            }

            100% {
                box-shadow: 0 0 0 0 rgba(255, 140, 0, 0);
            }
        }

        /* الإنتهاء من زر تابع طلبك */
    </style>

    <body class="animsition">

        <!-- header -->
        <header>
            <div class="container-menu-desktop">

                <!-- Topbar -->
                <div class="top-bar" dir="rtl">
                    <div class="content-topbar flex-sb-m h-full container">
                        <div class="left-top-bar">
                            استمتع بشحن مجاني عند شراء 3 منتجات أو أكثر.
                        </div>
                        <div class="right-top-bar flex-w h-full">
                            <a href="#" onclick="setLang('en')" class="flex-c-m trans-04 p-lr-25">
                                EN
                            </a>
                            <a href="#" onclick="setLang('ar')" class="flex-c-m trans-04 p-lr-25">
                                AR
                            </a>
                        </div>
                    </div>
                </div>

                <div class="wrap-menu-desktop">
                    <nav class="limiter-menu-desktop container">

                        <!-- Logo -->
                        <a href="/" class="logo">
                            <img class="logo-dark" src="{{ asset('assets/frontend/images/logo/logo-black.png') }}">
                        </a>

                        <!-- Menu -->
                        <div class="menu-desktop">
                            <ul class="main-menu" dir="rtl">

                                {{-- LOGIN --}}
                                <li class="mobile-hide user-account-section">

                                    @guest
                                    <!-- المستخدم غير مسجل -->
                                    <a class="nav-link dropdown-toggle position-relative" href="#" id="guestDropdown"
                                        role="button" data-bs-toggle="dropdown"
                                        style="display: flex; align-items: center; gap: 8px; color: #fff; text-decoration: none;">

                                        <div class="guest-icon"
                                            style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #edb8f3 0%, #F28123 100%); display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="fa fa-user" style="font-size: 14px;"></i>
                                        </div>

                                        <span class="li-" style=" font-weight: 600;">حسابي</span>
                                    </a>

                                    <ul class="sub-menu guest-dropdown" aria-labelledby="guestDropdown"
                                        style="min-width: 220px; padding:10px; border-radius:10px;">

                                        <li
                                            style="padding:10px; background:#f8f9fa; border-radius:10px; text-align:right;">
                                            <div style="font-weight:bold;">مرحباً بك!</div>
                                            <small>سجل دخول للاستفادة من المميزات</small>
                                        </li>

                                        <li>
                                            <a href="{{ route('login') }}" class="dropdown-item" style="color:#28a745;">
                                                <i class="fa fa-sign-in"></i> تسجيل الدخول
                                            </a>
                                        </li>

                                        <li>
                                            <a href="{{ route('register') }}" class="dropdown-item"
                                                style="color:#007bff;">
                                                <i class="fa fa-user-plus"></i> مستخدم جديد
                                            </a>
                                        </li>

                                    </ul>
                                    @endguest


                                    @auth
                                    <!-- المستخدم مسجل -->
                                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                        data-bs-toggle="dropdown"
                                        style="display: flex; align-items: center; gap: 8px; color: #fff;">

                                        <div class="guest-icon"
                                            style="width: 32px; height: 32px; border-radius: 50%; background: #F28123; display: flex; align-items: center; justify-content: center; color: white;">
                                            <i class="fa fa-user"></i>
                                        </div>

                                        <span class="li-" style=" font-weight: 600;">
                                            {{ Auth::user()->name }}
                                        </span>
                                    </a>

                                    <ul class="sub-menu" aria-labelledby="userDropdown"
                                        style="min-width: 200px; padding:10px; border-radius:10px;">

                                        <li>
                                            <a href="/profile" class="dropdown-item">
                                                <i class="fa fa-user"></i> حسابي
                                            </a>
                                        </li>

                                        <li>
                                            <a href="/cart" class="dropdown-item">
                                                <i class="fa fa-shopping-cart"></i> السلة
                                            </a>
                                        </li>

                                        <li>
                                            <form method="POST" action="{{ route('logout') }}">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-danger">
                                                    <i class="fa fa-sign-out"></i> تسجيل الخروج
                                                </button>
                                            </form>
                                        </li>

                                    </ul>
                                    @endauth

                                </li>

                                <li class="{{ request()->is('/') ? 'active-menu' : '' }}">
                                    <a href="/">الرئيسية</a>
                                </li>

                                <li class="{{ request()->is('product') ? 'active-menu' : '' }}">
                                    <a href="{{ route('prods') }}">المتجر</a>
                                </li>

                                <li class="{{ request()->is('category') ? 'active-menu' : '' }}">
                                    <a href="{{ route('cats') }}">الأقسام</a>
                                </li>

                                <li>
                                    <a href="/reviews">أراء العملاء</a>
                                </li>

                            </ul>
                        </div>

                        <!-- Icons -->
                        <div class="wrap-icon-header flex-w flex-r-m">

                            <!-- Dark Mode -->
                            <div class="icon-header-item">
                                <i class="fa fa-moon-o theme-btn"></i>
                            </div>

                            <!-- Search -->
                            <div class="icon-header-item js-show-modal-search">
                                <i class="zmdi zmdi-search"></i>
                            </div>

                            <!-- Cart -->
                            {{-- @auth
                            @php
                            $cartCount = \App\Models\Cart::where('user_id', auth()->id())->sum('quantity');
                            @endphp
                            @endauth

                            <a href="/cart" class="icon-header-item icon-header-noti"
                                data-notify="{{ $cartCount ?? 0 }}">
                                <i class="zmdi zmdi-shopping-cart"></i>
                            </a> --}}

                            <!-- Cart -->

                            <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart"
                                data-notify="{{ $cartCount }}">
                                <i class="zmdi zmdi-shopping-cart"></i>
                            </div>
                        </div>

                    </nav>
                </div>
            </div>

            <!-- Mobile -->
            <!-- Header Mobile -->
            <div class="wrap-header-mobile">

                <!-- Logo mobile -->
                <div class="logo-mobile">
                    <a href="/" class="logo">
                        <img class="logo-light" src="{{ asset('assets/frontend/images/logo/logo-white.png') }}">
                        <img class="logo-dark" src="{{ asset('assets/frontend/images/logo/logo-black.png') }}">
                    </a>
                </div>

                <!-- Icons -->
                <div class="wrap-icon-header flex-w flex-r-m">

                    <!-- Dark Mode -->
                    <div class="icon-header-item">
                        <i class="fa fa-moon-o theme-btn"></i>
                    </div>

                    <!-- Search -->
                    <div class="icon-header-item js-show-modal-search">
                        <i class="zmdi zmdi-search"></i>
                    </div>

                    <!-- Cart -->
                    <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart"
                        data-notify="{{ $cartCount ?? 0 }}">
                        <i class="zmdi zmdi-shopping-cart"></i>
                    </div>

                </div>

                <!-- Button menu -->
                <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
                    <span class="hamburger-box">
                        <span class="hamburger-inner"></span>
                    </span>
                </div>
            </div>


            <!-- Mobile Menu -->
            <div class="menu-mobile">

                <ul class="topbar-mobile" dir="rtl">
                    <li>
                        <div class="right-top-bar">
                            الشحن مجاني عند شراء 3 منتجات فأكثر.
                        </div>
                    </li>
                </ul>

                <ul class="main-menu-m" dir="rtl">

                    <li>
                        <a href="/">الرئيسية</a>
                    </li>

                    <li>
                        <a href="{{ route('prods') }}">المتجر</a>
                    </li>

                    <li>
                        <a href="/cart">عمليات الشراء</a>
                    </li>

                    <li>
                        <a href="{{ route('cats') }}">الأقسام</a>
                    </li>

                    <li>
                        <a href="/reviews">آراء العملاء</a>
                    </li>

                    <!-- LOGIN -->
                    @guest
                    <li>
                        <a href="{{ route('login') }}">تسجيل الدخول</a>
                    </li>
                    <li>
                        <a href="{{ route('register') }}">إنشاء حساب</a>
                    </li>
                    @endguest

                    @auth
                    <li>
                        <a href="/profile">حسابي</a>
                    </li>
                    <li>
                        <a href="/cart">السلة</a>
                    </li>
                    <li>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" style="background:none;border:none;color:red;">
                                تسجيل الخروج
                            </button>
                        </form>
                    </li>
                    @endauth

                </ul>
            </div>


            <!-- Search Modal -->
            <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
                <div class="container-search-header">

                    <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
                        <img src="{{ asset('assets/frontend/images/icons/icon-close2.png') }}">
                    </button>

                    <form action="/search" method="POST" class="wrap-search-header flex-w p-l-15">
                        @csrf
                        <button class="flex-c-m trans-04">
                            <i class="zmdi zmdi-search"></i>
                        </button>

                        <input class="plh3" type="text" name="searchkey" dir="rtl"
                            placeholder="يمكنك البحث عن أي منتج هنا ... ">
                    </form>

                </div>
            </div>

        </header>
        <!-- end header -->





        <!-- Sidebar -->
        <aside class="wrap-sidebar js-sidebar">
            <div class="s-full js-hide-sidebar"></div>

            <div class="sidebar flex-col-l p-t-22 p-b-25">
                <div class="flex-r w-full p-b-30 p-r-27">
                    <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-sidebar">
                        <i class="zmdi zmdi-close"></i>
                    </div>
                </div>


            </div>
        </aside>

        <!-- Cart -->
        <div class="wrap-header-cart js-panel-cart">
            <div class="s-full js-hide-cart"></div>

            <div class="header-cart flex-col-l p-l-25 p-r-50">

                <div class="header-cart-title flex-w flex-sb-m p-b-8">
                    <span class="mtext-103 cl2 text-">
                        عمليات الشراء
                    </span>

                    <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                        <i class="zmdi zmdi-close"></i>
                    </div>
                </div>

                <div class="header-cart-content flex-w js-pscroll"
                    style="position: relative;overflow: hidden;direction: rtl;">

                    <ul class="header-cart-wrapitem w-full">

                        @auth
                        @php
                        $cartItems = \App\Models\Cart::with('product')->where('user_id', auth()->id())->get();
                        $total = 0;
                        @endphp

                        @forelse($cartItems as $item)
                        @php
                        $total += $item->product->price * $item->quantity;
                        @endphp

                        <li class="header-cart-item flex-w flex-t m-b-12" style="position:relative;">

                            <div class="header-cart-item-img"
                                onclick="document.getElementById('delete-{{ $item->id }}').submit();">

                                <img src="{{ asset($item->product->imagepath) }}" alt="IMG">

                                <!-- form مخفي -->
                                <form id="delete-{{ $item->id }}" action="{{ route('cart.delete', $item->id) }}"
                                    method="POST">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </div>

                            <!-- تفاصيل المنتج -->
                            <div class="header-cart-item-txt p-t-8" style="padding-right:10px;">

                                <!-- اسم المنتج -->
                                <a href="{{ route('product.details', $item->product->id) }}"
                                    class="header-cart-item-name m-b-6 hov-cl1 trans-04">
                                    {{ $item->product->name }}
                                </a>

                                <!-- الكمية -->
                                <div>
                                    الكمية: {{ $item->quantity }}
                                </div>

                                <!-- السعر -->
                                <div>
                                    السعر: {{ $item->product->price }} ج
                                </div>



                            </div>

                        </li>
                        @empty
                        <p style="text-align:center">السلة فارغة</p>
                        @endforelse
                        @else
                        <p style="text-align:center">سجل دخول علشان تشوف السلة</p>
                        @endauth

                    </ul>

                    <div class="w-full">

                        <div class="header-cart-total w-full p-tb-40">
                            الإجمالي: {{ $total ?? 0 }} ج
                        </div>

                        <div class="header-cart-buttons flex-w w-full">

                            <a href="/cart"
                                class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                                عرض السلة
                            </a>

                            <a href="/checkout"
                                class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                                الدفع
                            </a>

                        </div>

                    </div>

                </div>
            </div>
        </div>

        <!-- Slider -->
        <section class="section-slide">
            <div class="wrap-slick1 rs1-slick1">
                <div class="slick1">
                    <div class="item-slick1"
                        style="background-image: url({{ asset('assets/frontend/images/slider/2.png') }});">
                        <div class="container h-full">
                            <div class="flex-col-l-m h-full p-t-100 p-b-30">
                                <div class="layer-slick1" data-appear="fadeInDown" data-delay="0">
                                    <span class="ltext-202 cl2 respon2">
                                        اصنع ستايلك بنفسك
                                    </span>
                                </div>
                                <div class="layer-slick1" data-appear="fadeInUp" data-delay="800">
                                    <h2 class="ltext-104 cl2 p-t-19 p-b-43 respon1">
                                        صمم الهودي بطريقتك
                                    </h2>
                                </div>
                                <div class="layer-slick1 animated visible-false" data-appear="zoomIn" data-delay="1600">
                                    <a href="product.html" class="zoom-btn">
                                        <span class="icon">→</span>
                                        <span class="btn-text">ابدأ التصميم</span>
                                        <span class="hover-bg"></span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="item-slick1"
                        style="background-image: url({{ asset('assets/frontend/images/slider/4.png') }});">
                        <div class="container h-full">
                            <div class="flex-col-l-m h-full p-t-100 p-b-30">
                                <div class="layer-slick1 animated visible-false" data-appear="rollIn" data-delay="0">
                                    <span class="ltext-202 cl2 respon2">
                                        تصميمك على تيشيرتك
                                    </span>
                                </div>

                                <div class="layer-slick1 animated visible-false" data-appear="lightSpeedIn"
                                    data-delay="800">
                                    <h2 class="ltext-104 cl2 p-t-19 p-b-43 respon1">
                                        اطبع فكرتك على التيشيرت
                                    </h2>
                                </div>
                                <div class="layer-slick1 animated visible-false" data-appear="slideInUp"
                                    data-delay="1600">
                                    <a href="product.html" class="zoom-btn">
                                        <span class="icon">→</span>
                                        <span class="btn-text">ابدأ التصميم</span>
                                        <span class="hover-bg"></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="item-slick1"
                        style="background-image: url({{ asset('assets/frontend/images/slider/3.png') }});">
                        <div class="container h-full">
                            <div class="flex-col-l-m h-full p-t-100 p-b-30">
                                <div class="layer-slick1 animated visible-false" data-appear="rotateInDownLeft"
                                    data-delay="0">
                                    <span class="ltext-202 cl2 respon2">
                                        صمم – اطبع – استلم

                                    </span>
                                </div>

                                <div class="layer-slick1 animated visible-false" data-appear="rotateInUpRight"
                                    data-delay="800">
                                    <h2 class="ltext-104 cl2 p-t-19 p-b-43 respon1">
                                        تصميمك يوصل لحد باب بيتك
                                    </h2>
                                </div>
                                <div class="layer-slick1 animated visible-false" data-appear="rotateIn"
                                    data-delay="1600">
                                    <a href="product.html" class="zoom-btn">
                                        <span class="icon">→</span>
                                        <span class="btn-text">ابدأ التصميم</span>
                                        <span class="hover-bg"></span>
                                    </a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>



        @yield('content')


































        <!-- Footer -->
        <footer class="bg3 p-t-75 p-b-32" dir="rtl">
            <div class="container">
                <div class="row">
                    <div class="col-sm-6 col-lg-3 p-b-50">
                        <h4 class="stext-301 cl0 p-b-30">
                            الأقسام
                        </h4>

                        <ul>
                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    الرئيسية
                                </a>
                            </li>

                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    المتجر
                                </a>
                            </li>

                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    عمليات الشراء
                                </a>
                            </li>

                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    المدونة
                                </a>
                            </li>
                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    من نحن
                                </a>
                            </li>
                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    تواصل معنى
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-sm-6 col-lg-3 p-b-50">
                        <h4 class="stext-301 cl0 p-b-30">
                            المساعدة
                        </h4>

                        <ul>
                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    الأسئلة الشائعة
                                </a>
                            </li>

                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    الإرجاع
                                </a>
                            </li>

                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    الشحن
                                </a>
                            </li>

                            <li class="p-b-10">
                                <a href="#" class="stext-107 cl7 hov-cl1 trans-04">
                                    الدعم
                                </a>
                            </li>
                        </ul>
                    </div>

                    <div class="col-sm-6 col-lg-3 p-b-50">
                        <h4 class="stext-301 cl0 p-b-30">
                            تواصل معنا
                        </h4>

                        <p class="stext-107 cl7 size-201">
                            يمكنك التواصل معنى من خلال الأرقام التالية: <br>
                            01023196780<br>
                            01020721186<br>
                            أو من خلال السويشل ميديا
                        </p>

                        <div class="p-t-27">
                            <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
                                <i class="fa fa-facebook"></i>
                            </a>

                            <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
                                <i class="fa fa-instagram"></i>
                            </a>

                            <a href="#" class="fs-18 cl7 hov-cl1 trans-04 m-r-16">
                                <i class="fa fa-pinterest-p"></i>
                            </a>
                        </div>
                    </div>

                    <div class="col-sm-6 col-lg-3 p-b-50">
                        <h4 class="stext-301 cl0 p-b-30">
                            اشترك في النشرة البريدية
                        </h4>

                        <form>
                            <div class="wrap-input1 w-full p-b-4">
                                <input class="input1 bg-none plh1 stext-107 cl7" type="text" name="email"
                                    placeholder="oebrahem123@gmail.com">
                                <div class="focus-input1 trans-04"></div>
                            </div>

                            <div class="p-t-18">
                                <a href="product.html" class="zoom-btn" dir="ltr">
                                    <span class="icon">→</span>
                                    <span class="btn-text">أشترك </span>
                                    <span class="hover-bg"></span>
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="p-t-40">
                    <div class="flex-c-m flex-w p-b-18">
                        <a href="#" class="m-all-1">
                            <img src="{{ asset('assets/frontend/images/icons/icon-pay-01.png') }}" alt="ICON-PAY">
                        </a>

                        <a href="#" class="m-all-1">
                            <img src="{{ asset('assets/frontend/images/icons/icon-pay-02.png') }}" alt="ICON-PAY">
                        </a>

                        <a href="#" class="m-all-1">
                            <img src="{{ asset('assets/frontend/images/icons/icon-pay-03.png') }}" alt="ICON-PAY">
                        </a>

                        <a href="#" class="m-all-1">
                            <img src="{{ asset('assets/frontend/images/icons/icon-pay-04.png') }}" alt="ICON-PAY">
                        </a>

                        <a href="#" class="m-all-1">
                            <img src="{{ asset('assets/frontend/images/icons/icon-pay-05.png') }}" alt="ICON-PAY">
                        </a>
                    </div>

                    <p class="stext-107 cl6 txt-center" dir="ltr">
                        ©
                        <script>
                            document.write(new Date().getFullYear())
                        </script> Dashly. All Rights Reserved | Designed &
                        Developed by Dashly
                    </p>
                </div>
            </div>
        </footer>


        <!-- Back to top -->
        <div class="btn-back-to-top" id="myBtn">
            <span class="symbol-btn-back-to-top">
                <i class="zmdi zmdi-chevron-up"></i>
            </span>
        </div>
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

        <script src="{{ asset('assets/frontend/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
        <script src="{{ asset('assets/frontend/vendor/bootstrap/js/popper.js') }}"></script>
        <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/vendor/select2/select2.min.css') }}">
        <script src="{{ asset('assets/frontend/vendor/select2/select2.min.js') }}"></script>


        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

        <!--===============================================================================================-->
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/animsition/js/animsition.min.js') }}"></script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
        <!--===============================================================================================-->

        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/daterangepicker/moment.min.js') }}"></script>
        <script src="{{ asset('assets/frontend/vendor/daterangepicker/daterangepicker.js') }}"></script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/slick/slick.min.js') }}"></script>
        <script src="{{ asset('assets/frontend/js/slick-custom.js') }}"></script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/parallax100/parallax100.js') }}"></script>
        <script>
            $('.parallax100').parallax100();
        </script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/MagnificPopup/jquery.magnific-popup.min.js') }}"></script>
        <script>
            $('.gallery-lb').each(function () { // the containers for all your galleries
			$(this).magnificPopup({
				delegate: 'a', // the selector for gallery item
				type: 'image',
				gallery: {
					enabled: true
				},
				mainClass: 'mfp-fade'
			});
		});
        </script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/isotope/isotope.pkgd.min.js') }}"></script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/sweetalert/sweetalert.min.js') }}"></script>
        <script>
            $('.js-addwish-b2').on('click', function (e) {
			e.preventDefault();
		});

		$('.js-addwish-b2').each(function () {
			var nameProduct = $(this).parent().parent().find('.js-name-b2').html();
			$(this).on('click', function () {
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-b2');
				$(this).off('click');
			});
		});

		$('.js-addwish-detail').each(function () {
			var nameProduct = $(this).parent().parent().parent().find('.js-name-detail').html();

			$(this).on('click', function () {
				swal(nameProduct, "is added to wishlist !", "success");

				$(this).addClass('js-addedwish-detail');
				$(this).off('click');
			});
		});

		/*---------------------------------------------*/

		$('.js-addcart-detail').each(function () {
			var nameProduct = $(this).parent().parent().parent().parent().find('.js-name-detail').html();
			$(this).on('click', function () {
				swal(nameProduct, "is added to cart !", "success");
			});
		});
        </script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/vendor/perfect-scrollbar/perfect-scrollbar.min.js') }}"></script>
        <script>
            $('.js-pscroll').each(function () {
			$(this).css('position', 'relative');
			$(this).css('overflow', 'hidden');
			var ps = new PerfectScrollbar(this, {
				wheelSpeed: 1,
				scrollingThreshold: 1000,
				wheelPropagation: false,
			});

			$(window).on('resize', function () {
				ps.update();
			})
		});
        </script>
        <!--===============================================================================================-->
        <script src="{{ asset('assets/frontend/js/main.js') }}"></script>
        <!--==================================Language Script======================================-->
        <script>
            function setLang(lang) {
			localStorage.setItem("lang", lang);
			location.reload();
		}
        </script>
        <script src="/vendor/select2/select2.min.js"></script>

    </body>

</html>