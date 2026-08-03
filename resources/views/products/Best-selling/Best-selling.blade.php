<style>
    .custom-heading {
        color: #1a1a1a;
        font-family: 'Cairo', serif;
        /* أو Amiri إذا كنت تفضل طابعاً كلاسيكياً أكثر */
        font-weight: 600;
    }

    /* تنسيق الزر باللون المطلوب #ff6e26 */
    .custom-btn {
        color: #ff6e26 !important;
        font-weight: 600;
        font-size: 0.95rem;
        transition: color 0.3s ease;
    }

    /* تأثير عند تمرير الماوس على الزر */
    .custom-btn:hover {
        color: #eb8f61 !important;
    }

    .transition-arrow {
        transition: transform 0.3s ease;
    }

    .custom-btn:hover .transition-arrow {
        transform: translateX(+5px);
    }
</style>
<section class="bg0 p-t-6 p-b-0">
    <div class="container my-5" dir="rtl">

        <!-- صف العنوان والزر العلوي -->
        <div class="row align-items-center position-relative mb-4">
            <div class="col-md-4 text-start d-none d-md-block">
                <a href="#" class="custom-btn text-decoration-none d-inline-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        class="bi bi-arrow-right transition-arrow" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                    </svg>
                    <span>عرض جميع المنتجات</span>
                </a>
            </div>

            <div class="col-12 col-md-4 text-center">
                <h2 class="section-title"> الأكثر مبيعًا</h2>
                <p>المنتجات الأكثر مبيعًا في متجرنا</p>
            </div>

            <div class="col-md-4 d-none d-md-block"></div>
        </div>

        <!-- هنا المشكلة كانت محذوفة -->
        <div class="row isotope-grid" style="position: relative; height: 1721.6px;">


            <!-- زر الانتقال السفلي -->
            <div class="row d-block d-md-none text-center m-t-20">
                <div class="col-12">
                    <a href="#"
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