@extends('layouts.master')
@section('content')




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

                {{-- SORT --}}
                <div class="filter-col1 p-r-15 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        ترتيب حسب
                    </div>

                    <ul>
                        <li class="p-b-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'low-high']) }}"
                                class="filter-link stext-106 trans-04 {{ request('sort') == 'low-high' ? 'filter-link-active' : '' }}">
                                السعر: من الأقل للأعلى
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'high-low']) }}"
                                class="filter-link stext-106 trans-04 {{ request('sort') == 'high-low' ? 'filter-link-active' : '' }}">
                                السعر: من الأعلى للأقل
                            </a>
                        </li>

                        <li class="p-b-6">
                            <a href="{{ request()->fullUrlWithQuery(['sort' => 'new']) }}"
                                class="filter-link stext-106 trans-04 {{ request('sort') == 'new' ? 'filter-link-active' : '' }}">
                                الأحدث
                            </a>
                        </li>
                    </ul>
                </div>

                {{-- PRICE --}}
                <div class="filter-col2 p-r-15 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        السعر
                    </div>

                    <ul>
                        <li class="p-b-6">
                            <a href="{{ request()->fullUrlWithQuery(['price' => null]) }}"
                                class="filter-link stext-106 trans-04 {{ !request('price') ? 'filter-link-active' : '' }}">
                                كل الأسعار
                            </a>
                        </li>

                        @foreach(['0-250','250-350','350-500','500+'] as $range)
                        <li class="p-b-6">
                            <a href="{{ request()->fullUrlWithQuery(['price' => $range]) }}"
                                class="filter-link stext-106 trans-04 {{ request('price') == $range ? 'filter-link-active' : '' }}">
                                {{ $range }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- COLORS --}}
                <div class="filter-col3 p-r-15 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        الألوان
                    </div>

                    <ul>

                        <li class="p-b-6">
                            <a href="{{ request()->fullUrlWithQuery(['color' => null]) }}"
                                class="filter-link stext-106 trans-04 {{ !request('color') ? 'filter-link-active' : '' }}">
                                كل الألوان
                            </a>
                        </li>

                        @foreach(['أسود','#222','ازرق','#4272d7','رمادي','#b3b3b3','أخضر','#00ad5f','أحمر','#fa4251','أبيض','#aaa']
                        as $index => $value)
                        @if($index % 2 == 0)
                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6"
                                style="color: {{ ['#222','#4272d7','#b3b3b3','#00ad5f','#fa4251','#aaa'][$index/2] }}">
                                <i class="zmdi zmdi-circle"></i>
                            </span>

                            <a href="{{ request()->fullUrlWithQuery(['color' => $value]) }}"
                                class="filter-link stext-106 trans-04 {{ request('color') == $value ? 'filter-link-active' : '' }}">
                                {{ $value }}
                            </a>
                        </li>
                        @endif
                        @endforeach

                    </ul>
                </div>

                {{-- CATEGORIES --}}
                <div class="filter-col4 p-b-27">
                    <div class="mtext-102 cl2 black p-b-15">
                        الأقسام
                    </div>

                    <div class="flex-w p-t-4 m-r--5">

                        {{-- ALL --}}
                        <a href="{{ url('/product') }}" class="flex-c-m stext-107 size-301 bor7 p-lr-15 trans-04 m-r-5 m-b-5
                   {{ request()->segment(2) == null ? 'filter-link-active' : 'cl6 hov-tag1' }}">
                            كل الأقسام
                        </a>

                        {{-- CATEGORIES --}}
                        @foreach($categories as $cat)
                        <a href="{{ url('/product/'.$cat->id) }}" class="flex-c-m stext-107 size-301 bor7 p-lr-15 trans-04 m-r-5 m-b-5
                   {{ request()->segment(2) == $cat->id ? 'filter-link-active' : 'cl6 hov-tag1' }}">
                            {{ $cat->name }}
                        </a>
                        @endforeach

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
                    @php
                    $selectedColor = request('color');

                    $variantImage = null;

                    if ($selectedColor && $item->productphotos) {
                    $variantImage = $item->productphotos
                    ->where('color', $selectedColor)
                    ->first();
                    }
                    @endphp

                    <img src="{{ url($variantImage->imagepath ?? $item->imagepath) }}">

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
    <div class="flex-c-m flex-w w-full p-t-38">
        {{ $products->links('vendor.pagination.custom') }}
    </div>


    </div>
</section>


@endsection