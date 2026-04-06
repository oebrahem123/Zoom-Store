<!-- Swiper CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

<style>
    .swiper-wrapper {
        height: 51% !important;

    }

    .stars {
        font-size: 20px;
        margin-top: 100px;
        direction: ltr;
        text-align: right;
    }

    .stars span {
        color: #ffc107;
        margin-left: -2px;
    }

    :root {
        --primary-heading: #34495e;
        --subtitle-color: #8e44ad;
        --background-light: #f7f7f7;
        --background-white: #ffffff;
        --text-color: #555555;
        --muted-text: #95a5a6;
    }

    .reviews-page {
        direction: ltr;
        background: var(--background-light);
        padding: 44px 0px 33px 0px;
        font-family: 'Arial', sans-serif;
        position: relative;
    }

    .section-title-wrapper {
        text-align: center;
        margin-bottom: 40px;
    }

    .color {
        width: 100%;
        height: 1px;
        background-color: #e5812f;
    }

    .section-subtitle {
        color: var(--subtitle-color);
        font-size: 1rem;
        font-weight: 500;
        text-transform: uppercase;
    }

    .section-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--primary-heading);
    }

    /* الكروت */
    .testimonial-card {
        background: var(--background-white);
        border-radius: 10px;
        padding: 30px;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.03);
        transition: all 0.3s ease;
        text-align: left;
        position: relative;

        display: flex;
        flex-direction: column;
        justify-content: space-between;
        min-height: 370px;
    }

    .testimonial-card:hover {
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.15);
        transform: translateY(-4px);
    }

    .reviewer-header {
        text-align: center;
        margin-bottom: 25px;
        position: relative;
        margin-top: -64px;
    }

    /* تعديل الـ avatar ليشبه الأكونت */
    .avatar-background {
        width: 70px;
        height: 70px;
        border-radius: 50%;
        margin: 0 auto 10px;
        background: #e5812f;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: bold;
        font-size: 1.4rem;
        border: 3px solid #fff;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .reviewer-name {
        font-weight: 700;
        color: var(--primary-heading);
        font-size: 1.1rem;
        margin-top: 10px;
    }

    .reviewer-handle {
        color: #e5812f;
        font-size: 0.9rem;
    }

    .review-text {
        color: var(--text-color);
        line-height: 1.7;
        font-size: 0.95rem;
        margin-bottom: 25px;
        text-align: right;
        flex-grow: 1;
    }

    .review-footer {
        display: flex;
        justify-content: space-between;
        border-top: 1px solid #eee;
        padding-top: 12px;
        font-size: 0.85rem;
        color: var(--muted-text);
    }

    /* إصلاح مشكلة الأسهم */
    .swiper {
        width: 90%;
        margin: 0 auto;
        padding: 40px 11px 0px 11px;
        overflow: hidden;
        /* تغيير من hidden إلى visible */
        position: relative;

    }

    /* إصلاح تام للأسهم */
    .swiper-button-prev,
    .swiper-button-next {
        color: #f28123;
        transition: 0.3s;
        /* top: 50%; */
        transform: translateY(-50%);
        width: 20px;
        height: 20px;
        background: rgba(255, 255, 255, 0.95);
        border-radius: 50%;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.15);
        display: flex !important;
        /* إضافة !important */
        align-items: center;
        justify-content: center;
        z-index: 1000 !important;
        /* زيادة الـ z-index */
        opacity: 1 !important;
        /* التأكد من الظهور */
    }

    .swiper-button-prev:after,
    .swiper-button-next:after {
        font-size: 10px;
        font-weight: bold;
    }

    .swiper-button-prev:hover,
    .swiper-button-next:hover {
        background: #f28123;
        color: #fff;
        transform: translateY(-50%) scale(1.1);
    }

    /* تحديد مواقع الأسهم بوضوح */
    .swiper-button-prev {
        left: -60px !important;
        /* إبعاد أكثر */
    }

    .swiper-button-next {
        right: -60px !important;
        /* إبعاد أكثر */
    }

    .swiper-pagination-bullet-active {
        background: #f28123;
    }

    .swiper-pagination {
        margin-top: 20px;
        /* للتحكم بالمسافة بين الشرائح والنقاط */
        margin-bottom: 0 !important;
        /* لضمان عدم وجود مسافة سفلية إضافية */
    }

    /* تجاوب */
    @media (max-width: 1200px) {
        .swiper-button-prev {
            left: -40px !important;
        }

        .swiper-button-next {
            right: -40px !important;
        }
    }

    @media (max-width: 992px) {
        .swiper {
            width: 95%;
        }

        .swiper-button-prev {
            left: -30px !important;
        }

        .swiper-button-next {
            right: -30px !important;
        }
    }

    @media (max-width: 768px) {
        .testimonial-card {
            padding: 25px;
            min-height: 340px;
        }

        .swiper-button-prev,
        .swiper-button-next {
            display: none;
        }

        .avatar-background {
            width: 60px;
            height: 60px;
            font-size: 1.2rem;
        }
    }

    @media (max-width: 576px) {
        .reviews-page {
            padding: 50px 0;
        }

        .swiper {
            width: 100%;
            padding: 30px 0 50px 0;
        }
    }

    .swiper,
    .swiper-wrapper,
    .swiper-slide {
        height: auto !important;
    }

    .swiper-pagination {
        margin-bottom: 0 !important;
    }
</style>

<div class="reviews-page">
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

        <div class="swiper mySwiper">
            <div class="swiper-wrapper">
                @if(isset($reviews) && count($reviews) > 0)
                @foreach($reviews as $item)
                <div class="swiper-slide">
                    <div class="testimonial-card">
                        <div>
                            <div class="reviewer-header">
                                <div class="avatar-background">
                                    {{ mb_substr($item->name, 0, 1) ?? 'U' }}
                                </div>
                                <div class="reviewer-name">{{ $item->name ?? 'Customer Name' }}</div>
                                <div class="reviewer-handle">{{ $item->email ?? 'no-email@example.com' }}</div>

                            </div>
                            <p class="review-text">
                                "{{ $item->message ?? 'رأي العميل حول المنتج أو الخدمة.' }}"
                            </p>
                        </div>
                        <div class="stars">
                            @php
                            $stars = 3;
                            $subject = $item->subject ?? '';

                            if (str_contains($subject, '5') || $subject == 'happy' || $subject == 'love') {
                            $stars = 5;
                            } elseif (str_contains($subject, '4')) {
                            $stars = 4;
                            } elseif (str_contains($subject, '3') || $subject == 'neutral') {
                            $stars = 3;
                            } elseif (str_contains($subject, '2') || $subject == 'confused') {
                            $stars = 2;
                            } elseif (str_contains($subject, '1') || $subject == 'angry') {
                            $stars = 1;
                            }
                            @endphp

                            @for ($i = 1; $i <= 5; $i++) @if ($i <=$stars) <span>&#9733;</span>
                                @else
                                <span style="color: #ddd;">&#9733;</span>
                                @endif
                                @endfor
                        </div>
                        <div>
                            <div class="color"></div>
                            <div class="review-footer">
                                <span>{{ $item->created_at ? $item->created_at->format('M d, Y') : date('M d, Y')
                                    }}</span>
                                <span>{{ $item->created_at ? $item->created_at->format('h:i A') : date('h:i A')
                                    }}</span>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
                @else
                <div class="swiper-slide">
                    <div class="testimonial-card text-center p-5 text-muted">
                        <i class="fas fa-comments fa-3x mb-3"></i>
                        <p>لا توجد آراء بعد — كن أول من يشارك رأيه!</p>
                    </div>
                </div>
                @endif
            </div>






        </div>
        <!-- النقاط -->
        <div class="swiper-pagination"></div>
        <!-- الأسهم -->
    </div>
</div>












<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
    slidesPerView: 3,
    spaceBetween: 30,
    loop: {{ count($reviews) > 3 ? 'true' : 'false' }}, // loop فقط إذا عدد الـ reviews أكثر من 3
    centeredSlides: false,
    pagination: {
        el: ".swiper-pagination",
        clickable: true,
        dynamicBullets: true,
    },
    navigation: {
        nextEl: ".swiper-button-next",
        prevEl: ".swiper-button-prev",
    },
    autoplay: {
        delay: 4000,
        disableOnInteraction: false,
    },
    breakpoints: {
        0: {
            slidesPerView: 1,
            spaceBetween: 20
        },
        576: {
            slidesPerView: 1,
            spaceBetween: 20
        },
        768: {
            slidesPerView: 2,
            spaceBetween: 25
        },
        992: {
            slidesPerView: 2,
            spaceBetween: 25
        },
        1200: {
            slidesPerView: 3,
            spaceBetween: 30
        },
    },
    // إعدادات لتجنب مشكلة الـ loop مع عدد قليل من الـ slides
    watchOverflow: true,
    resistance: true,
    resistanceRatio: 0.85,
});
</script>
