@extends('layouts.master')
@section('content')



{{-- <div class="product-section mt-100 mb-100">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <div class="section-title">
                    <h3><span class="orange-text">منتجاتنا</span></h3>
                    <p>مجموعة متنوعة من المنتجات المتاحة حاليًا</p>
                </div>
            </div>
        </div>

        <div class="row">
            @foreach ($products as $item)
            <div class="col-lg-4 col-md-6 text-center d-flex mb-4">
                <div class="single-product-item flex-fill">
                    <a href="/single-product/{{ $item->id }}">
                        <div class="product-image"> <img src="{{ url($item->imagepath) }}"
                                style="width:250px; height:250px; object-fit:cover; border-radius:8px;" alt="">
                        </div>
                    </a>
                    <div class="product-info mt-2">
                        <div class="product-name">{{ $item->name }}</div>

                        <div class="product-description" id="desc-{{ $item->id }}">
                            {{ $item->description }}
                        </div>

                        <span class="read-more" id="read-more-{{ $item->id }}"
                            onclick="toggleDescription({{ $item->id }})">
                            اعرف أكثر <i class="fas fa-chevron-down small"></i>
                        </span>

                        <div class="product-price-section mt-">
                            <div class="product-quantity-label">السعر</div>
                            <div class="product-price">{{ $item->price }} ج.م</div>

                            <div class="product-quantity-label">الكمية المتاحة</div>
                            <div class="product-quantity-value">{{ $item->quantity }}</div>
                        </div>
                    </div>

                    <div class="product-actions mt-3">
                        <a href="/addproducttocart/{{ $item->id }}" class="cart-btn btn btn-success btn-sm"
                            style="width: 100%">
                            <i class="fas fa-shopping-cart"></i> إضافة إلى السلة
                        </a>

                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="row">
            <div class="col-lg-12 text-center" style="">
                <div class="pagination-wrap">
                    {{ $products->links('vendor.pagination.custom') }}
                </div>
            </div>
        </div>
    </div>

</div> --}}

<section class="bg0 p-t-23 p-b-140">
    <div class="container">

        <div class="container text-center p-b-50">
            <h2 class="section-title">
                منتجاتنا
            </h2>
            <p class="text-center">مجموعة متنوعة من المنتجات المتاحة حاليًا</p>
        </div>

        <div class="flex-w justify-content-end p-b-52">


            <div class="flex-w flex-c-m m-tb-10">
                <div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter">
                    <i class="icon-filter cl2 black m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
                    <i class="icon-close-filter cl2 black m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                    فلتر
                </div>

                <div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search">
                    <i class="icon-search cl2 black m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                    <i class="icon-close-search cl2 black m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                    بحث
                </div>
            </div>

            <!-- Search product -->
            <div class="dis-none panel-search w-full p-t-10 p-b-15" dir="rtl">
                <form action="/search" method="post">
                    <div class="bor8 dis-flex p-l-15">
                        @csrf
                        <button class="size-113 flex-c-m fs-16 cl2 black hov-cl1 trans-04">
                            <i class="zmdi zmdi-search"></i>
                        </button>
                        <input class="mtext-107 cl2 black size-114 plh2 p-r-15" type="text" name="searchkey"
                            placeholder="  أبحث عن منتج ..... ">
                </form>

            </div>
        </div>

        <!-- Filter -->
        <div class="dis-none panel-filter w-full p-t-10">
            <div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">
                <div class="filter-col1 p-r-15 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        Sort By
                    </div>

                    <ul>
                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                Default
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                Popularity
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                Average rating
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04 filter-link-active">
                                Newness
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                Price: Low to High
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                Price: High to Low
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="filter-col2 p-r-15 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        Price
                    </div>

                    <ul>
                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04 filter-link-active">
                                All
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                $0.00 - $50.00
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                $50.00 - $100.00
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                $100.00 - $150.00
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                $150.00 - $200.00
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="#" class="filter-link stext-106 trans-04">
                                $200.00+
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="filter-col3 p-r-15 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        Color
                    </div>

                    <ul>
                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #222;">
                                <i class="zmdi zmdi-circle"></i>
                            </span>

                            <a href="#" class="filter-link stext-106 trans-04">
                                Black
                            </a>
                        </li>

                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #4272d7;">
                                <i class="zmdi zmdi-circle"></i>
                            </span>

                            <a href="#" class="filter-link stext-106 trans-04 filter-link-active">
                                Blue
                            </a>
                        </li>

                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #b3b3b3;">
                                <i class="zmdi zmdi-circle"></i>
                            </span>

                            <a href="#" class="filter-link stext-106 trans-04">
                                Grey
                            </a>
                        </li>

                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #00ad5f;">
                                <i class="zmdi zmdi-circle"></i>
                            </span>

                            <a href="#" class="filter-link stext-106 trans-04">
                                Green
                            </a>
                        </li>

                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #fa4251;">
                                <i class="zmdi zmdi-circle"></i>
                            </span>

                            <a href="#" class="filter-link stext-106 trans-04">
                                Red
                            </a>
                        </li>

                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: #aaa;">
                                <i class="zmdi zmdi-circle-o"></i>
                            </span>

                            <a href="#" class="filter-link stext-106 trans-04">
                                White
                            </a>
                        </li>
                    </ul>
                </div>

                <div class="filter-col4 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        Tags
                    </div>

                    <div class="flex-w p-t-4 m-r--5">
                        <a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
                            Fashion
                        </a>

                        <a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
                            Lifestyle
                        </a>

                        <a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
                            Denim
                        </a>

                        <a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
                            Streetstyle
                        </a>

                        <a href="#" class="flex-c-m stext-107 cl6 size-301 bor7 p-lr-15 hov-tag1 trans-04 m-r-5 m-b-5">
                            Crafts
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row isotope-grid" style="position: relative; height: 2582.35px;">
        @foreach ($products as $item)
        <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women"
            style="position: absolute; left: 0%; top: 0px;">
            <!-- Block2 -->
            <div class="block2">
                <div class="block2-pic hov-img0">
                    <img src="{{ url($item->imagepath) }}">

                    {{-- <a href="/addproducttocart/{{ $item->id }}"
                        class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1"
                        style="width: 80%">
                        إضافة إلى السلة
                    </a> --}}
                    <a href="/single-product/{{ $item->id }}"
                        class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                        عرض المنتج
                    </a>
                </div>

                <div class="block2-txt flex-w flex-t p-t-14">
                    <div class="block2-txt-child1 flex-col-l ">
                        <a href="/single-product/{{ $item->id }}"
                            class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                            {{ $item->name }}
                        </a>

                        <span class="stext-105 cl3">
                            {{ $item->price }} ج.م
                        </span>
                    </div>

                    <div class="block2-txt-child2 flex-r p-t-3">
                        {{ $item->quantity }}
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Load more -->
    <div class="flex-c-m flex-w w-full p-t-45">
        <a href="#" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
            Load More
        </a>
    </div>
    </div>
</section>


@endsection