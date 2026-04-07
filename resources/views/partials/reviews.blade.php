<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />
<section class="reviews-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 offset-lg-2 text-center">
                <!-- العنوان -->
                <div class="container text-center p-b-50">
                    <h2 class="section-title">
                        أراء العملاء
                    </h2>
                    <p>تجارب حقيقية مع عملائنا</p>
                </div>
            </div>
        </div>

        <!-- Swiper Carousel -->
        <div class="swiper reviewSwiper">
            <div class="swiper-wrapper">
                @forelse($reviews as $review)
                <div class="swiper-slide">
                    <div class="review-card">
                        <!-- صورة المنتج -->
                        <img src="{{ asset($review->product->imagepath ?? 'assets/frontend/images/default.png') }}"
                            class="product-img">

                        <div class="review-content" dir="rtl">
                            <!-- بيانات المستخدم -->
                            <div class="d-flex align-items-center mb-2">
                                <img src="{{ asset('assets/frontend/images/avatar.png') }}" class="user-img">
                                <div class="ms-2 p-r-10">
                                    <div class="name">{{ $review->name }}</div>
                                    <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                                </div>
                            </div>

                            <!-- النجوم -->
                            <div class="stars mb-2">
                                @for($i = 1; $i <= 5; $i++) @if($i <=$review->rating) ★ @else ☆ @endif
                                    @endfor
                            </div>

                            <!-- التعليق -->
                            <p class="review-text">{{ Str::limit($review->message, 100) }}</p>

                            <!-- المنتج -->
                            <small class="text-muted">
                                على:
                                <a href="/single-product/{{ $review->product_id }}">
                                    {{ $review->product->name ?? 'منتج' }}
                                </a>
                            </small>
                        </div>
                    </div>
                </div>
                @empty
                <div class="swiper-slide">
                    <div class="text-center p-5">
                        <h5>لا توجد تقييمات حتى الآن</h5>
                    </div>
                </div>
                @endforelse
            </div>

            <!-- الأسهم -->
            <div class="swiper-button-prev"></div>
            <div class="swiper-button-next"></div>

            <!-- النقاط -->
            <div class="swiper-pagination"></div>
        </div>
    </div>
</section>
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var reviewSwiper = new Swiper(".reviewSwiper", {
            // عدد المنتجات المعروضة
            slidesPerView: 1,
            spaceBetween: 20,
            centeredSlides: true,

            // الحلقة اللانهائية
            loop: {{ count($reviews) > 3 ? 'true' : 'false' }},

            // الأسهم
            navigation: {
                nextEl: ".swiper-button-next",
                prevEl: ".swiper-button-prev",
            },

            // النقاط
            pagination: {
                el: ".swiper-pagination",
                clickable: true,
                dynamicBullets: true,
            },

            // تأثير التحريك
            effect: "slide",
            speed: 500,

            // تشغيل تلقائي (اختياري)
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },

            // التجاوب مع الشاشات المختلفة
            breakpoints: {
                // على الموبايل (أقل من 640px)
                0: {
                    slidesPerView: 1,
                    spaceBetween: 15,
                    centeredSlides: false,
                },
                // على التابلت (640px فأكثر)
                640: {
                    slidesPerView: 1,
                    spaceBetween: 20,
                    centeredSlides: true,
                },
                // على الشاشات المتوسطة (768px فأكثر)
                768: {
                    slidesPerView: 2,
                    spaceBetween: 25,
                    centeredSlides: true,
                },
                // على الشاشات الكبيرة (1024px فأكثر)
                1024: {
                    slidesPerView: 3,
                    spaceBetween: 30,
                    centeredSlides: true,
                },
            },

            // إعدادات إضافية
            watchOverflow: true,
            resistance: true,
            resistanceRatio: 0.85,

            // منع فقدان التركيز عند التحريك
            touchReleaseOnEdges: true,
        });
    });
</script>
