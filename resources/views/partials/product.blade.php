<section class="bg0 p-t-6 p-b-0">
    <div class="container">

        <div class="text-center p-b-40">
            <h2 class="section-title">منتجاتنا</h2>
            <p>أحدث المنتجات المضافة</p>
        </div>

        <div class="row">
            @foreach ($products->take(8) as $item)
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35">

                <div class="block2" dir="rtl">
                    <div class="block2-pic hov-img0">
                        <img src="{{ url($item->imagepath) }}">

                        <a href="/single-product/{{ $item->id }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1">
                            عرض المنتج
                        </a>
                    </div>

                    <div class="block2-txt-child1 flex-col-l ">
                        <a href="/single-product/{{ $item->id }}"
                            class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                            {{ $item->name }}
                        </a>

                        <span class="stext-105 cl3">
                            {{ $item->price }} ج.م
                        </span>
                    </div>
                </div>

            </div>
            @endforeach
        </div>

        <!-- زر يوديك لصفحة المنتجات -->
        <div class="text-center">

            <a href="/product" class="zoom-btn " dir="ltr">

                <span class="btn-text om"> عرض كل المنتجات </span>

            </a>


        </div>

    </div>
</section>
