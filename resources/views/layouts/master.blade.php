<!DOCTYPE html>
<html lang="ar">

<head>
    <title>@yield('title', 'Zoom-Store')</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--===============================================================================================-->
    <link rel="icon" type="image/png" href="{{ asset('assets\frontend\images\logo\New-Logo\small.png') }}" />
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/vendor/bootstrap/css/bootstrap.min.css') }}">
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css"
        href="{{ asset('assets/frontend/fonts/font-awesome-4.7.0/css/font-awesome.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/frontend/vendor/select2/select2.min.css') }}">
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
    <style>
        /* heder.blade.php only — scoped under .header-v4 */
        .header-v4 .main-menu>li>a,
        .header-v4 .main-menu>li:hover>a,
        .header-v4 .fix-menu-desktop .wrap-menu-desktop .main-menu>li>a,
        .header-v4 .fix-menu-desktop .wrap-menu-desktop .main-menu>li>a:hover {
            color: #000 !important;
        }

        .header-v4 .main-menu>li.active-menu>a,
        .header-v4 .fix-menu-desktop .main-menu>li.active-menu>a {
            color: var(--secondary-color) !important;
        }

        .header-v4 .icon-header-item,
        .header-v4 .icon-header-item:hover,
        .header-v4 .fix-menu-desktop .icon-header-item {
            color: #000 !important;
        }

        .header-v4 .cl2,
        .header-v4 .wrap-header-mobile .icon-header-item {
            color: #000 !important;
        }

        .header-v4 .nav-link.dropdown-toggle {
            color: #000 !important;
        }

        .header-v4 .main-menu>li>.sub-menu a {
            color: #555;
        }

        .btn-addwish-b2 .icon-heart1,
        .btn-addwish-b2 .icon-heart2 {
            filter: brightness(0) saturate(100%) invert(48%) sepia(89%) saturate(1744%) hue-rotate(350deg) brightness(100%) contrast(101%);
        }

        body.dark .header-v4 .main-menu>li>a,
        body.dark .header-v4 .main-menu>li:hover>a,
        body.dark .header-v4 .fix-menu-desktop .wrap-menu-desktop .main-menu>li>a,
        body.dark .header-v4 .fix-menu-desktop .wrap-menu-desktop .main-menu>li>a:hover {
            color: #000 !important;
        }

        body.dark .header-v4 .main-menu>li.active-menu>a,
        body.dark .header-v4 .fix-menu-desktop .main-menu>li.active-menu>a {
            color: var(--secondary-color) !important;
        }

        body.dark .header-v4 .icon-header-item,
        body.dark .header-v4 .icon-header-item:hover,
        body.dark .header-v4 .fix-menu-desktop .icon-header-item {
            color: #000 !important;
        }

        body.dark .header-v4 .nav-link.dropdown-toggle {
            color: #000 !important;
        }
    </style>
    <!--===============================================================================================-->
    <link rel="stylesheet" type="text/css" href="{{ asset('assets/css/zoom-loading.css') }}">
</head>

<body class="animsition">

    <header class="header-v4">
        <!-- Header desktop -->
        <div class="container-menu-desktop">
            <!-- Topbar -->
            <div class="top-bar" dir="rtl">
                <div class="content-topbar flex-sb-m h-full container">
                    <div class="left-top-bar">
                        الشحن مجاني عند شراء 3 منتجات فأكثر.
                    </div>
                </div>
            </div>

            <div class="wrap-menu-desktop how-shadow1" style="top: 40px;">
                <nav class="limiter-menu-desktop container">

                    <!-- Logo desktop -->
                    <a href="/" class="logo">
                        <img class="logo-dark" src="{{ asset('assets\frontend\images\logo\New-Logo\New.png') }}"
                            alt="LOGO">
                        <img class="logo-light" src="{{ asset('assets\frontend\images\logo\New-Logo\New.png') }}"
                            alt="LOGO">
                    </a>

                    <!-- Menu desktop -->
                    <div class="menu-desktop">
                        <ul class="main-menu" dir="rtl">

                            {{-- LOGIN --}}
                            <li class="mobile-hide user-account-section">

                                @guest
                                <!-- المستخدم غير مسجل -->
                                <a class="nav-link dropdown-toggle position-relative" href="#" id="guestDropdown"
                                    role="button" data-bs-toggle="dropdown"
                                    style="display: flex; align-items: center; gap: 8px;text-decoration: none;">

                                    <div class="guest-icon"
                                        style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; ">
                                        <x-user-avatar class="rounded-circle"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="User Icon" />
                                    </div>

                                    <span class="li-" style=" font-weight: 600;">حسابي</span>
                                </a>

                                <ul class="sub-menu guest-dropdown" aria-labelledby="guestDropdown"
                                    style="min-width: 220px; padding:10px; border-radius:10px;">

                                    <li style="padding:10px; background:#f8f9fa; border-radius:10px; text-align:right;">
                                        <div style="font-weight:bold;">مرحباً بك في موقعنا</div>
                                        <small>سجل دخول للاستفادة من المميزات</small>
                                    </li>

                                    <li>
                                        <a href="{{ route('login') }}" class="dropdown-item" style="color:#28a745;">
                                            <i class="fa fa-sign-in"></i> تسجيل الدخول
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ route('register') }}" class="dropdown-item" style="color:#007bff;">
                                            <i class="fa fa-user-plus"></i> إنشاء حساب
                                        </a>
                                    </li>

                                </ul>
                                @endguest


                                @auth
                                <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                                    data-bs-toggle="dropdown"
                                    style="display: flex; align-items: center; gap: 8px; color: #fff;">

                                    <div class="guest-icon"
                                        style="width: 32px; height: 32px; border-radius: 50%; display: flex; align-items: center; justify-content: center; ">
                                        <x-user-avatar :user="Auth::user()" class="rounded-circle"
                                            style="width: 100%; height: 100%; object-fit: cover;" alt="User Icon" />
                                    </div>

                                    <span class="li-" style=" font-weight: 600;">
                                        {{ Auth::user()->name }}
                                    </span>
                                </a>

                                <ul class="sub-menu" aria-labelledby="userDropdown"
                                    style="min-width: 200px; padding:10px; border-radius:10px;">

                                    <li>
                                        <a href="{{ route('profile') }}" class="dropdown-item">
                                            <i class="fa fa-user"></i>
                                            حسابي
                                        </a>

                                    </li>

                                    <li>
                                        <a href="{{ route('designs.my') }}" class="dropdown-item">
                                            <i class="fa fa-paint-brush"></i>
                                            تصميماتي
                                        </a>
                                    </li>

                                    <li>
                                        <a href="/cart" data-notify="{{ $cartCount ?? 0 }}" class="dropdown-item">
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

                            <li
                                class="{{ request()->routeIs('prods') || request()->routeIs('product.details') ? 'active-menu' : '' }}">
                                <a href="{{ route('prods') }}">المتجر</a>
                            </li>

                            <li class="{{ request()->routeIs('cats') ? 'active-menu' : '' }}">
                                <a href="{{ route('cats') }}">الأقسام</a>
                            </li>
                            <li
                                class="{{ request()->routeIs('design.*') || request()->routeIs('designs.*') ? 'active-menu' : '' }}">
                                <a href="{{ route('design.start') }}">صمم منتجك</a>
                            </li>
                            <li class="{{ request()->routeIs('reviews') ? 'active-menu' : '' }}">
                                <a href="{{ route('reviews') }}">آراء العملاء</a>
                            </li>

                        </ul>
                    </div>


                    <!-- Icon header -->
                    <div class="wrap-icon-header flex-w flex-r-m">

                        {{-- my account --}}
                        {{-- <div class="icon-header-item">
                            <a href=""
                                style="color: inherit; text-decoration: none;display: flex; align-items: center; gap: 8px;">
                                <i class="zmdi zmdi-account"></i>
                                <span class="" style=" font-weight: 600; font-size: 14px; "> تسجيل
                                    الدخول </span>
                            </a>
                        </div> --}}

                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 js-show-modal-search">
                            <i class="zmdi zmdi-search"></i>
                        </div>

                        <div class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti js-show-cart"
                            data-notify="{{ $cartCount ?? 0 }}">
                            <i class="zmdi zmdi-shopping-cart"></i>
                        </div>

                        <a href="{{ route('wishlist.index') }}"
                            class="icon-header-item cl2 hov-cl1 trans-04 p-l-22 p-r-11 icon-header-noti"
                            data-notify="{{ $wishlistCount ?? 0 }}">
                            <i class="zmdi zmdi-favorite-outline"></i>
                        </a>
                    </div>

            </div>
            </nav>
        </div>
        </div>

        <!-- Header Mobile -->
        <div class="wrap-header-mobile">
            <!-- Logo mobile -->
            <div class="logo-mobile">
                <a href="/">
                    <img class="logo-dark" src="{{ asset('assets\frontend\images\logo\New-Logo\New.png') }}" alt="LOGO">
                    <img class="logo-light" src="{{ asset('assets\frontend\images\logo\New-Logo\New.png') }}"
                        alt="LOGO">
                </a>
            </div>

            <!-- Icon header -->
            <div class="wrap-icon-header flex-w flex-r-m m-r-15">
                <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 js-show-modal-search">
                    <i class="zmdi zmdi-search"></i>
                </div>

                <div class="icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti js-show-cart"
                    data-notify="{{ $cartCount ?? 0 }}">
                    <i class="zmdi zmdi-shopping-cart"></i>
                </div>

                <a href="{{ route('wishlist.index') }}"
                    class="dis-block icon-header-item cl2 hov-cl1 trans-04 p-r-11 p-l-10 icon-header-noti"
                    data-notify="{{ $wishlistCount ?? 0 }}">
                    <i class="zmdi zmdi-favorite-outline"></i>
                </a>
            </div>

            <!-- Button show menu -->
            <div class="btn-show-menu-mobile hamburger hamburger--squeeze">
                <span class="hamburger-box">
                    <span class="hamburger-inner"></span>
                </span>
            </div>
        </div>

        <!-- Menu Mobile -->
        <div class="menu-mobile">
            <ul class="topbar-mobile" dir="rtl">
                <li>
                    <div class="left-top-bar">
                        استمتع بشحن مجاني عند شراء 3 منتجات أو أكثر.
                    </div>
                </li>

            </ul>

            <ul class="main-menu-m" dir="rtl">
                <li class="{{ request()->is('/') ? 'active-menu' : '' }}">
                    <a href="/">الرئيسية</a>
                </li>

                <li
                    class="{{ request()->routeIs('prods') || request()->routeIs('product.details') ? 'active-menu' : '' }}">
                    <a href="{{ route('prods') }}">المتجر</a>
                </li>

                <li class="{{ request()->routeIs('cats') ? 'active-menu' : '' }}">
                    <a href="{{ route('cats') }}">الأقسام</a>
                </li>

                <li
                    class="{{ request()->routeIs('design.*') || request()->routeIs('designs.*') ? 'active-menu' : '' }}">
                    <a href="{{ route('design.start') }}">صمم منتجك</a>
                </li>

                <li
                    class="{{ request()->routeIs('cart') || request()->routeIs('Completeorder') || request()->routeIs('orders.*') ? 'active-menu' : '' }}">
                    <a href="{{ route('cart') }}">عمليات الشراء</a>
                </li>

                <li class="{{ request()->routeIs('reviews') ? 'active-menu' : '' }}">
                    <a href="{{ route('reviews') }}">أراء العملاء</a>
                </li>

                <!-- LOGIN / REGISTER / LOGOUT -->
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
                    <a href="{{ route('profile') }}">حسابي</a>
                </li>
                <li>
                    <a href="/cart" data-notify="{{ $cartCount ?? 0 }}">السلة</a>
                </li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            style="background:none;border:none;color:red;cursor:pointer;width:100%;text-align:right;padding:8px 0;">
                            تسجيل الخروج
                        </button>
                    </form>
                </li>
                @endauth
            </ul>
        </div>

        <!-- Modal Search -->
        <div class="modal-search-header flex-c-m trans-04 js-hide-modal-search">
            <div class="container-search-header">
                <button class="flex-c-m btn-hide-modal-search trans-04 js-hide-modal-search">
                    <img src="{{ asset('assets/frontend/images/icons/icon-close2.png') }}" alt="CLOSE">
                </button>

                <form action="/search" method="POST" class="wrap-search-header flex-w p-l-15">
                    @csrf
                    <button class="flex-c-m trans-04">
                        <i class="zmdi zmdi-search"></i>
                    </button>
                    <input class="plh3" type="text" name="searchkey" dir="rtl"
                        placeholder="يمكنك البحث عن أي منتج هنا ...">
                </form>
            </div>
        </div>
    </header>

    <!-- Cart Sidebar -->
    <div class="wrap-header-cart js-panel-cart">
        <div class="s-full js-hide-cart"></div>

        <div class="header-cart flex-col-l p-l-25 p-r-50">
            <div class="header-cart-title flex-w flex-sb-m p-b-8">
                <span class="mtext-103 black cl2">
                    عمليات الشراء
                </span>

                <div class="fs-35 lh-10 cl2 p-lr-5 pointer hov-cl1 trans-04 js-hide-cart">
                    <i class="zmdi zmdi-close"></i>
                </div>
            </div>

            <div class="header-cart-content flex-w js-pscroll"
                style="position: relative; overflow: hidden; direction: rtl;">
                <ul class="header-cart-wrapitem w-full">
                    @auth
                    @forelse($headerCartItems as $item)
                    <li class="header-cart-item flex-w flex-t m-b-12 {{ !$item->isAvailable ? 'opacity-50' : '' }}"
                        style="position:relative;">

                        <div class="header-cart-item-img"
                            onclick="document.getElementById('delete-{{ $item->id }}').submit();">
                            <img src="{{ asset($item->display_image) }}" alt="{{ $item->display_name }}">
                            <!-- form مخفي -->
                            <form id="delete-{{ $item->id }}" action="{{ route('cart.delete', $item->id) }}"
                                method="POST">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>

                        <!-- تفاصيل المنتج -->
                        <div class="header-cart-item-txt p-t-8" style="padding-right:10px;">
                            @if($item->product_id)
                            @if(isset($item->design) && $item->design?->id)
                            <a
                                href="{{ route('design.edit', ['design' => $item->design->id, 'cart_item_id' => $item->id]) }}">
                                @else
                                <a href="{{ route('product.details', [
                                    'productid' => $item->product_id,
                                    'size' => $item->size,
                                    'color' => $item->color,
                                    'cart_item_id' => $item->id
                                ]) }}">
                                    @endif
                                    <span class="{{ !$item->isAvailable ? 'text-decoration-line-through' : '' }}"
                                        style="display:inline-block;">
                                        {{ $item->display_name }}
                                    </span>
                                </a>
                                @else
                                <span
                                    class="text-danger {{ !$item->isAvailable ? 'text-decoration-line-through' : '' }}">
                                    {{ $item->display_name }}
                                </span>
                                @endif

                                @if(!$item->isAvailable)
                                <span class="d-block" style="font-size:11px;margin-top:4px;
                            color:{{ $item->availabilityStatus === 'out_of_stock' ? '#dc3545' : '#6c757d' }};">
                                    {{ $item->availabilityMessage }}
                                </span>
                                @endif

                                <div>المقاس : {{ $item->size ?? '—' }}</div>
                                <div>اللون : {{ $item->color ?? '—' }}</div>
                                <div>الكمية: {{ $item->quantity }}</div>
                                <div>السعر: {{ number_format($item->display_price, 2) }} ج</div>
                        </div>
                    </li>
                    @empty
                    <p style="text-align:center;padding:20px 0;">السلة فارغة</p>
                    @endforelse
                    @else
                    <p style="text-align:center;padding:20px 0;">سجل دخول علشان تشوف السلة</p>
                    @endauth
                </ul>

                <div class="w-full">
                    <div class="header-cart-total w-full p-tb-40">
                        الإجمالي: {{ number_format($headerCartTotal ?? 0, 2) }} ج
                    </div>

                    <div class="header-cart-buttons flex-w w-full">
                        <a href="/cart"
                            class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-r-8 m-b-10">
                            عرض السلة
                        </a>

                        <a href="{{ route('Completeorder') }}"
                            class="flex-c-m stext-101 cl0 size-107 bg3 bor2 hov-btn3 p-lr-15 trans-04 m-b-10">
                            الدفع
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>





    <!-- end search area -->



















    @yield('content')






    @include('layouts.partials.footer')

    <div class="floating-track-container">
        @auth
        @if($hasTrackableShipment)
        <a href="{{ route('orders.index') }}" class="floating-track-btn">
            <i class="fas fa-shipping-fast"></i>
            تتبّع طلبك
        </a>
        @endif
        @endauth
    </div>

    <!-- Back to top -->
    <div class="btn-back-to-top" id="myBtn">
        <span class="symbol-btn-back-to-top">
            <i class="zmdi zmdi-chevron-up"></i>
        </span>
    </div>


    <!--===============================================================================================-->
    <script src="{{ asset('assets/frontend/vendor/jquery/jquery-3.2.1.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/frontend/vendor/animsition/js/animsition.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/frontend/vendor/bootstrap/js/popper.js') }}"></script>
    <script src="{{ asset('assets/frontend/vendor/bootstrap/js/bootstrap.min.js') }}"></script>
    <!--===============================================================================================-->
    <script src="{{ asset('assets/frontend/vendor/select2/select2.min.js') }}"></script>
    <script>
        $(".js-select2").each(function () {
			$(this).select2({
				minimumResultsForSearch: 20,
				dropdownParent: $(this).next('.dropDownSelect2')
			});
		})
    </script>
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
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });

        $(document).on('click', '.js-addwish-b2', function (e) {
			e.preventDefault();
			var $btn = $(this);
			var productId = $btn.data('product-id');
			var isAdded = $btn.hasClass('js-addedwish-b2');
			var url = isAdded ? '/wishlist/remove/' + productId : '/wishlist/add/' + productId;
			var method = isAdded ? 'DELETE' : 'POST';

			$.ajax({
				url: url,
				type: method,
				dataType: 'json',
				success: function (res) {
					$btn.toggleClass('js-addedwish-b2');
					$('.icon-header-noti[href*="wishlist"]').attr('data-notify', res.wishlistCount);
				}
			});
		});

		/*---------------------------------------------*/

		window.ZoomStore = window.ZoomStore || {};
		ZoomStore.showAddToCartSuccess = function(productName, submitForm) {
			if (!submitForm) return;
			// Phase 1 — loading: original swal-modal, swal-title, swal-text, no icon, no buttons
			swal({
				title: productName,
				text: '\u062C\u0627\u0631\u064D \u0625\u0636\u0627\u0641\u0629 \u0627\u0644\u0645\u0646\u062A\u062C \u0625\u0644\u0649 \u0627\u0644\u0633\u0644\u0629...',
				buttons: false,
				closeOnClickOutside: false
			});
			// Phase 2 — after ~400ms: success icon animation (original swal-icon--success) → auto-close → submit
			setTimeout(function() {
				swal({
					title: productName,
					text: '\u062A\u0645\u062A \u0625\u0636\u0627\u0641\u0629 \u0627\u0644\u0645\u0646\u062A\u062C \u0625\u0644\u0649 \u0627\u0644\u0633\u0644\u0629 \u0628\u0646\u062C\u0627\u062D',
					icon: 'success',
					buttons: false,
					timer: 1000
				});
				setTimeout(function() { submitForm(); }, 1050);
			}, 400);
		};
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
    <script src="{{ asset('assets/js/zoom-loading.js') }}"></script>
    <script>
        // Global ZoomLoading: show on any "edit design" or "start design" link click
        $(document).on('click', 'a[href*="design.edit"], a[href*="design.start"]', function() {
            if (window.ZoomStore && ZoomStore.ZoomLoading) {
                ZoomStore.ZoomLoading.show({ message: '\u062C\u0627\u0631\u064A \u062A\u062D\u0645\u064A\u0644 \u0627\u0644\u0645\u062D\u0631\u0631...', allowClose: false });
            }
        });
        // Global ZoomLoading: show on checkout form submission
        $(document).on('submit', '#store-order', function() {
            if (window.ZoomStore && ZoomStore.ZoomLoading) {
                ZoomStore.ZoomLoading.show({ message: '\u062C\u0627\u0631\u064A \u062A\u0623\u0643\u064A\u062F \u0627\u0644\u0637\u0644\u0628...', allowClose: false });
            }
        });
    </script>
    @stack('scripts')
</body>

</html>