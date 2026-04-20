<div class="container my-5">
    <div class="row g-3 align-items-stretch">

        {{-- LEFT BIG CATEGORY --}}
        @if($categories->count() > 0)
        @php $main = $categories[0]; @endphp

        <div class="col-lg-6 d-flex" dir="rtl">
            <a href="{{ route('prods', $main->id) }}"
                class="product-card main-card p-5 d-flex flex-column justify-content-between w-100 text-decoration-none">

                <div>
                    <h2>{{ $main->name }}</h2>
                    <p class="mt-3 text-muted">
                        {{ $main->description ?? 'تسوق الآن' }}
                    </p>
                </div>

                <div class="text-center">
                    <img src="{{ asset($main->imagepath) }}" class="main-img">
                </div>

                <span class="shop-now">▶ ابدأ التسوق</span>

            </a>
        </div>
        @endif

        {{-- RIGHT SIDE --}}
        <div class="col-lg-6 d-flex flex-column" dir="rtl">

            {{-- TOP CATEGORY --}}
            @if($categories->count() > 1)
            @php
            $top = $categories->where('name', 'كابات Zoom')->first();
            @endphp

            <div class="mb-3" style="flex:1.3;">
                <a href="{{ route('prods', $top->id) }}"
                    class="product-card smart-watch-card p-4 d-flex justify-content-between align-items-center h-100 text-decoration-none">

                    <div>
                        <h3>{{ $top->name }}</h3>
                        <p class="mt-2">{{ $top->description ?? '' }}</p>
                    </div>


                    <img src="{{ asset($top->imagepath) }}" class="side-img">

                </a>
            </div>
            @endif

            {{-- BOTTOM TWO --}}
            <div class="row g-3 flex-fill" dir="rtl">

                @foreach($categories->slice(2,2) as $item)
                <div class="col-md-6 d-flex">
                    <a href="{{ route('prods', $item->id) }}"
                        class="product-card sub-card p-4 d-flex flex-column justify-content-between w-100 text-decoration-none">

                        <div>
                            <span class="price-red">تسوق الآن</span>
                        </div>

                        <img src="{{ asset($item->imagepath) }}" class="mini-img my-3">

                        <p class="product-name">{{ $item->name }}</p>

                    </a>
                </div>
                @endforeach

            </div>

        </div>

    </div>
</div>

@if($categories->count() > 4)

<div class="text-center my-4">
    <button id="showMoreBtn" class="btn btn-outline-dark">
        المزيد من الأقسام ↓
    </button>
</div>

<div id="moreCategories" class="container" style="display:none;">
    <div class="row g-3">

        @foreach($categories->slice(4) as $item)
        <div class="col-md-3">
            <a href="{{ route('prods', $item->id) }}" class="product-card p-3 text-center d-block text-decoration-none">

                <img src="{{ asset($item->imagepath) }}" class="mini-img mb-2">

                <p class="product-name">{{ $item->name }}</p>
            </a>
        </div>
        @endforeach

    </div>
</div>

@endif

{{-- ************************** --}}
<div class="container my-5">
    <div class="row g-3 align-items-stretch">

        <!-- LEFT BIG CARD -->
        <div class="col-lg-6 d-flex">
            <div class="product-card main-card p-5 d-flex flex-column justify-content-between w-100">

                <div>
                    <h2>Bold Chronograph</h2>
                    <h2>Rose Dial Ladies</h2>
                    <h2>Watch</h2>

                    <p class="price-green mt-4">$450.00</p>
                </div>

                <div class="text-center">
                    <img src="{{ asset('assets/frontend/images/main-watch.jpg') }}" class="main-img">
                </div>

                <a href="#" class="shop-now">▶ SHOP NOW</a>

            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-6 d-flex flex-column">

            <!-- TOP ORANGE -->
            <div class="mb-3" style="flex: 1.3;">
                <div class="product-card smart-watch-card p-4 d-flex justify-content-between align-items-center h-100">

                    <div>
                        <h3>Red Motion</h3>
                        <h3>Stainless Steel</h3>
                        <h3>Smartwatch</h3>

                        <p class="price-white mt-3">$99.99</p>
                    </div>

                    <div class="play-btn">&#9655;</div>

                    <img src="{{ asset('assets/frontend/images/banner/G.png') }}" class="side-img">

                </div>
            </div>

            <!-- BOTTOM -->
            <div class="row g-3 flex-fill">

                <div class="col-md-6 d-flex">
                    <div class="product-card sub-card p-4 d-flex flex-column justify-content-between w-100">

                        <div>
                            <span class="price-red">$19.90</span>
                        </div>

                        <img src="https://via.placeholder.com/150" class="mini-img my-3">

                        <p class="product-name">Leather Shoulder Bag</p>

                    </div>
                </div>

                <div class="col-md-6 d-flex">
                    <div class="product-card sub-card p-4 d-flex flex-column justify-content-between w-100">

                        <div>
                            <span class="price-red">$199.00</span>
                        </div>

                        <img src="https://via.placeholder.com/150" class="mini-img my-3">

                        <p class="product-name">Adidas Red Sneaker</p>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
{{-- ***************************** --}}





{{-- الجذء القديم الى فى صفحه الهوم للأقسام --}}

<div class="sec-banner bg0">
    <div class="flex-w flex-c-m">
        @foreach ($categories->take(3) as $item)
        <div class="size-202 m-lr-auto respon4" dir="rtl">
            <!-- Block1 -->
            <div class="block1 wrap-pic-w om-o">
                <img class="im" src="{{ asset($item->imagepath) }}" alt="{{ $item->name }}">
                <a href="{{ route('prods', $item->id) }}"
                    class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                    <div class="block1-txt-child1 flex-col-l">
                        <span class="block1-name ltext-102 trans-04 p-b-8">
                            {{ $item->name }}
                        </span>
                        <span class="block1-info stext-102 trans-04">
                            {{ $item->description ?? 'تسوق الآن' }}
                        </span>
                    </div>
                    <div class="block1-txt-child2 p-b-4 trans-05">
                        <div class="block1-link stext-101 cl0 trans-09">
                            ابدأ التسوق
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>


    <div class="flex-c-m m-t-30 m-b-30">
        <button id="showMoreCategoriesBtn" class="btn-showmore">
            المزيد من الأقسام
            <i class="fa fa-chevron-right m-l-5"></i>
        </button>
    </div>


    <div id="moreCategories" style="display: none;">
        <div class="flex-w flex-c-m m-t-30">
            @foreach ($categories->skip(3) as $item)
            <div class="size-202 m-lr-auto respon4" dir="rtl">

                <div class="block1 wrap-pic-w om-o">
                    <img class="im" src="{{ asset($item->imagepath) }}" alt="{{ $item->name }}">
                    <a href="{{ route('prods', $item->id) }}"
                        class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                        <div class="block1-txt-child1 flex-col-l">
                            <span class="block1-name ltext-102 trans-04 p-b-8">
                                {{ $item->name }}
                            </span>
                            <span class="block1-info stext-102 trans-04">
                                {{ $item->description ?? 'تسوق الآن' }}
                            </span>
                        </div>
                        <div class="block1-txt-child2 p-b-4 trans-05">
                            <div class="block1-link stext-101 cl0 trans-09">
                                ابدأ التسوق
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>
