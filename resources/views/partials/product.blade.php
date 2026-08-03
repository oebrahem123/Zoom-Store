<section class="bg0 p-t-6 p-b-0">
    <div class="container my-5" dir="rtl">

        <!-- صف العنوان والزر العلوي -->
        <div class="row align-items-center position-relative mb-4">
            <div class="col-md-4 text-start d-none d-md-block">
                <a href="/product" class="custom-btn text-decoration-none d-inline-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        class="bi bi-arrow-right transition-arrow" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                    </svg>
                    <span>عرض جميع المنتجات</span>
                </a>
            </div>

            <div class="col-12 col-md-4 text-center">
                <h2 class="section-title">منتجاتنا</h2>
                <p>أحدث المنتجات المضافة</p>
            </div>

            <div class="col-md-4 d-none d-md-block"></div>
        </div>

        <!-- هنا المشكلة كانت محذوفة -->
        <div class="row isotope-grid" style="position: relative; height: 1721.6px;">
            @php
            $positions = [
            ['left' => '0%', 'top' => '0px'],
            ['left' => '25%', 'top' => '0px'],
            ['left' => '50%', 'top' => '0px'],
            ['left' => '75%', 'top' => '0px'],
            ['left' => '0%', 'top' => '430px'],
            ['left' => '25%', 'top' => '430px'],
            ['left' => '50%', 'top' => '430px'],
            ['left' => '75%', 'top' => '430px'],
            ];
            @endphp

            @foreach ($products->take(8) as $index => $item)
            @php
            $pos = $positions[$index] ?? ['left' => (($index % 4) * 25) . '%', 'top' => floor($index / 4) * 430 . 'px'];
            @endphp

            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item women"
                style="position: absolute; left: {{ $pos['left'] }}; top: {{ $pos['top'] }};">

                <div class="block2" dir="rtl">
                    <div class="block2-pic hov-img0">
                        <img src="{{ url($item->imagepath) }}" alt="{{ $item->name }}">

                        <a href="/single-product/{{ $item->id }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                            عرض المنتج
                        </a>
                    </div>

                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l">
                            <a href="/single-product/{{ $item->id }}"
                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                {{ $item->name }}
                            </a>
                            <span class="stext-105 cl3">
                                {{ $item->price }} ج.م
                            </span>
                        </div>
                        <div class="block2-txt-child2 flex-r p-t-3">
                            @auth
                            <a href="#"
                                class="btn-addwish-b2 dis-block pos-relative js-addwish-b2 {{ in_array($item->id, $wishlistProductIds ?? []) ? 'js-addedwish-b2' : '' }}"
                                data-product-id="{{ $item->id }}" tabindex="0">
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

        <!-- زر الانتقال السفلي -->
        <div class="row d-block d-md-none text-center m-t-20">
            <div class="col-12">
                <a href="/product"
                    class="custom-btn text-decoration-none d-inline-flex align-items-center gap-2 justify-content-center p-3 border rounded">
                    <span>عرض جميع المنتجات</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        class="bi bi-arrow-left transition-arrow" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                </a>
            </div>
        </div>

    </div>
</section>