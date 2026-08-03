@extends('layouts.master')
@section('content')

<section class="bg0 p-t-23 p-b-140">
    <div class="container">

        {{-- عنوان القسم --}}
        <div class="container text-center p-b-50">
            <h2 class="section-title">اختر المنتج وابدأ التصميم</h2>
            <p class="text-center">صمم منتجك بنفسك - اختر المنتج وابدأ الإبداع</p>
        </div>

        {{-- شريط الفلتر والبحث --}}
        <div class="flex-w flex-sb-m p-b-52" dir="rtl">

            {{-- أزرار الفلتر والبحث --}}
            <div class="flex-w flex-c-m m-tb-10">
                <div class="flex-c-m stext-106 cl6 size-104 bor4 pointer hov-btn3 trans-04 m-r-8 m-tb-4 js-show-filter"
                    dir="ltr">
                    <i class="icon-filter black cl2 m-r-6 fs-15 trans-04 zmdi zmdi-filter-list"></i>
                    <i class="icon-close-filter cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                    فلتر
                </div>

                <div class="flex-c-m stext-106 cl6 size-105 bor4 pointer hov-btn3 trans-04 m-tb-4 js-show-search"
                    dir="ltr">
                    <i class="icon-search black cl2 m-r-6 fs-15 trans-04 zmdi zmdi-search"></i>
                    <i class="icon-close-search cl2 m-r-6 fs-15 trans-04 zmdi zmdi-close dis-none"></i>
                    بحث
                </div>
            </div>

            {{-- مربع البحث --}}
            <div class="dis-none panel-search w-full p-t-10 p-b-15" dir="rtl">
                <form action="{{ route('design.search') }}" method="post">
                    @csrf
                    <div class="bor8 dis-flex p-l-15">
                        <button class="size-113 flex-c-m fs-16 cl2 hov-cl1 trans-04">
                            <i class="zmdi zmdi-search"></i>
                        </button>
                        <input class="mtext-107 cl2 size-114 plh2 p-r-15" type="text" name="searchkey"
                            placeholder="أبحث عن منتج ..... ">
                    </div>
                </form>
            </div>
        </div>

        {{-- لوحة الفلتر المتقدمة --}}
        <div class="dis-none panel-filter w-full p-t-10">
            <div class="wrap-filter flex-w bg6 w-full p-lr-40 p-t-27 p-lr-15-sm">

                {{-- ترتيب حسب --}}
                <div class="filter-col1 p-r-15 p-b-27">
                    <div class="mtext-102 black cl2 p-b-15">ترتيب حسب</div>
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

                {{-- السعر --}}
                <div class="filter-col2 p-r-15 p-b-27">
                    <div class="mtext-102 black cl2 p-b-15">السعر</div>
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

                {{-- الألوان --}}
                <div class="filter-col3 p-r-15 p-b-27">
                    <div class="mtext-102 black cl2 p-b-15">الألوان</div>
                    <ul>
                        <li class="p-b-6">
                            <a href="{{ request()->fullUrlWithQuery(['color' => null]) }}"
                                class="filter-link stext-106 trans-04 {{ !request('color') ? 'filter-link-active' : '' }}">
                                كل الألوان
                            </a>
                        </li>
                        @php
                        $colors = [
                        'أسود' => '#222',
                        'ازرق' => '#4272d7',
                        'رمادي' => '#b3b3b3',
                        'أخضر' => '#00ad5f',
                        'أحمر' => '#fa4251',
                        'أبيض' => '#aaa'
                        ];
                        @endphp
                        @foreach($colors as $name => $hex)
                        <li class="p-b-6">
                            <span class="fs-15 lh-12 m-r-6" style="color: {{ $hex }};">
                                <i class="zmdi zmdi-circle"></i>
                            </span>
                            <a href="{{ request()->fullUrlWithQuery(['color' => $name]) }}"
                                class="filter-link stext-106 trans-04 {{ request('color') == $name ? 'filter-link-active' : '' }}">
                                {{ $name }}
                            </a>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- الأقسام (Categories) --}}
                <div class="filter-col4 p-b-27">
                    <div class="mtext-102 black cl2 p-b-15">الأقسام</div>
                    <div class="flex-w p-t-4 m-r--5">
                        <a href="{{ url('/design') }}" class="flex-c-m stext-107 size-301 bor7 p-lr-15 trans-04 m-r-5 m-b-5
                                  {{ !request('catid') ? 'filter-link-active' : 'cl6 hov-tag1' }}">
                            كل الأقسام
                        </a>
                        @foreach($categories as $cat)
                        <a href="{{ request()->fullUrlWithQuery(['catid' => $cat->id]) }}" class="flex-c-m stext-107 size-301 bor7 p-lr-15 trans-04 m-r-5 m-b-5
                                  {{ request('catid') == $cat->id ? 'filter-link-active' : 'cl6 hov-tag1' }}">
                            {{ $cat->name }}
                        </a>
                        @endforeach
                    </div>
                </div>

            </div>
        </div>

        {{-- شبكة المنتجات --}}
        <div class="row isotope-grid" style="position: relative; height: auto;">
            @foreach ($products as $product)
            @php
            $variant = $product->variants->where('quantity', '>', 0)->first();
            $selectedColor = request('color');
            $variantImage = null;

            if ($selectedColor && $product->productphotos) {
            $variantImage = $product->productphotos->where('color', $selectedColor)->first();
            }
            @endphp

            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item">
                <div class="block2">
                    {{-- صورة المنتج --}}
                    <div class="block2-pic hov-img0">
                        <img src="{{ url($variantImage->imagepath ?? $product->imagepath) }}"
                            alt="{{ $product->name }}">

                        @if($variant)
                        <a href="{{ url('/design/'.$variant->id) }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                            ابدأ التصميم
                        </a>
                        @else
                        <button class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04"
                            style="cursor: not-allowed; opacity:0.6;">
                            غير متوفر حالياً
                        </button>
                        @endif
                    </div>

                    {{-- معلومات المنتج --}}

                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l ">
                            @if($variant)
                            <a href="{{ url('/design/'.$variant->id) }}"
                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                {{ $product->name }}
                            </a>
                            @else
                            <span class="stext-104 p-t-3 cl4 p-b-6">{{ $product->name }}</span>
                            @endif
                            <span class="stext-105 cl3">
                                {{ $product->price }} ج.م
                            </span>
                        </div>
                        <div class="block2-txt-child2 flex-r p-t-3">
                            @auth
                            <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2 {{ in_array($product->id, $wishlistProductIds ?? []) ? 'js-addedwish-b2' : '' }}"
                                data-product-id="{{ $product->id }}" tabindex="0">
                                <img class="icon-heart1 dis-block trans-04"
                                    src="{{ asset('assets/frontend/images/icons/icon-heart-01.png') }}" alt="ICON">
                                <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                    src="{{ asset('assets/frontend/images/icons/icon-heart-02.png') }}" alt="ICON">
                            </a>
                            @endauth
                        </div>

                    </div>


                </div>
            </div>
            @endforeach
        </div>

        {{-- الترقيم (Pagination) --}}
        <div class="flex-c-m flex-w w-full p-t-38">
            {{ $products->links('vendor.pagination.custom') }}
        </div>

    </div>
</section>

@endsection