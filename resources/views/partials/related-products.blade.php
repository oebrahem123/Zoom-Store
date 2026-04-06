<div class="sec-banner bg0 p-t-80">
    <div class="container">
        <div class="container text-center p-b-50">
            <h2 class="section-title">
                منتجات ذات صله
            </h2>
            <p> </p>
        </div>
    </div>
</div>

<section class="bg0 p-t-23 p-b-140">
    <div class="container">
        <div class="row isotope-grid" style="position: relative; height: 2582.35px;">
            @foreach ($relatedProducts as $item)
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item "
                style="position: absolute; left: 0%; top: 0px;">
                <!-- Block2 -->
                <div class="block2">
                    <div class="block2-pic hov-img0">
                        <a href="/single-product/{{ $item->id }}">
                            <img src="{{ url($item->imagepath) }}">
                        </a>
                        <a href="/addproducttocart/{{ $item->id }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04 js-show-modal1"
                            style="width: 80%">
                            إضافة إلى السلة
                        </a>
                        {{-- <a href="/single-product/{{ $item->id }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                            عرض المنتج
                        </a> --}}
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
    </div>
</section>