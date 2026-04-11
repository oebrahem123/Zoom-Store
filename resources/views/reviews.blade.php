@extends('layouts.master')
@section('content')

<section class="sec-product-detail bg0 p-t-65 p-b-60">
    <div class="container">
        <div class="bor10 m-t-50 p-t-43 p-b-40">
            <div class="tab01">

                <!-- عنوان الصفحة -->
                <div class="text-center p-b-30" dir="rtl">
                    <h3 class="mtext-105 cl2 black">
                        جميع آراء وتقييمات العملاء
                    </h3>
                    <p class="stext-102 cl6">
                        تعرف على تجارب الآخرين مع منتجاتنا
                    </p>
                </div>


                <!-- ========== عرض جميع التعليقات ========== -->
                <div class="row">
                    <div class="col-sm-10 col-md-10 col-lg-8 m-lr-auto">
                        <div class="p-b-30 m-lr-15-sm">

                            @forelse($reviews as $review)
                            <div class="flex-w flex-t p-b-68" dir="rtl">
                                <div class="wrap-pic-s size-109 bor0 of-hidden m-l-18 m-t-6">
                                    <img src="{{ asset('assets/frontend/images/user.png') }}" alt="AVATAR">
                                </div>

                                <div class="size-207">
                                    <div class="flex-w flex-sb-m p-b-17">
                                        <div>
                                            <span class="mtext-107 cl2 black">
                                                {{ $review->name }}
                                            </span>
                                            <br>
                                            <small class="stext-102 cl8">
                                                على منتج:
                                                <a href="/single-product/{{ $review->product_id }}" class="hov-cl1">
                                                    {{ $review->product->name ?? 'منتج غير متوفر' }}
                                                </a>
                                            </small>
                                        </div>

                                        <span class="fs-18 cl11">
                                            @php
                                            $fullStars = floor($review->rating);
                                            $halfStar = $review->rating - $fullStars >= 0.5;
                                            @endphp

                                            @for($i = 1; $i <= 5; $i++) @if($i <=$fullStars) <i class="zmdi zmdi-star">
                                                </i>
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
                                style="background: #f8f9fa; border: 1px solid #d1ecf1; color: #0c5460; padding: 40px; border-radius: 10px;">
                                <i class="zmdi zmdi-comment-outline" style="font-size: 48px;"></i>
                                <h5 class="m-t-20">لا توجد تعليقات حتى الآن</h5>
                                <p class="m-t-10">كن أول من يضيف تقييماً لأحد منتجاتنا!</p>
                            </div>
                            @endforelse

                            <!-- Pagination -->
                            @if(method_exists($reviews, 'links'))
                            <div class="flex-w flex-c-m p-t-30" dir="rtl">
                                {{ $reviews->links() }}
                            </div>
                            @endif

                        </div>
                    </div>
                </div>
                <!-- ========== نموذج إضافة رأي جديد ========== -->
                <div class="row justify-content-center m-b-50">
                    <div class="col-sm-10 col-md-10 col-lg-8">
                        <div class="p-30">

                            @auth
                            {{-- المستخدم مسجل دخول --}}
                            <form class="w-full" method="POST" action="{{ route('storeReview') }}" id="reviewForm">
                                @csrf

                                <h5 class="mtext-108 black cl2 p-b-7" dir="rtl">
                                    أضف رأيك
                                </h5>

                                <p class="stext-102 cl6 p-b-20" dir="rtl">
                                    مرحباً {{ Auth::user()->name }}! شاركنا رأيك في أي منتج من منتجاتنا
                                </p>

                                <!-- اختيار المنتج -->
                                <div class="row p-b-15" dir="rtl">
                                    <div class="col-12 p-b-5">
                                        <label class="stext-102 cl3" for="product_id">اختر المنتج <span
                                                class="text-danger">*</span></label>
                                        <select class="size-111 bor8 stext-102 black cl2 p-lr-20" id="product_id"
                                            name="product_id" required>
                                            <option value="">-- اختر المنتج --</option>
                                            @foreach(\App\Models\Product::orderBy('name')->get() as $product)
                                            <option value="{{ $product->id }}">
                                                {{ $product->name }} ({{ $product->category->name ?? 'بدون قسم' }})
                                            </option>
                                            @endforeach
                                        </select>
                                        @error('product_id')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <!-- التقييم بالنجوم -->
                                <div class="flex-w flex-m p-b-23" dir="rtl">
                                    <span class="stext-102 cl3 m-l-16">
                                        ما هو تقييمك؟
                                    </span>

                                    <span class="wrap-rating fs-18 cl11 pointer" id="ratingStars">
                                        <i class="item-rating pointer zmdi zmdi-star-outline" data-value="1"></i>
                                        <i class="item-rating pointer zmdi zmdi-star-outline" data-value="2"></i>
                                        <i class="item-rating pointer zmdi zmdi-star-outline" data-value="3"></i>
                                        <i class="item-rating pointer zmdi zmdi-star-outline" data-value="4"></i>
                                        <i class="item-rating pointer zmdi zmdi-star-outline" data-value="5"></i>
                                        <input type="hidden" name="rating" id="ratingValue" value="5">
                                    </span>
                                </div>

                                <div class="row p-b-25" dir="rtl">
                                    <!-- الرسالة -->
                                    <div class="col-12 p-b-5">
                                        <label class="stext-102 cl3" for="message">اكتب تقييمك <span
                                                class="text-danger">*</span></label>
                                        <textarea class="size-110 bor8 stext-102 cl2 black p-lr-20 p-tb-10" id="message"
                                            name="message" rows="4" placeholder="شاركنا رأيك في هذا المنتج..."
                                            required></textarea>
                                        @error('message')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- الاسم (ممكن تعديله) -->
                                    <div class="col-sm-6 p-b-5">
                                        <label class="stext-102 cl3" for="name">الاسم <span
                                                class="text-danger">*</span></label>
                                        <input class="size-111 bor8 stext-102 black cl2 p-lr-20" id="name" type="text"
                                            name="name" value="{{ old('name', Auth::user()->name) }}" required>
                                        @error('name')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>

                                    <!-- البريد الإلكتروني (ممكن تعديله) -->
                                    <div class="col-sm-6 p-b-5">
                                        <label class="stext-102 cl3" for="email">البريد الإلكتروني <span
                                                class="text-danger">*</span></label>
                                        <input class="size-111 bor8 stext-102 cl2 black p-lr-20" id="email" type="email"
                                            name="email" value="{{ old('email', Auth::user()->email) }}" required>
                                        @error('email')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                </div>

                                <button type="submit"
                                    class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">
                                    إرسال التقييم
                                </button>
                            </form>
                            @else
                            {{-- المستخدم غير مسجل دخول --}}
                            <div class="text-center" dir="rtl">
                                <i class="zmdi zmdi-account-circle" style="font-size: 48px; color: #717fe0;"></i>
                                <h5 class="mtext-108 cl2 p-b-7">لإضافة رأيك، يرجى تسجيل الدخول أولاً</h5>
                                <p class="stext-102 cl6 p-b-20">سجل دخولك لتشاركنا تجربتك مع منتجاتنا</p>
                                <div class="flex-w flex-c-m">
                                    <a href="{{ route('login') }}" class="btn btn-primary"
                                        style="background: #717fe0; border: none; padding: 10px 30px; border-radius: 5px; color: white; margin-left: 10px;">
                                        تسجيل الدخول
                                    </a>
                                    <a href="{{ route('register') }}" class="btn btn-secondary"
                                        style="background: #6c757d; border: none; padding: 10px 30px; border-radius: 5px; color: white;">
                                        إنشاء حساب جديد
                                    </a>
                                </div>
                            </div>
                            @endauth

                            <!-- عرض رسائل النجاح أو الخطأ -->
                            @if(session('success'))
                            <div class="alert alert-success text-center m-t-20" dir="rtl"
                                style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 10px;">
                                <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
                            </div>
                            @endif

                            @if($errors->any())
                            <div class="alert alert-danger text-center m-t-20" dir="rtl"
                                style="background: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 10px;">
                                <i class="zmdi zmdi-alert-circle"></i> يرجى التحقق من البيانات المدخلة
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</section>

<!-- JavaScript لنظام التقييم بالنجوم -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const stars = document.querySelectorAll('.item-rating');
        const ratingInput = document.getElementById('ratingValue');

        if (stars.length > 0 && ratingInput) {
            ratingInput.value = 5;
            for(let i = 0; i < 5; i++) {
                stars[i].classList.remove('zmdi-star-outline');
                stars[i].classList.add('zmdi-star');
            }

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const value = parseInt(this.getAttribute('data-value'));
                    ratingInput.value = value;
                    stars.forEach(s => {
                        s.classList.remove('zmdi-star');
                        s.classList.add('zmdi-star-outline');
                    });
                    for(let i = 0; i < value; i++) {
                        stars[i].classList.remove('zmdi-star-outline');
                        stars[i].classList.add('zmdi-star');
                    }
                });
            });
        }
    });
</script>

@endsection