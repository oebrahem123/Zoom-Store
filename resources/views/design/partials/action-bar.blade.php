<div class="zoom-action-bar bg-white shadow-sm py-3 px-4 border-bottom" dir="rtl">

    <!-- Desktop View -->
    <div class="row align-items-center justify-content-between g-3 d-none d-md-flex">
        <!-- Left -->
        <div class="col-auto d-flex align-items-center gap-3">
            <button type="button"
                class="zoom-action-bar-btn zoom-action-bar-btn-outline d-flex align-items-center gap-2 fw-semibold px-3 py-2"
                onclick="openProductSwitcher()">
                <i class="bi bi-plus-circle fs-5"></i> تبديل المنتج
            </button>
            <div class="zoom-action-bar-thumb-container">
                <img id="actionBarImage" src="{{ isset($baseImages[0]) ? asset($baseImages[0]) : '' }}"
                    alt="{{ $product->name ?? '' }}" class="rounded"
                    style="width: 45px; height: 50px; object-fit: cover;">
            </div>
            <div class="zoom-action-bar-vr"></div>
        </div>

        <!-- Center -->
        <div class="col flex-grow-1 px-2">
            <div class="d-flex flex-wrap align-items-center gap-2">
                <span class="fw-normal text-dark fs-13" id="actionBarProductName">{{ $product->name ?? '' }}</span>
                <a href="javascript:void(0)" class="zoom-action-bar-link" onclick="openProductSwitcher()"> تبديل المنتج
                </a>
            </div>
            <div class="d-flex align-items-center gap-2 mt-1">
                <span class="zoom-action-bar-color-box" id="actionBarColorBox" style="background-color:#737397;"></span>
                <span class="text-muted small fs-13" id="actionBarColorText">—</span>
                <a href="javascript:void(0)" class="zoom-action-bar-link ms-2"
                    onclick="navigateTo('details');return false;"> تبديل اللون </a>
            </div>
        </div>

        <!-- Right -->
        <div class="col-auto d-flex align-items-center gap-2">
            <button type="button"
                class="zoom-action-bar-btn zoom-action-bar-btn-outline d-flex align-items-center gap-2 fw-semibold px-3 py-2"
                onclick="saveDesign(); shareDesign();">
                <i class="bi bi-floppy fs-5"></i> حفظ | مشاركة
            </button>
            <button type="button"
                class="zoom-action-bar-btn zoom-action-bar-btn-primary ms-3 d-flex align-items-center gap-2 fw-semibold px-3 py-2"
                onclick="addToCartDesign()">
                <i class="bi bi-credit-card fs-5"></i> إضافه إلى السله </button>
        </div>
    </div>

    <!-- Mobile View -->
    <div class="d-flex align-items-center justify-content-between d-md-none">
        <button type="button" class="zoom-action-bar-nav-item" onclick="saveDesign()">
            <i class="bi bi-floppy"></i>
            <span class="com"> حفظ </span>
        </button>
        <button type="button" class="zoom-action-bar-nav-item" onclick="navigateTo('designs')">
            <i class="bi bi-folder"></i>
            <span class="com"> تصميماتى </span>
        </button>
        <a href="{{ route('cart') }}" class="zoom-action-bar-nav-item">
            <i class="bi bi-cart"></i>
            <span class="com"> السله </span>
        </a>
        <button type="button" class="zoom-action-bar-nav-item" onclick="openProductSwitcher()">
            <i class="bi bi-arrow-down-up"></i>
            <span class="com"> تبديل المنتج </span>
        </button>
        <div>
            <button type="button" class="zoom-action-bar-buy-btn d-flex align-items-center gap-1"
                onclick="addToCartDesign()">
                شراء
            </button>
        </div>
    </div>

</div>