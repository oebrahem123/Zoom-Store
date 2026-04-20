@extends('layouts.master')
@section('content')
<style>
    .form-control {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background-color: #fff;
        font-size: 14px;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #f28123;
        outline: none;
        box-shadow: 0 0 0 2px rgba(242, 129, 35, 0.2);
    }

    select.form-control {
        cursor: pointer;
        appearance: none;
        -webkit-appearance: none;
        background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%23666' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6 9 12 15 18 9'%3e%3c/polyline%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: left 12px center;
        background-size: 16px;
    }
</style>


<section class="sec-product-detail bg0 p-t-65 p-b-60">
    <div class="container">

        @php
        $baseImages = [];
        $colorImages = [];

        if ($product->imagepath) {
        $baseImages[] = str_replace('\\', '/', $product->imagepath);
        }

        if ($product->productphotos) {
        foreach ($product->productphotos as $img) {
        $path = str_replace('\\', '/', $img->imagepath);

        if (!$path) {
        continue;
        }

        $normalizedColor = strtolower(trim((string) $img->color));

        // الصور بدون لون تعتبر صور اساسية
        if ($normalizedColor === '') {
        if (!in_array($path, $baseImages)) {
        $baseImages[] = $path;
        }
        continue;
        }

        if (!isset($colorImages[$normalizedColor])) {
        $colorImages[$normalizedColor] = [];
        }

        if (!in_array($path, $colorImages[$normalizedColor])) {
        $colorImages[$normalizedColor][] = $path;
        }
        }
        }

        if (empty($baseImages) && !empty($colorImages)) {
        $firstColorImages = reset($colorImages);
        $baseImages = is_array($firstColorImages) ? $firstColorImages : [];
        }
        @endphp

        <div class="row">

            <!-- الصور -->
            <div class="col-md-6 col-lg-7 p-b-30">
                <div class="p-l-25 p-r-30 p-lr-0-lg">

                    <div class="wrap-slick3 flex-sb flex-w">

                        <div class="wrap-slick3-dots"></div>

                        <div class="wrap-slick3-arrows flex-sb-m flex-w"></div>

                        <div class="slick3 gallery-lb">

                            @foreach($baseImages as $img)
                            <div class="item-slick3" data-thumb="{{ asset($img) }}">
                                <div class="wrap-pic-w pos-relative">
                                    <img src="{{ asset($img) }}" alt="IMG-PRODUCT">

                                    <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04"
                                        href="{{ asset($img) }}">
                                        <i class="fa fa-expand"></i>
                                    </a>
                                </div>
                            </div>
                            @endforeach

                        </div>

                    </div>

                </div>
            </div>

            <!-- التفاصيل -->
            <div class="col-md-6 col-lg-5 p-b-30">
                <div class="p-r-50 p-t-5 p-lr-0-lg text-right">

                    <h4 class="mtext-105 cl2 js-name-detail p-b-14 black">
                        {{ $product->name }}
                    </h4>

                    <span class="mtext-106 black cl2">
                        {{ $product->price }} ج.م
                    </span>
                    <p class="stext-102 cl3 p-t-23">
                        الكميه المتاحة : {{ $product->quantity }}
                    </p>
                    <p class="stext-102 cl3 p-t-23">
                        {{ $product->description }}
                    </p>

                    <div class="p-t-33">

                        <div class="flex-w flex-r-m p-b-10" dir="rtl">
                            <div class="size-203 flex-c-m respon6">
                                المقاس
                            </div>

                            <div class="size-204 respon6-next">
                                <div class="rs1-select2 bor8 bg0">
                                    <select id="sizeSelect" class="form-control" name="size"
                                        style="padding: 8px 12px; border-radius: 5px;">
                                        <option value=""> اختر المقاس </option>
                                        @foreach($product->variants->pluck('size')->unique() as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                        @endforeach
                                    </select>


                                    <!-- ده برا select -->
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- Color -->
                        <div class="flex-w flex-r-m p-b-10" dir="rtl">
                            <div class="size-203 flex-c-m respon6">
                                اللون
                            </div>

                            <div class="size-204 respon6-next">
                                <div class="rs1-select2 bor8 bg0">
                                    <select id="colorSelect" class="form-control" name="color"
                                        style="padding: 8px 12px; border-radius: 5px;">
                                        <option value=""> اختر اللون أولاً </option>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- الكمية + زر -->
                        {{-- <div class="flex-w flex-r-m p-b-10">
                            <div class="size-204 flex-w flex-m respon6-next">
                                <!-- زرار -->
                                <a href="/addproducttocart/{{ $product->id }}"
                                    class="flex-c-m stext-101 cl0 size-101 bg3 bor1 hov-btn3 p-lr-15">
                                    إضافة إلى السلة
                                </a>
                            </div>
                        </div> --}}
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" id="addToCartForm">
                            @csrf

                            <!-- hidden input للـ variant -->
                            <input type="hidden" name="variant_id" id="variant_id">

                            <button type="submit" class="zoom-btn m-t-20">
                                <span class="icon">→</span>
                                <span class="btn-text"> إضافة إلى السلة </span>
                                <span class="hover-bg"></span>
                            </button>
                        </form>
                    </div>

                </div>
            </div>

        </div>

        <!-- Tabs -->
        <div class="bor10 m-t-50 p-t-43 p-b-40">
            <!-- Tab01 -->
            <div class="tab01">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist" dir="rtl">
                    <li class="nav-item p-b-10">
                        <a class="nav-link active" data-toggle="tab" href="#description" role="tab"
                            aria-expanded="true"> وصف المنتج </a>
                    </li>

                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#information" role="tab" aria-expanded="false">
                            معلومات إضافية
                        </a>
                    </li>

                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#reviews" role="tab" aria-expanded="false">
                            التعليقات
                        </a>
                    </li>
                </ul>

                <!-- Tab panes -->
                <div class="tab-content p-t-43">
                    <!-- - -->
                    <div class="tab-pane fade active show" id="description" role="tabpanel" aria-expanded="true"
                        dir="rtl">
                        <div class="how-pos2 p-lr-15-md">
                            <p class="stext-102 cl6">
                                {{ $product->description }}
                            </p>
                        </div>
                    </div>

                    <!-- - -->
                    <div class="tab-pane fade" id="information" role="tabpanel" aria-expanded="false" dir="rtl">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <ul class="p-lr-28 p-lr-15-sm">
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            وزن
                                        </span>

                                        <span id="weight">--</span>
                                    </li>


                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            خامات
                                        </span>

                                        <span id="material">--</span>
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            الألوان المتاحة
                                        </span>

                                        {{ $product->variants->pluck('color')->unique()->implode('، ') }}
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            المقاسات
                                        </span>

                                        {{ $product->variants->pluck('size')->unique()->implode(', ') }}
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- - -->
                    <div class="tab-pane fade" id="reviews" role="tabpanel" aria-expanded="false">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <div class="p-b-30 m-lr-15-sm">

                                    <!-- عرض آراء العملاء الخاصة بهذا المنتج -->
                                    @forelse($product->reviews as $review)
                                    <div class="flex-w flex-t p-b-68" dir="rtl">
                                        <div class="wrap-pic-s size-109 bor0 of-hidden m-l-18 m-t-6">
                                            <img src="{{ asset('assets/frontend/images/user.png') }}" alt="AVATAR">
                                        </div>

                                        <div class="size-207">
                                            <div class="flex-w flex-sb-m p-b-17">
                                                <span class="mtext-107 cl2 black">
                                                    {{ $review->name }}
                                                </span>

                                                <span class="fs-18 cl11">
                                                    @php
                                                    $fullStars = floor($review->rating);
                                                    $halfStar = $review->rating - $fullStars >= 0.5;
                                                    @endphp

                                                    @for($i = 1; $i <= 5; $i++) @if($i <=$fullStars) <i
                                                        class="zmdi zmdi-star"></i>
                                                        @elseif($i == $fullStars + 1 && $halfStar)
                                                        <i class="zmdi zmdi-star-half"></i>
                                                        @else
                                                        <i class="zmdi zmdi-star-outline"></i>
                                                        @endif
                                                        @endfor
                                                </span>
                                            </div>

                                            <p class="stext-102 cl6" dir="rtl">
                                                {{ $review->message }}
                                            </p>

                                            <small class="stext-102 cl8" style="font-size: 12px;">
                                                {{ $review->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="alert alert-info text-center" dir="rtl"
                                        style="background: #f8f9fa; border: 1px solid #d1ecf1; color: #0c5460; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                        <i class="zmdi zmdi-comment-outline" style="font-size: 24px;"></i>
                                        <p style="margin-top: 10px; margin-bottom: 0;">لا توجد تعليقات على هذا المنتج
                                            بعد. كن أول من يقيّم!</p>
                                    </div>
                                    @endforelse

                                    <!-- Add review -->
                                    <form class="w-full" method="POST" action="{{ route('storeReview') }}"
                                        id="reviewForm">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                                        <h5 class="mtext-108 black cl2 p-b-7" dir="rtl">
                                            إضافة مراجعة
                                        </h5>

                                        <p class="stext-102 cl6" dir="rtl">
                                            لن يتم نشر عنوان بريدك الإلكتروني. الحقول المطلوبة مُشار إليها بعلامة *
                                        </p>

                                        <div class="flex-w flex-m p-t-50 p-b-23" dir="rtl">
                                            <span class="stext-102 cl3 m-l-16">
                                                ما هو تقييمك؟
                                            </span>

                                            <span class="wrap-rating fs-18 cl11 pointer" id="ratingStars">
                                                <i class="item-rating pointer zmdi zmdi-star-outline"
                                                    data-value="1"></i>
                                                <i class="item-rating pointer zmdi zmdi-star-outline"
                                                    data-value="2"></i>
                                                <i class="item-rating pointer zmdi zmdi-star-outline"
                                                    data-value="3"></i>
                                                <i class="item-rating pointer zmdi zmdi-star-outline"
                                                    data-value="4"></i>
                                                <i class="item-rating pointer zmdi zmdi-star-outline"
                                                    data-value="5"></i>
                                                <input type="hidden" name="rating" id="ratingValue" value="5">
                                            </span>
                                        </div>

                                        <div class="row p-b-25" dir="rtl">

                                            <div class="col-12 p-b-5">
                                                <label class="stext-102 cl3" for="message"> اكتب تقييمك <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="size-110 bor8 stext-102 cl2 black p-lr-20 p-tb-10"
                                                    id="message" name="message"
                                                    required>{{ old('message', session('review_data.message')) }}</textarea>
                                                @error('message')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="col-sm-6 p-b-5">
                                                <label class="stext-102 cl3" for="name">الاسم <span
                                                        class="text-danger">*</span></label>
                                                <input class="size-111 bor8 stext-102 black cl2 p-lr-20" id="name"
                                                    type="text" name="name"
                                                    value="{{ old('name', session('review_data.name')) }}" required>
                                                @error('name')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>

                                            <div class="col-sm-6 p-b-5">
                                                <label class="stext-102 cl3" for="email"> البريد الإلكتروني <span
                                                        class="text-danger">*</span></label>
                                                <input class="size-111 bor8 stext-102 cl2 black p-lr-20" id="email"
                                                    type="email" name="email"
                                                    value="{{ old('email', session('review_data.email')) }}" required>
                                                @error('email')
                                                <small class="text-danger">{{ $message }}</small>
                                                @enderror
                                            </div>
                                        </div>

                                        <button type="submit"
                                            class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">
                                            إرسال
                                        </button>
                                    </form>

                                    <!-- عرض رسائل النجاح أو الخطأ -->
                                    @if(session('success'))
                                    <div class="alert alert-success text-center" dir="rtl"
                                        style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 10px; margin-top: 20px;">
                                        <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
                                    </div>
                                    @endif

                                    @if($errors->any())
                                    <div class="alert alert-danger text-center" dir="rtl"
                                        style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 10px; margin-top: 20px;">
                                        <i class="zmdi zmdi-alert-circle"></i> يرجى التحقق من البيانات المدخلة
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>



<script>
    document.addEventListener("DOMContentLoaded", function () {

    const variants = @json($product->variants);
    const baseImages = @json(array_values($baseImages));
    const colorImages = @json($colorImages);

    const sizeSelect = document.getElementById('sizeSelect');
    const colorSelect = document.getElementById('colorSelect');
    const weightSpan = document.getElementById('weight');
    const materialSpan = document.getElementById('material');
    const wrapSlick3 = document.querySelector('.wrap-slick3');
    const slick3Container = wrapSlick3 ? wrapSlick3.querySelector('.slick3') : null;

    function normalizeColor(value) {
        return (value || '').toString().trim().toLowerCase();
    }

    function getSliderHtml(images) {
        return images.map(function (img) {
            const imageUrl = `{{ asset('') }}${img}`.replace(/([^:]\/)\/+/g, '$1');
            return `
                <div class="item-slick3" data-thumb="${imageUrl}">
                    <div class="wrap-pic-w pos-relative">
                        <img src="${imageUrl}" alt="IMG-PRODUCT">
                        <a class="flex-c-m size-108 how-pos1 bor0 fs-16 cl10 bg0 hov-btn3 trans-04" href="${imageUrl}">
                            <i class="fa fa-expand"></i>
                        </a>
                    </div>
                </div>
            `;
        }).join('');
    }

    function initSlick3() {
        if (!window.jQuery || !slick3Container) return;

        const $slick = window.jQuery(slick3Container);

        $slick.slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            fade: true,
            infinite: true,
            autoplay: false,
            autoplaySpeed: 6000,
            arrows: true,
            appendArrows: window.jQuery(wrapSlick3).find('.wrap-slick3-arrows'),
            prevArrow:'<button class="arrow-slick3 prev-slick3"><i class="fa fa-angle-left" aria-hidden="true"></i></button>',
            nextArrow:'<button class="arrow-slick3 next-slick3"><i class="fa fa-angle-right" aria-hidden="true"></i></button>',
            dots: true,
            appendDots: window.jQuery(wrapSlick3).find('.wrap-slick3-dots'),
            dotsClass:'slick3-dots',
            customPaging: function(slick, index) {
                var portrait = window.jQuery(slick.$slides[index]).data('thumb');
                return '<img src=" ' + portrait + ' "/><div class="slick3-dot-overlay"></div>';
            },
        });
    }

    function updateSlider(images) {
        if (!window.jQuery || !slick3Container || !images.length) return;

        const $slick = window.jQuery(slick3Container);

        if ($slick.hasClass('slick-initialized')) {
            $slick.slick('unslick');
        }

        slick3Container.innerHTML = getSliderHtml(images);
        initSlick3();
    }

    function resetProductImages() {
        updateSlider(baseImages);
    }

    sizeSelect.addEventListener('change', function () {

        let selectedSize = this.value;

        colorSelect.innerHTML = '<option value="">-- اختر اللون --</option>';
        weightSpan.innerText = '--';
        materialSpan.innerText = '--';
        resetProductImages();

        if (!selectedSize) return;

        let colors = variants
            .filter(v => v.size === selectedSize && v.color)
            .map(v => v.color);

        let uniqueColors = [...new Set(colors)];

        uniqueColors.forEach(color => {
            let option = document.createElement('option');
            option.value = color;
            option.text = color;
            colorSelect.appendChild(option);
        });
    });

    colorSelect.addEventListener('change', function () {

        let size = sizeSelect.value;
        let color = this.value;

        let variant = variants.find(v => v.size === size && v.color === color);

        if (variant) {
            weightSpan.innerText = variant.weight ? variant.weight + ' كجم' : 'غير محدد';
            materialSpan.innerText = variant.material || 'غير محدد';
        }

        const selectedColorKey = normalizeColor(color);

        if (!selectedColorKey) {
            resetProductImages();
            return;
        }

        const selectedColorImages = colorImages[selectedColorKey] || [];

        if (selectedColorImages.length) {
            updateSlider(selectedColorImages);
            return;
        }

        resetProductImages();
    });

});
document.addEventListener("DOMContentLoaded", function () {

    const variants = @json($product->variants);

    const sizeSelect = document.getElementById('sizeSelect');
    const colorSelect = document.getElementById('colorSelect');
    const variantInput = document.getElementById('variant_id');

    // لما يختار لون
    colorSelect.addEventListener('change', function () {

        let size = sizeSelect.value;
        let color = this.value;

        let variant = variants.find(v => v.size === size && v.color === color);

        if (variant) {
            variantInput.value = variant.id;
        } else {
            variantInput.value = '';
        }
    });

    // منع الإرسال لو مفيش اختيار
    document.getElementById('addToCartForm').addEventListener('submit', function(e) {

        const size = sizeSelect.value;
        const color = colorSelect.value;

        if (!size || !color) {
            e.preventDefault();
            alert('❌ يجب أختيار المقاس واللون قبل إضافة المنتج إلى السلة');
            return false;
        }

    });

});
</script>

@include('partials.related-products')







@endsection
