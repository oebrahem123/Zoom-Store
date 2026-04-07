@extends('layouts.master')
@section('content')



{{-- <div class="single-product-page mt-5">
    <div class="container">
        <!-- البطاقة الرئيسية للمنتج -->
        <div class="product-main-card">
            <div class="row align-items-center">
                <!-- قسم الصورة -->
                <div class="col-lg-6 pr-5">
                    <div class="product-image-section text-center">
                        <img src="{{ asset($product->imagepath) }}" alt="{{ $product->name }}"
                            class="main-product-image">
                    </div>
                </div>

                <!-- قسم المعلومات -->
                <div class="col-lg-6" style="text-align: right">
                    <div class="product-info-section">
                        <h1 class="product-title">{{ $product->name }}</h1>

                        <div class="product-category">
                            <i class="fas fa-tag"></i> {{ $product->category->name }}
                        </div>

                        <div class="product-price">
                            {{ $product->price }} <span class="currency">ج.م</span>
                        </div>

                        <div class="product-stock">
                            <strong>الكمية المتاحة : </strong>
                            <span class="{{ $product->quantity > 10 ? 'stock-available' : 'stock-low' }}">
                                {{ $product->quantity }} قطعة
                            </span>
                        </div>

                        <p class="product-description">
                            {{ $product->description }}
                        </p>

                        <!-- ميزات إضافية -->
                        <div class="product-features">
                            <div class="feature-item">
                                <i class="fas fa-shipping-fast"></i>
                                <span>شحن سريع خلال 24 ساعة</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-undo-alt"></i>
                                <span>إرجاع مجاني خلال 14 يوم</span>
                            </div>
                            <div class="feature-item">
                                <i class="fas fa-shield-alt"></i>
                                <span>ضمان الجودة لمدة عام</span>
                            </div>
                        </div>

                        <div class="single-product-form mt-4" style="text-align:left">
                            <a href="/addproducttocart/{{ $product->id }}" class="btn add-to-cart-btn">
                                <i class="fas fa-shopping-cart"></i> إضافة إلى السلة
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- الصور الإضافية -->
        @if($product->productphotos->count() > 0)
        <div class="additional-images-section">
            <h2 class="section-title-1">صور إضافية للمنتج</h2>
            <div class="row justify-content-center">
                @foreach ($product->productphotos as $item)
                <div class="col-xl-3 col-lg-4 col-md-6 mb-4">
                    <div class="additional-image-card" onclick="openImageModal('{{ asset($item->imagepath) }}')">
                        <div class="position-relative overflow-hidden">
                            <img src="{{ asset($item->imagepath) }}" alt="صورة إضافية للمنتج" class="additional-image">
                            <div class="image-overlay">
                                <span class="btn view-image-btn">
                                    <i class="fas fa-expand"></i> عرض الصورة
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>


<!-- مودال معدل -->
<div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">صورة المنتج</h5>
                <button type="button" class="custom-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <img id="modalImage" src="" alt="صورة مكبرة للمنتج" class="modal-image">
            </div>
            <!-- تم إخفاء الـ footer -->
        </div>
    </div>
</div>
--}}

<section class="sec-product-detail bg0 p-t-65 p-b-60">
    <div class="container">

        @php
        // دمج الصور بدون تكرار
        $images = [];

        if ($product->imagepath) {
        $images[] = str_replace('\\', '/', $product->imagepath);
        }

        if ($product->productphotos) {
        foreach ($product->productphotos as $img) {
        $path = str_replace('\\', '/', $img->imagepath);

        if (!in_array($path, $images)) {
        $images[] = $path;
        }
        }
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

                            @foreach($images as $img)
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
                                    <select class="js-select2" name="size">
                                        <option>اختر المقاس</option>
                                        <option>S</option>
                                        <option>M</option>
                                        <option>L</option>
                                        <option>XL</option>
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
                                    <select class="js-select2" name="color">
                                        <option>اختر اللون</option>
                                        <option>أحمر</option>
                                        <option>أزرق</option>
                                        <option>أسود</option>
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
                        <div class="p-t-18"
                            style=" align-items: center; justify-content: center; position: relative; display: flex;">
                            <a href=" /addproducttocart/{{ $product->id }}" class="zoom-btn" dir="ltr">
                                <span class="icon">→</span>
                                <span class="btn-text"> إضافة إلى السلة </span>
                                <span class="hover-bg"></span>
                            </a>
                        </div>
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

                                        <span class="stext-102 cl6 size-206">
                                            0.79 kg
                                        </span>
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            مقاس
                                        </span>

                                        <span class="stext-102 cl6 size-206">
                                            110 x 33 x 100 cm
                                        </span>
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            خامات
                                        </span>

                                        <span class="stext-102 cl6 size-206">
                                            60% cotton
                                        </span>
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            الألوان المتاحة
                                        </span>

                                        <span class="stext-102 cl6 size-206">
                                            أسود، أزرق، رمادي، أخضر، أحمر، أبيض
                                        </span>
                                    </li>

                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">
                                            المقاسات
                                        </span>

                                        <span class="stext-102 cl6 size-206">
                                            XL, L, M, S
                                        </span>
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
                                            <img src="{{ asset('assets/frontend/images/avatar.png') }}" alt="AVATAR">
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





@include('partials.related-products')







@endsection
