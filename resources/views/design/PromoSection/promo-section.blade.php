{{-- Promo Section --}}
<style>
    .hero-row {

        background:url("{{ asset('assets/frontend/images/slider/new.png') }}") no-repeat left center;

        background-size: 100% auto;

        min-height: 550px;

        display: flex;

        align-items: flex-end;

        margin: 0;
    }

    .hero-content {

        text-align: right;

        padding-bottom: 35px;
    }

    /* ===========================
        Tablet
=========================== */

    @media(max-width:767px) {

        .hero-row {
            background-image:url("{{ asset('assets/frontend/images/slider/new-mobile.png') }}");
            background-position: center top;

            background-size: contain;

            min-height: 620px;

            align-items: flex-end;

        }

        .hero-content {

            text-align: center;

            padding: 0 30px 35px;

        }

        .hero-content h1 {

            font-size: 42px;

        }

        .hero-content p {

            max-width: 100%;

        }

        .hero-content .zoom-btn {

            margin: auto;

        }

    }

    /* ===========================
        Mobile
=========================== */

    @media(max-width:767px) {

        .hero-row {

            background-position: center top;

            background-size: 100% auto;

            min-height: 500px;

        }

        .hero-content {

            padding: 276px 20px 25px;

        }

        .hero-content h1 {

            font-size: 30px;

            line-height: 1.2;

        }

        .hero-content p {

            font-size: 15px;

            line-height: 1.8;

        }

        .hero-content .zoom-btn {

            transform: scale(.95);

        }

    }
</style>


<section class="hero-design bg0 p-t-10 p-b-20">
    <div class="container">

        <div class="row hero-row flex-row-reverse">

            <!-- النص -->
            <div class="col-lg-5 hero-content">

                <h1 class="ltext-102 cl5">
                    أنشئ تصميمك
                    <br>
                    <span class="cl1">بطريقتك الخاصة</span>
                </h1>

                <p class="stext-102 cl6 p-t-16">
                    ارفع صورتك أو اكتب فكرتك، أو استخدم الذكاء الاصطناعي لإنشاء تصميم فريد يُطبع على التيشيرتات،
                    الهوديز، الكابات، والبنطلونات والمنتجات الأخرى.
                </p>

                <div class="p-t-18">
                    <a href="{{ route('design.start') }}" class="zoom-btn" dir="ltr">
                        <span class="icon">→</span>
                        <span class="btn-text">ابدأ التصميم الآن</span>
                        <span class="hover-bg"></span>
                    </a>
                </div>

            </div>

            <div class="col-lg-7"></div>

        </div>

    </div>
</section>
<!-- نهاية قسم الهيرو -->

<!-- بداية قسم المميزات (Features) -->
<section class="bg0 p-t-60 p-b-60">
    <div class="container">
        <div class="row">
            <div class="col-md-3 col-6 p-l-15 p-r-15 m-b-30 flex-c-m flex-col">
                <i class="fa-solid fa-lock cl1 fs-35 m-b-10"></i>
                <h5 class="mtext-102 cl5 p-b-5">دفع آمن</h5>
                <p class="stext-107 cl6">حماية كاملة لبياناتك</p>
            </div>

            <div class="col-md-3 col-6 p-l-15 p-r-15 m-b-30 flex-c-m flex-col">
                <i class="fa-solid fa-award cl1 fs-35 m-b-10"></i>
                <h5 class="mtext-102 cl5 p-b-5">طباعة عالية الجودة</h5>
                <p class="stext-107 cl6">ألوان زاهية تدوم طويلاً</p>
            </div>

            <div class="col-md-3 col-6 p-l-15 p-r-15 m-b-30 flex-c-m flex-col">
                <i class="fa-solid fa-truck-fast cl1 fs-35 m-b-10"></i>
                <h5 class="mtext-102 cl5 p-b-5">شحن سريع</h5>
                <p class="stext-107 cl6">لكل أنحاء مصر</p>
            </div>

            <div class="col-md-3 col-6 p-l-15 p-r-15 m-b-30 flex-c-m flex-col">
                <i class="fas fa-medal cl1 fs-35 m-b-10"></i>
                <h5 class="mtext-102 cl5 p-b-5">ضمان رضاك 100%</h5>
                <p class="stext-107 cl6">جودة عالية أو استرداد</p>
            </div>
        </div>
    </div>
</section>