@extends('layouts.master')

@section('content')

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
        if (!$path) continue;

        $normalizedColor = strtolower(trim((string) $img->color));

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

                        <!-- الصور الصغيرة -->
                        <div class="wrap-slick3-dots">
                            @foreach ($baseImages as $index => $img)
                            <img src="{{ asset($img) }}"
                                style="width:60px; cursor:pointer; margin:5px; border:2px solid transparent;"
                                onclick="changeImage('{{ asset($img) }}', {{ $index }})">
                            @endforeach
                        </div>

                        <!-- الـ Canvas -->
                        <div class="slick3 gallery-lb" style="width:100%;">
                            <div id="designArea" style="position:relative; width:100%; max-width:500px;">
                                <canvas id="fabricCanvas" width="500" height="500"
                                    style="border:1px solid #eee; border-radius:8px; width:100%;"></canvas>
                            </div>
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
                        الكميه المتاحة : <span id="availableQty">{{ $product->quantity }}</span>
                    </p>

                    <p class="stext-102 cl3 p-t-23">
                        {{ $product->description }}
                    </p>

                    <div class="p-t-33">

                        <!-- المقاس -->
                        <div class="flex-w flex-r-m p-b-10" dir="rtl">
                            <div class="size-203 flex-c-m respon6">المقاس</div>
                            <div class="size-204 respon6-next">
                                <div class="rs1-select2 bor8 bg0">
                                    <select id="sizeSelect" class="form-control" name="size"
                                        style="padding: 8px 12px; border-radius: 5px;">
                                        <option value="">اختر المقاس</option>
                                        @php
                                        $sizes = $product->variants->where('quantity', '>', 0)->pluck('size')->unique();
                                        @endphp
                                        @foreach ($sizes as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                        @endforeach
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- اللون -->
                        <div class="flex-w flex-r-m p-b-10" dir="rtl">
                            <div class="size-203 flex-c-m respon6">اللون</div>
                            <div class="size-204 respon6-next">
                                <div class="rs1-select2 bor8 bg0">
                                    <select id="colorSelect" class="form-control" name="color"
                                        style="padding: 8px 12px; border-radius: 5px;">
                                        <option value="">اختر اللون أولاً</option>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- أدوات التصميم -->
                        <div class="design-tools p-3" style="border:1px solid #eee; border-radius:10px;">
                            <h5 class="text-center mb-3">🎨 أدوات التصميم</h5>

                            <div class="mb-2">
                                <button type="button" onclick="addText()" class="btn btn-dark w-100">
                                    ➕ إضافة نص
                                </button>
                            </div>

                            <div class="mb-2">
                                <label>نوع الخط</label>
                                <select id="fontFamily" class="form-control">
                                    <option value="Arial">Arial</option>
                                    <option value="Tahoma">Tahoma</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Courier New">Courier</option>
                                    <option value="Cairo">Cairo</option>
                                    <option value="Tajawal">Tajawal</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label>رفع صورة</label>
                                <input type="file" id="uploadImage" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-2">
                                <label>تدوير</label>
                                <input type="range" id="rotateText" min="0" max="360" class="form-control-range w-100">
                            </div>

                            <div class="mb-2">
                                <label>لون النص</label>
                                <input type="color" id="textColor" class="form-control" value="#000000">
                            </div>

                            <div class="mb-2">
                                <label>حجم الخط</label>
                                <input type="range" id="fontSize" min="10" max="80" value="20"
                                    class="form-control-range w-100">
                            </div>

                            <div>
                                <button type="button" onclick="deleteSelected()" class="btn btn-danger w-100">
                                    🗑 حذف
                                </button>
                            </div>
                        </div>

                        <!-- الفورم -->
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="cart_item_id" value="{{ request('cart_item_id') }}">
                            <input type="hidden" name="variant_id" id="variant_id">
                            <input type="hidden" name="design_id" id="design_id">
                            <button type="button" onclick="handleSubmit()" class="zoom-btn m-t-20">
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
            <div class="tab01">
                <ul class="nav nav-tabs" role="tablist" dir="rtl">
                    <li class="nav-item p-b-10">
                        <a class="nav-link active" data-toggle="tab" href="#description" role="tab">وصف المنتج</a>
                    </li>
                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#information" role="tab">معلومات إضافية</a>
                    </li>
                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">التعليقات</a>
                    </li>
                </ul>

                <div class="tab-content p-t-43">

                    <div class="tab-pane fade active show" id="description" role="tabpanel" dir="rtl">
                        <div class="how-pos2 p-lr-15-md">
                            <p class="stext-102 cl6">{{ $product->description }}</p>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="information" role="tabpanel" dir="rtl">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <ul class="p-lr-28 p-lr-15-sm">
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">وزن</span>
                                        <span id="weight">--</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">خامات</span>
                                        <span id="material">--</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">الألوان المتاحة</span>
                                        <span>{{ $product->variants->where('quantity', '>',
                                            0)->pluck('color')->unique()->implode(' ، ') }}</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">المقاسات</span>
                                        <span>{{ $product->variants->where('quantity', '>',
                                            0)->pluck('size')->unique()->implode(' , ') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <div class="p-b-30 m-lr-15-sm">

                                    @forelse($product->reviews as $review)
                                    <div class="flex-w flex-t p-b-68" dir="rtl">
                                        <div class="wrap-pic-s size-109 bor0 of-hidden m-l-18 m-t-6">
                                            <x-user-avatar :user="$review->user" alt="AVATAR" />
                                        </div>
                                        <div class="size-207">
                                            <div class="flex-w flex-sb-m p-b-17">
                                                <span class="mtext-107 cl2 black">{{ $review->name }}</span>
                                                <span class="fs-18 cl11">
                                                    @php
                                                    $fullStars = floor($review->rating);
                                                    $halfStar = $review->rating - $fullStars >= 0.5;
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++) @if ($i <=$fullStars) <i
                                                        class="zmdi zmdi-star"></i>
                                                        @elseif($i == $fullStars + 1 && $halfStar)
                                                        <i class="zmdi zmdi-star-half"></i>
                                                        @else
                                                        <i class="zmdi zmdi-star-outline"></i>
                                                        @endif
                                                        @endfor
                                                </span>
                                            </div>
                                            <p class="stext-102 cl6" dir="rtl">{{ $review->message }}</p>
                                            <small class="stext-102 cl8" style="font-size: 12px;">
                                                {{ $review->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="alert alert-info text-center" dir="rtl"
                                        style="background:#f8f9fa; border:1px solid #d1ecf1; color:#0c5460; padding:20px; border-radius:10px; margin-bottom:30px;">
                                        <i class="zmdi zmdi-comment-outline" style="font-size:24px;"></i>
                                        <p style="margin-top:10px; margin-bottom:0;">لا توجد تعليقات على هذا المنتج بعد.
                                            كن أول من يقيّم!</p>
                                    </div>
                                    @endforelse

                                    <form class="w-full" method="POST" action="{{ route('storeReview') }}"
                                        id="reviewForm">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                                        <h5 class="mtext-108 black cl2 p-b-7" dir="rtl">إضافة مراجعة</h5>
                                        <p class="stext-102 cl6" dir="rtl">لن يتم نشر عنوان بريدك الإلكتروني.</p>

                                        <div class="flex-w flex-m p-t-50 p-b-23" dir="rtl">
                                            <span class="stext-102 cl3 m-l-16">ما هو تقييمك؟</span>
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
                                                <label class="stext-102 cl3" for="message">اكتب تقييمك <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="size-110 bor8 stext-102 cl2 black p-lr-20 p-tb-10"
                                                    id="message" name="message"
                                                    required>{{ old('message', session('review_data.message')) }}</textarea>
                                                @error('message')<small class="text-danger">{{ $message
                                                    }}</small>@enderror
                                            </div>
                                            <div class="col-sm-6 p-b-5">
                                                <label class="stext-102 cl3" for="name">الاسم <span
                                                        class="text-danger">*</span></label>
                                                <input class="size-111 bor8 stext-102 black cl2 p-lr-20" id="name"
                                                    type="text" name="name"
                                                    value="{{ old('name', auth()->check() ? auth()->user()->name : session('review_data.name')) }}" @auth readonly @endauth required>
                                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                                            </div>
                                            <div class="col-sm-6 p-b-5">
                                                <label class="stext-102 cl3" for="email">البريد الإلكتروني <span
                                                        class="text-danger">*</span></label>
                                                <input class="size-111 bor8 stext-102 cl2 black p-lr-20" id="email"
                                                    type="email" name="email"
                                                    value="{{ old('email', auth()->check() ? auth()->user()->email : session('review_data.email')) }}" @auth readonly @endauth required>
                                                @error('email')<small class="text-danger">{{ $message
                                                    }}</small>@enderror
                                            </div>
                                        </div>

                                        <button type="submit"
                                            class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">
                                            إرسال
                                        </button>
                                    </form>

                                    @if (session('success'))
                                    <div class="alert alert-success text-center" dir="rtl"
                                        style="background:#d4edda; border:1px solid #c3e6cb; color:#155724; padding:12px; border-radius:10px; margin-top:20px;">
                                        <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
                                    </div>
                                    @endif

                                    @if ($errors->any())
                                    <div class="alert alert-danger text-center" dir="rtl"
                                        style="background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; padding:12px; border-radius:10px; margin-top:20px;">
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

{{-- Fonts & Fabric.js --}}
<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<script>
    // ============================================================
    // Fix Fabric.js textBaseline error - IMPORTANT!
    // ============================================================
    // Must be done BEFORE any fabric operations
    if (typeof fabric !== 'undefined') {
        if (fabric.Text) {
            fabric.Text.prototype.textBaseline = 'bottom';
        }
        if (fabric.IText) {
            fabric.IText.prototype.textBaseline = 'bottom';
        }
        if (fabric.Textbox) {
            fabric.Textbox.prototype.textBaseline = 'bottom';
        }
    }

    // ============================================================
    // البيانات من Laravel
    // ============================================================
    const variants = @json($product->variants);
    const productImages = @json($baseImages);
    const colorImages = @json($colorImages);
    const existingVariant = @json($existingVariantData ?? null);
    const existingDesign = @json($existingDesign ?? null);

    // ============================================================
    // Fabric.js Setup
    // ============================================================
    let canvas;
    let canvasViews = {};
    let currentView = 0;

    // Helper function to fix image paths
    function fixImagePath(path) {
        if (!path) return null;
        // Remove any /design/edit prefix if exists
        let cleanPath = path.replace(/^\/design\/edit\//, '');
        // Ensure it starts with /
        if (!cleanPath.startsWith('/') && !cleanPath.startsWith('http')) {
            cleanPath = '/' + cleanPath;
        }
        return cleanPath;
    }

    // Initialize canvas
    // بعد إنشاء الكانفاس، أضف مستمع للأحداث
function initCanvas() {
    try {
        const canvasElement = document.getElementById('fabricCanvas');
        if (!canvasElement) {
            console.error('Canvas element not found');
            return false;
        }

        canvas = new fabric.Canvas('fabricCanvas', {
            selection: true,
            preserveObjectStacking: true,
            width: 500,
            height: 500,
            backgroundColor: 'transparent'
        });

        if (!canvas) {
            console.error('Failed to create fabric canvas');
            return false;
        }

        // إضافة مستمع لحدث التعديل على الكانفاس
        canvas.on('object:modified', function() {
            console.log('Object modified, saving view...');
            saveCurrentView();
        });

        canvas.on('object:added', function() {
            console.log('Object added, saving view...');
            saveCurrentView();
        });

        canvas.on('object:removed', function() {
            console.log('Object removed, saving view...');
            saveCurrentView();
        });

        return true;
    } catch (error) {
        console.error('Error initializing canvas:', error);
        return false;
    }
}

    // تحميل صورة المنتج كـ background
    function loadProductImage(src) {
        if (!canvas || !src) {
            console.error('Canvas not initialized or no image source');
            return;
        }

        let cleanSrc = fixImagePath(src);

        console.log('Loading product image:', cleanSrc);

        fabric.Image.fromURL(cleanSrc, function(img) {
            if (img && canvas) {
                try {
                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                        scaleX: canvas.width / img.width,
                        scaleY: canvas.height / img.height,
                        crossOrigin: 'anonymous'
                    });
                } catch (error) {
                    console.error('Error setting background image:', error);
                }
            }
        }, function(error) {
            console.error('Error loading product image:', cleanSrc, error);
        });
    }

    // تغيير الصورة
   // تغيير الصورة - النسخة المصححة
function changeImage(src, index) {
    if (!canvas) {
        console.error('Canvas not initialized');
        return;
    }

    try {
        // حفظ التصميم الحالي أولاً (حتى لو كان فارغ)
        const currentState = canvas.toJSON();
        canvasViews[currentView] = currentState;

        // تحديث الـ view الحالي
        currentView = index;

        // التحقق من وجود تصميم محفوظ لهذه الصورة
        const savedView = canvasViews[index];

        if (savedView && savedView.objects && savedView.objects.length > 0) {
            // يوجد تصميم محفوظ - استعادته
            console.log(`Loading saved design for view ${index}`);

            canvas.loadFromJSON(savedView, () => {
                // بعد تحميل التصميم، نضيف الخلفية
                loadProductImage(src);
                canvas.renderAll();
            });
        } else {
            // لا يوجد تصميم محفوظ - صفحة جديدة
            console.log(`Creating new design for view ${index}`);

            // مسح الكانفاس وإنشاء صفحة جديدة
            canvas.getObjects().forEach(obj => {
                if (obj !== canvas.backgroundImage) {
                    canvas.remove(obj);
                }
            });

            // تحميل الخلفية فقط
            loadProductImage(src);
        }

        // تحديث الـ thumbnail المحدد
        document.querySelectorAll('.wrap-slick3-dots img').forEach(t => t.style.borderColor = 'transparent');
        if (event && event.target) {
            event.target.style.borderColor = 'red';
        }
    } catch (error) {
        console.error('Error changing image:', error);
    }
}

   // إضافة نص - النسخة المصححة
function addText() {
    if (!canvas) {
        console.error('Canvas not initialized');
        return;
    }

    try {
        const text = new fabric.Textbox('اكتب هنا', {
            left: 150,
            top: 150,
            fontSize: 20,
            fill: '#000000',
            fontFamily: 'Cairo',
            padding: 5,
            cornerColor: 'red',
            cornerSize: 8,
            transparentCorners: false,
            textBaseline: 'bottom',
            width: 150,  // عرض محدد للنص
            hasControls: true,
            hasBorders: true
        });

        canvas.add(text);
        canvas.setActiveObject(text);
        canvas.renderAll();

        // حفظ التغيير فوراً
        saveCurrentView();

    } catch (error) {
        console.error('Error adding text:', error);
    }
}

  // حذف العنصر المحدد - النسخة المصححة
function deleteSelected() {
    if (!canvas) return;

    try {
        const obj = canvas.getActiveObject();
        if (obj) {
            canvas.remove(obj);
            canvas.renderAll();

            // حفظ التغيير فوراً
            saveCurrentView();
        }
    } catch (error) {
        console.error('Error deleting object:', error);
    }
}

    // أدوات التحكم
    function setupControls() {
        const textColor = document.getElementById('textColor');
        const fontFamily = document.getElementById('fontFamily');
        const fontSize = document.getElementById('fontSize');
        const rotateText = document.getElementById('rotateText');

        if (textColor) {
            textColor.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj) { obj.set('fill', this.value); canvas.renderAll(); }
            });
        }

        if (fontFamily) {
            fontFamily.addEventListener('change', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('fontFamily', this.value); canvas.renderAll(); }
            });
        }

        if (fontSize) {
            fontSize.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('fontSize', parseInt(this.value)); canvas.renderAll(); }
            });
        }

        if (rotateText) {
            rotateText.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('angle', parseInt(this.value)); canvas.renderAll(); }
            });
        }
    }

    // رفع صورة
  // رفع صورة - النسخة المصححة
function setupImageUpload() {
    const uploadImage = document.getElementById('uploadImage');
    if (!uploadImage) return;

    uploadImage.addEventListener('change', function(e) {
        if (!canvas) return;

        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(event) {
            fabric.Image.fromURL(event.target.result, function(img) {
                if (img && canvas) {
                    const maxWidth = 200;
                    let scale = 1;
                    if (img.width > maxWidth) {
                        scale = maxWidth / img.width;
                    }

                    img.scale(scale);
                    img.set({
                        left: 100,
                        top: 100,
                        originalWidth: img.width,
                        originalHeight: img.height,
                        hasControls: true,
                        hasBorders: true
                    });

                    canvas.add(img);
                    canvas.setActiveObject(img);
                    canvas.renderAll();

                    // حفظ التغيير فوراً
                    saveCurrentView();
                }
            });
        };
        reader.readAsDataURL(file);
    });
}
    // المقاس → الألوان
    function loadColorsBySize() {
        const size = document.getElementById('sizeSelect').value;
        const colorSelect = document.getElementById('colorSelect');
        if (!colorSelect) return;

        colorSelect.innerHTML = '<option value="">اختر اللون</option>';
        if (!size || !variants) return;

        const filtered = variants.filter(v => v.size === size && v.quantity > 0);
        const colors = [...new Set(filtered.map(v => v.color))];

        colors.forEach(color => {
            const option = document.createElement('option');
            option.value = color;
            option.textContent = color;
            colorSelect.appendChild(option);
        });
    }

    function onColorChange() {
        const size = document.getElementById('sizeSelect').value;
        const color = document.getElementById('colorSelect').value;

        if (variants && size && color) {
            const found = variants.find(v => v.size === size && v.color === color);
            if (found) {
                const variantIdInput = document.getElementById('variant_id');
                const availableQtySpan = document.getElementById('availableQty');
                const weightSpan = document.getElementById('weight');
                const materialSpan = document.getElementById('material');

                if (variantIdInput) variantIdInput.value = found.id;
                if (availableQtySpan) availableQtySpan.textContent = found.quantity ?? '--';
                if (weightSpan) weightSpan.textContent = found.weight ?? '--';
                if (materialSpan) materialSpan.textContent = found.material ?? '--';
            }
        }

        if (color && colorImages && colorImages[color.toLowerCase().trim()]) {
            updateThumbnails(colorImages[color.toLowerCase().trim()]);
        }
    }

    // تحديث الـ Thumbnails
    function updateThumbnails(images) {
        const container = document.querySelector('.wrap-slick3-dots');
        if (!container) return;

        container.innerHTML = '';
        images.forEach((img, i) => {
            const el = document.createElement('img');
            let imgSrc = fixImagePath(img);
            el.src = imgSrc;
            el.style.cssText = 'width:60px; cursor:pointer; margin:5px; border:2px solid transparent;';
            el.onclick = function() {
                changeImage(imgSrc, i);
            };
            container.appendChild(el);
        });

        if (images.length > 0) {
            changeImage(fixImagePath(images[0]), 0);
        }
    }

   // Load existing design
function loadExistingDesign() {
    if (!existingDesign || !existingDesign.designs || !canvas) {
        console.log('No existing design to load');
        return;
    }

    console.log('Loading existing design:', existingDesign);

    try {
        // Clear existing views
        canvasViews = {};

        // Process each view
        existingDesign.designs.forEach(viewDesign => {
            const viewIndex = viewDesign.view_index;
            canvasViews[viewIndex] = {
                objects: [],
                version: '1.0'
            };

            // Process each element
            viewDesign.elements.forEach(el => {
                if (el.type === 'text') {
                    canvasViews[viewIndex].objects.push({
                        type: 'i-text',
                        text: el.content,
                        left: el.position_x,
                        top: el.position_y,
                        fill: el.color,
                        fontFamily: el.font_family,
                        angle: el.rotation,
                        fontSize: el.font_size || 20,
                        textBaseline: 'bottom',
                        width: 150
                    });
                }
                else if (el.type === 'image') {
                    let imagePath = fixImagePath(el.content);

                    const imageObj = {
                        type: 'image',
                        src: imagePath,
                        left: el.position_x,
                        top: el.position_y,
                        angle: el.rotation
                    };

                    if (el.scale_x && el.scale_y) {
                        imageObj.scaleX = el.scale_x;
                        imageObj.scaleY = el.scale_y;
                        if (el.original_width) imageObj.width = el.original_width;
                        if (el.original_height) imageObj.height = el.original_height;
                    } else if (el.width && el.height) {
                        imageObj.calculatedWidth = el.width;
                        imageObj.calculatedHeight = el.height;
                        imageObj.needsScaleCalculation = true;
                    } else {
                        imageObj.scaleX = 1;
                        imageObj.scaleY = 1;
                    }

                    canvasViews[viewIndex].objects.push(imageObj);
                }
            });
        });

        // Load the first view (index 0) or current view
        const initialView = canvasViews[0] ? 0 : currentView;

        if (canvasViews[initialView] && productImages && productImages[initialView]) {
            canvas.loadFromJSON(canvasViews[initialView], function() {
                fixImageScales();
                loadProductImage(fixImagePath(productImages[initialView]));
                canvas.renderAll();
                console.log(`Loaded view ${initialView} with ${canvasViews[initialView].objects.length} objects`);
            });
        }
    } catch (error) {
        console.error('Error loading existing design:', error);
    }
}
    // Fix image scales
    function fixImageScales() {
        if (!canvas) return;

        const objects = canvas.getObjects();
        objects.forEach(obj => {
            if (obj.type === 'image' && obj.needsScaleCalculation) {
                const targetWidth = obj.calculatedWidth;
                const targetHeight = obj.calculatedHeight;

                if (targetWidth && targetHeight && obj.width && obj.height) {
                    const scaleX = targetWidth / obj.width;
                    const scaleY = targetHeight / obj.height;
                    obj.scale(scaleX);
                    obj.set({
                        scaleX: scaleX,
                        scaleY: scaleY
                    });
                }

                delete obj.needsScaleCalculation;
                delete obj.calculatedWidth;
                delete obj.calculatedHeight;
            }
        });
        canvas.renderAll();
    }

    // Submit handler
    async function handleSubmit() {
        const variantId = document.getElementById('variant_id').value;
        if (!variantId) {
            alert('اختار المقاس واللون الأول ❗');
            return;
        }

        if (!canvas) {
            alert('خطأ في تحميل التصميم ❗');
            return;
        }

        try {
            canvasViews[currentView] = canvas.toJSON();

            const designsPayload = [];
            for (const viewIndex in canvasViews) {
                if (!canvasViews[viewIndex] || !canvasViews[viewIndex].objects) continue;

                const objects = canvasViews[viewIndex].objects || [];
                if (objects.length === 0) continue;

                const elements = objects.map(obj => {
                    if (obj.type === 'image') {
                        return {
                            type: 'image',
                            content: obj.src || null,
                            position_x: Math.round(obj.left || 0),
                            position_y: Math.round(obj.top || 0),
                            width: obj.width ? Math.round(obj.width * (obj.scaleX || 1)) : null,
                            height: obj.height ? Math.round(obj.height * (obj.scaleY || 1)) : null,
                            rotation: Math.round(obj.angle || 0),
                            scale_x: obj.scaleX || 1,
                            scale_y: obj.scaleY || 1,
                            original_width: obj.originalWidth || obj.width || null,
                            original_height: obj.originalHeight || obj.height || null,
                            z_index: obj.zIndex || 0,
                        };
                    }

                    return {
                        type: 'text',
                        content: obj.text || null,
                        position_x: Math.round(obj.left || 0),
                        position_y: Math.round(obj.top || 0),
                        rotation: Math.round(obj.angle || 0),
                        color: obj.fill || null,
                        font_family: obj.fontFamily || null,
                        font_size: obj.fontSize || null,
                        z_index: obj.zIndex || 0,
                    };
                });

                designsPayload.push({ view_index: parseInt(viewIndex), elements });
            }

            const previewImage = canvas.toDataURL({ format: 'png', quality: 0.8 });
            const existingDesignId = document.getElementById('design_id').value;

            const payload = {
                product_id: {{ $product->id }},
                variant_id: variantId,
                view: currentView.toString(),
                designs: designsPayload,
                preview_image: previewImage,
            };

            if (existingDesignId) payload.design_id = existingDesignId;

            const response = await fetch("{{ route('design.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || 'حصل خطأ في حفظ التصميم');
                return;
            }

            const designIdInput = document.getElementById('design_id');
            if (designIdInput) designIdInput.value = data.design_id;

            document.getElementById('addToCartForm').submit();

        } catch (err) {
            console.error('Submit error:', err);
            alert('حصل خطأ، حاول تاني');
        }
    }

    // Initialize everything
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');

        if (!initCanvas()) {
            console.error('Failed to initialize canvas');
            return;
        }

        const sizeSelect = document.getElementById('sizeSelect');
        const colorSelect = document.getElementById('colorSelect');

        if (sizeSelect) sizeSelect.addEventListener('change', loadColorsBySize);
        if (colorSelect) colorSelect.addEventListener('change', onColorChange);

        setupControls();
        setupImageUpload();

        if (productImages && productImages.length > 0 && productImages[0]) {
            loadProductImage(fixImagePath(productImages[0]));
        }

        if (existingVariant) {
            if (sizeSelect) sizeSelect.value = existingVariant.size;
            loadColorsBySize();
            setTimeout(() => {
                if (colorSelect) colorSelect.value = existingVariant.color;
                onColorChange();
            }, 100);
        }

        loadExistingDesign();

        console.log('Initialization complete');
    });
    // دالة لحفظ الـ view الحالي بشكل آمن
function saveCurrentView() {
    if (!canvas) return;

    try {
        // ننشئ نسخة من الكانفاس بدون الخلفية
        const currentObjects = canvas.getObjects().filter(obj => obj !== canvas.backgroundImage);

        canvasViews[currentView] = {
            objects: currentObjects.map(obj => obj.toJSON()),
            version: '1.0'
        };

        console.log(`View ${currentView} saved with ${currentObjects.length} objects`);
    } catch (error) {
        console.error('Error saving view:', error);
    }
}
</script>

@endsection









{{-- /////////////////////////////////////////////////////////////////////// --}}

@extends('layouts.master')

@section('content')

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
        if (!$path) continue;

        $normalizedColor = strtolower(trim((string) $img->color));

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

                        <!-- الصور الصغيرة -->
                        <div class="wrap-slick3-dots">
                            @foreach ($baseImages as $index => $img)
                            <img src="{{ asset($img) }}"
                                style="width:60px; cursor:pointer; margin:5px; border:2px solid transparent;"
                                onclick="changeImage('{{ asset($img) }}', {{ $index }})">
                            @endforeach
                        </div>

                        <!-- الـ Canvas -->
                        <div class="slick3 gallery-lb" style="width:100%;">
                            <div id="designArea" style="position:relative; width:100%; max-width:500px;">
                                <canvas id="fabricCanvas" width="500" height="500"
                                    style="border:1px solid #eee; border-radius:8px; width:100%;"></canvas>
                            </div>
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
                        الكميه المتاحة : <span id="availableQty">{{ $product->quantity }}</span>
                    </p>

                    <p class="stext-102 cl3 p-t-23">
                        {{ $product->description }}
                    </p>

                    <div class="p-t-33">

                        <!-- المقاس -->
                        <div class="flex-w flex-r-m p-b-10" dir="rtl">
                            <div class="size-203 flex-c-m respon6">المقاس</div>
                            <div class="size-204 respon6-next">
                                <div class="rs1-select2 bor8 bg0">
                                    <select id="sizeSelect" class="form-control" name="size"
                                        style="padding: 8px 12px; border-radius: 5px;">
                                        <option value="">اختر المقاس</option>
                                        @php
                                        $sizes = $product->variants->where('quantity', '>', 0)->pluck('size')->unique();
                                        @endphp
                                        @foreach ($sizes as $size)
                                        <option value="{{ $size }}">{{ $size }}</option>
                                        @endforeach
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- اللون -->
                        <div class="flex-w flex-r-m p-b-10" dir="rtl">
                            <div class="size-203 flex-c-m respon6">اللون</div>
                            <div class="size-204 respon6-next">
                                <div class="rs1-select2 bor8 bg0">
                                    <select id="colorSelect" class="form-control" name="color"
                                        style="padding: 8px 12px; border-radius: 5px;">
                                        <option value="">اختر اللون أولاً</option>
                                    </select>
                                    <div class="dropDownSelect2"></div>
                                </div>
                            </div>
                        </div>

                        <!-- أدوات التصميم -->
                        <div class="design-tools p-3" style="border:1px solid #eee; border-radius:10px;">
                            <h5 class="text-center mb-3">🎨 أدوات التصميم</h5>

                            <div class="mb-2">
                                <button type="button" onclick="addText()" class="btn btn-dark w-100">
                                    ➕ إضافة نص
                                </button>
                            </div>

                            <div class="mb-2">
                                <label>نوع الخط</label>
                                <select id="fontFamily" class="form-control">
                                    <option value="Arial">Arial</option>
                                    <option value="Tahoma">Tahoma</option>
                                    <option value="Verdana">Verdana</option>
                                    <option value="Courier New">Courier</option>
                                    <option value="Cairo">Cairo</option>
                                    <option value="Tajawal">Tajawal</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label>رفع صورة</label>
                                <input type="file" id="uploadImage" class="form-control" accept="image/*">
                            </div>

                            <div class="mb-2">
                                <label>تدوير</label>
                                <input type="range" id="rotateText" min="0" max="360" class="form-control-range w-100">
                            </div>

                            <div class="mb-2">
                                <label>لون النص</label>
                                <input type="color" id="textColor" class="form-control" value="#000000">
                            </div>

                            <div class="mb-2">
                                <label>حجم الخط</label>
                                <input type="range" id="fontSize" min="10" max="80" value="20"
                                    class="form-control-range w-100">
                            </div>

                            <div>
                                <button type="button" onclick="deleteSelected()" class="btn btn-danger w-100">
                                    🗑 حذف
                                </button>
                            </div>
                        </div>

                        <!-- الفورم -->
                        <form action="{{ route('cart.add', $product->id) }}" method="POST" id="addToCartForm">
                            @csrf
                            <input type="hidden" name="cart_item_id" value="{{ request('cart_item_id') }}">
                            <input type="hidden" name="variant_id" id="variant_id">
                            <input type="hidden" name="design_id" id="design_id">
                            <button type="button" onclick="handleSubmit()" class="zoom-btn m-t-20">
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
            <div class="tab01">
                <ul class="nav nav-tabs" role="tablist" dir="rtl">
                    <li class="nav-item p-b-10">
                        <a class="nav-link active" data-toggle="tab" href="#description" role="tab">وصف المنتج</a>
                    </li>
                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#information" role="tab">معلومات إضافية</a>
                    </li>
                    <li class="nav-item p-b-10">
                        <a class="nav-link" data-toggle="tab" href="#reviews" role="tab">التعليقات</a>
                    </li>
                </ul>

                <div class="tab-content p-t-43">

                    <div class="tab-pane fade active show" id="description" role="tabpanel" dir="rtl">
                        <div class="how-pos2 p-lr-15-md">
                            <p class="stext-102 cl6">{{ $product->description }}</p>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="information" role="tabpanel" dir="rtl">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <ul class="p-lr-28 p-lr-15-sm">
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">وزن</span>
                                        <span id="weight">--</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">خامات</span>
                                        <span id="material">--</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">الألوان المتاحة</span>
                                        <span>{{ $product->variants->where('quantity', '>',
                                            0)->pluck('color')->unique()->implode(' ، ') }}</span>
                                    </li>
                                    <li class="flex-w flex-t p-b-7">
                                        <span class="stext-102 cl3 size-205">المقاسات</span>
                                        <span>{{ $product->variants->where('quantity', '>',
                                            0)->pluck('size')->unique()->implode(' , ') }}</span>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <div class="tab-pane fade" id="reviews" role="tabpanel">
                        <div class="row">
                            <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                <div class="p-b-30 m-lr-15-sm">

                                    @forelse($product->reviews as $review)
                                    <div class="flex-w flex-t p-b-68" dir="rtl">
                                        <div class="wrap-pic-s size-109 bor0 of-hidden m-l-18 m-t-6">
                                            <x-user-avatar :user="$review->user" alt="AVATAR" />
                                        </div>
                                        <div class="size-207">
                                            <div class="flex-w flex-sb-m p-b-17">
                                                <span class="mtext-107 cl2 black">{{ $review->name }}</span>
                                                <span class="fs-18 cl11">
                                                    @php
                                                    $fullStars = floor($review->rating);
                                                    $halfStar = $review->rating - $fullStars >= 0.5;
                                                    @endphp
                                                    @for ($i = 1; $i <= 5; $i++) @if ($i <=$fullStars) <i
                                                        class="zmdi zmdi-star"></i>
                                                        @elseif($i == $fullStars + 1 && $halfStar)
                                                        <i class="zmdi zmdi-star-half"></i>
                                                        @else
                                                        <i class="zmdi zmdi-star-outline"></i>
                                                        @endif
                                                        @endfor
                                                </span>
                                            </div>
                                            <p class="stext-102 cl6" dir="rtl">{{ $review->message }}</p>
                                            <small class="stext-102 cl8" style="font-size: 12px;">
                                                {{ $review->created_at->diffForHumans() }}
                                            </small>
                                        </div>
                                    </div>
                                    @empty
                                    <div class="alert alert-info text-center" dir="rtl"
                                        style="background:#f8f9fa; border:1px solid #d1ecf1; color:#0c5460; padding:20px; border-radius:10px; margin-bottom:30px;">
                                        <i class="zmdi zmdi-comment-outline" style="font-size:24px;"></i>
                                        <p style="margin-top:10px; margin-bottom:0;">لا توجد تعليقات على هذا المنتج بعد.
                                            كن أول من يقيّم!</p>
                                    </div>
                                    @endforelse

                                    <form class="w-full" method="POST" action="{{ route('storeReview') }}"
                                        id="reviewForm">
                                        @csrf
                                        <input type="hidden" name="product_id" value="{{ $product->id }}">

                                        <h5 class="mtext-108 black cl2 p-b-7" dir="rtl">إضافة مراجعة</h5>
                                        <p class="stext-102 cl6" dir="rtl">لن يتم نشر عنوان بريدك الإلكتروني.</p>

                                        <div class="flex-w flex-m p-t-50 p-b-23" dir="rtl">
                                            <span class="stext-102 cl3 m-l-16">ما هو تقييمك؟</span>
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
                                                <label class="stext-102 cl3" for="message">اكتب تقييمك <span
                                                        class="text-danger">*</span></label>
                                                <textarea class="size-110 bor8 stext-102 cl2 black p-lr-20 p-tb-10"
                                                    id="message" name="message"
                                                    required>{{ old('message', session('review_data.message')) }}</textarea>
                                                @error('message')<small class="text-danger">{{ $message
                                                    }}</small>@enderror
                                            </div>
                                            <div class="col-sm-6 p-b-5">
                                                <label class="stext-102 cl3" for="name">الاسم <span
                                                        class="text-danger">*</span></label>
                                                <input class="size-111 bor8 stext-102 black cl2 p-lr-20" id="name"
                                                    type="text" name="name"
                                                    value="{{ old('name', auth()->check() ? auth()->user()->name : session('review_data.name')) }}" @auth readonly @endauth required>
                                                @error('name')<small class="text-danger">{{ $message }}</small>@enderror
                                            </div>
                                            <div class="col-sm-6 p-b-5">
                                                <label class="stext-102 cl3" for="email">البريد الإلكتروني <span
                                                        class="text-danger">*</span></label>
                                                <input class="size-111 bor8 stext-102 cl2 black p-lr-20" id="email"
                                                    type="email" name="email"
                                                    value="{{ old('email', auth()->check() ? auth()->user()->email : session('review_data.email')) }}" @auth readonly @endauth required>
                                                @error('email')<small class="text-danger">{{ $message
                                                    }}</small>@enderror
                                            </div>
                                        </div>

                                        <button type="submit"
                                            class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">
                                            إرسال
                                        </button>
                                    </form>

                                    @if (session('success'))
                                    <div class="alert alert-success text-center" dir="rtl"
                                        style="background:#d4edda; border:1px solid #c3e6cb; color:#155724; padding:12px; border-radius:10px; margin-top:20px;">
                                        <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
                                    </div>
                                    @endif

                                    @if ($errors->any())
                                    <div class="alert alert-danger text-center" dir="rtl"
                                        style="background:#f8d7da; border:1px solid #f5c6cb; color:#721c24; padding:12px; border-radius:10px; margin-top:20px;">
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

{{-- Fonts & Fabric.js --}}
<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>
{{-- <script>
    // ============================================================
    // Fix Fabric.js textBaseline error - IMPORTANT!
    // ============================================================
    if (typeof fabric !== 'undefined') {
        if (fabric.Text) {
            fabric.Text.prototype.textBaseline = 'bottom';
        }
        if (fabric.IText) {
            fabric.IText.prototype.textBaseline = 'bottom';
        }
        if (fabric.Textbox) {
            fabric.Textbox.prototype.textBaseline = 'bottom';
        }
    }

    // ============================================================
    // البيانات من Laravel
    // ============================================================
    const variants = @json($product->variants);
    const productImages = @json($baseImages);
    const colorImages = @json($colorImages);
    const existingVariant = @json($existingVariantData ?? null);
    const existingDesign = @json($existingDesign ?? null);

    // ============================================================
    // Fabric.js Setup
    // ============================================================
    let canvas;
    let canvasViews = {};
    let currentView = 0;
    const imageCache = {};
    const uploadedImagesCache = {};

    // Helper function to fix image paths
    function fixImagePath(path) {
        if (!path) return null;
        let cleanPath = path.replace(/^\/design\/edit\//, '');
        if (!cleanPath.startsWith('/') && !cleanPath.startsWith('http')) {
            cleanPath = '/' + cleanPath;
        }
        return cleanPath;
    }

    // Initialize canvas
    function initCanvas() {
        try {
            const canvasElement = document.getElementById('fabricCanvas');
            if (!canvasElement) {
                console.error('Canvas element not found');
                return false;
            }

            canvas = new fabric.Canvas('fabricCanvas', {
                selection: true,
                preserveObjectStacking: true,
                width: 500,
                height: 500,
                backgroundColor: 'transparent',
                renderOnAddRemove: true
            });

            if (!canvas) {
                console.error('Failed to create fabric canvas');
                return false;
            }

            console.log('Canvas initialized successfully');

            canvas.on('object:modified', function(e) {
                console.log('Object modified:', e.target.type);
                saveCurrentView();
            });

            canvas.on('object:added', function(e) {
                console.log('Object added:', e.target.type);
                saveCurrentView();
            });

            canvas.on('object:removed', function(e) {
                console.log('Object removed');
                saveCurrentView();
            });

            return true;
        } catch (error) {
            console.error('Error initializing canvas:', error);
            return false;
        }
    }

    // تحميل صورة المنتج كـ background
    function loadProductImage(src) {
        if (!canvas || !src) {
            console.error('Canvas not initialized or no image source');
            return;
        }

        let cleanSrc = fixImagePath(src);

        if (imageCache[cleanSrc]) {
            const cachedImg = imageCache[cleanSrc];
            canvas.setBackgroundImage(
                cachedImg,
                canvas.renderAll.bind(canvas),
                {
                    scaleX: canvas.width / cachedImg.width,
                    scaleY: canvas.height / cachedImg.height,
                    crossOrigin: 'anonymous'
                }
            );
            return;
        }

        fabric.Image.fromURL(cleanSrc, function(img) {
            if (img && canvas) {
                imageCache[cleanSrc] = img;
                canvas.setBackgroundImage(
                    img,
                    canvas.renderAll.bind(canvas),
                    {
                        scaleX: canvas.width / img.width,
                        scaleY: canvas.height / img.height,
                        crossOrigin: 'anonymous'
                    }
                );
            }
        }, {
            crossOrigin: 'anonymous'
        });
    }

    // تغيير الصورة
    function changeImage(src, index) {
        if (!canvas) {
            console.error('Canvas not initialized');
            return;
        }

        try {
            saveCurrentView();
            currentView = index;
            const savedView = canvasViews[index];

            canvas.clear();

            if (savedView && savedView.objects && savedView.objects.length > 0) {
                console.log(`Loading saved design for view ${index} with ${savedView.objects.length} objects`);

                savedView.objects.forEach(objData => {
                    try {
                        if (objData.type === 'i-text' || objData.type === 'text' || objData.type === 'textbox') {
                            const text = new fabric.Textbox(objData.text || 'اكتب هنا', {
                                left: objData.left || 150,
                                top: objData.top || 150,
                                fontSize: objData.fontSize || 20,
                                fill: objData.fill || '#000000',
                                fontFamily: objData.fontFamily || 'Cairo',
                                angle: objData.angle || 0,
                                width: objData.width || 150,
                                scaleX: objData.scaleX || 1,
                                scaleY: objData.scaleY || 1,
                                textBaseline: 'bottom',
                                hasControls: true,
                                hasBorders: true
                            });
                            canvas.add(text);
                        } else if (objData.type === 'image') {
                            if (objData.src) {
                                if (uploadedImagesCache[objData.src]) {
                                    const cachedImg = fabric.util.object.clone(uploadedImagesCache[objData.src]);
                                    cachedImg.set({
                                        left: objData.left || 100,
                                        top: objData.top || 100,
                                        angle: objData.angle || 0,
                                        scaleX: objData.scaleX || 1,
                                        scaleY: objData.scaleY || 1,
                                        hasControls: true,
                                        hasBorders: true
                                    });
                                    canvas.add(cachedImg);
                                } else {
                                    fabric.Image.fromURL(objData.src, function(img) {
                                        if (img) {
                                            uploadedImagesCache[objData.src] = img;
                                            img.set({
                                                left: objData.left || 100,
                                                top: objData.top || 100,
                                                angle: objData.angle || 0,
                                                scaleX: objData.scaleX || 1,
                                                scaleY: objData.scaleY || 1,
                                                hasControls: true,
                                                hasBorders: true
                                            });
                                            canvas.add(img);
                                            canvas.renderAll();
                                        }
                                    });
                                }
                            }
                        }
                    } catch (err) {
                        console.warn('Error recreating object:', err);
                    }
                });
            } else {
                console.log(`Creating new design for view ${index}`);
            }

            loadProductImage(src);

            requestAnimationFrame(() => {
                canvas.renderAll();
            });

            document.querySelectorAll('.wrap-slick3-dots img').forEach(t => t.style.borderColor = 'transparent');
            if (event && event.target) {
                event.target.style.borderColor = 'red';
            }
        } catch (error) {
            console.error('Error changing image:', error);
        }
    }

    // إضافة نص
    function addText() {
        if (!canvas) {
            console.error('Canvas not initialized');
            return;
        }

        try {
            const text = new fabric.Textbox('اكتب هنا', {
                left: 150,
                top: 150,
                fontSize: 20,
                fill: '#000000',
                fontFamily: 'Cairo',
                padding: 5,
                cornerColor: 'red',
                cornerSize: 8,
                transparentCorners: false,
                textBaseline: 'bottom',
                width: 150,
                hasControls: true,
                hasBorders: true
            });

            canvas.add(text);
            canvas.setActiveObject(text);
            canvas.renderAll();
            saveCurrentView();
        } catch (error) {
            console.error('Error adding text:', error);
        }
    }

    // حذف العنصر المحدد
    function deleteSelected() {
        if (!canvas) return;

        try {
            const obj = canvas.getActiveObject();
            if (obj) {
                canvas.remove(obj);
                canvas.renderAll();
                saveCurrentView();
            }
        } catch (error) {
            console.error('Error deleting object:', error);
        }
    }

    // أدوات التحكم
    function setupControls() {
        const textColor = document.getElementById('textColor');
        const fontFamily = document.getElementById('fontFamily');
        const fontSize = document.getElementById('fontSize');
        const rotateText = document.getElementById('rotateText');

        if (textColor) {
            textColor.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj) { obj.set('fill', this.value); canvas.renderAll(); }
            });
        }

        if (fontFamily) {
            fontFamily.addEventListener('change', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('fontFamily', this.value); canvas.renderAll(); }
            });
        }

        if (fontSize) {
            fontSize.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('fontSize', parseInt(this.value)); canvas.renderAll(); }
            });
        }

        if (rotateText) {
            rotateText.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('angle', parseInt(this.value)); canvas.renderAll(); }
            });
        }
    }

    // رفع صورة
    function setupImageUpload() {
        const uploadImage = document.getElementById('uploadImage');
        if (!uploadImage) return;

        uploadImage.addEventListener('change', function(e) {
            if (!canvas) return;

            const file = e.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(event) {
                fabric.Image.fromURL(event.target.result, function(img) {
                    if (img && canvas) {
                        const maxWidth = 200;
                        let scale = 1;
                        if (img.width > maxWidth) {
                            scale = maxWidth / img.width;
                        }

                        img.scale(scale);
                        img.set({
                            left: 100,
                            top: 100,
                            originalWidth: img.width,
                            originalHeight: img.height,
                            hasControls: true,
                            hasBorders: true
                        });

                        canvas.add(img);
                        canvas.setActiveObject(img);
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            };
            reader.readAsDataURL(file);
        });
    }

    // المقاس → الألوان
    function loadColorsBySize() {
        const size = document.getElementById('sizeSelect').value;
        const colorSelect = document.getElementById('colorSelect');
        if (!colorSelect) return;

        colorSelect.innerHTML = '<option value="">اختر اللون</option>';
        if (!size || !variants) return;

        const filtered = variants.filter(v => v.size === size && v.quantity > 0);
        const colors = [...new Set(filtered.map(v => v.color))];

        colors.forEach(color => {
            const option = document.createElement('option');
            option.value = color;
            option.textContent = color;
            colorSelect.appendChild(option);
        });
    }

    function onColorChange() {
        const size = document.getElementById('sizeSelect').value;
        const color = document.getElementById('colorSelect').value;

        if (variants && size && color) {
            const found = variants.find(v => v.size === size && v.color === color);
            if (found) {
                const variantIdInput = document.getElementById('variant_id');
                const availableQtySpan = document.getElementById('availableQty');
                const weightSpan = document.getElementById('weight');
                const materialSpan = document.getElementById('material');

                if (variantIdInput) variantIdInput.value = found.id;
                if (availableQtySpan) availableQtySpan.textContent = found.quantity ?? '--';
                if (weightSpan) weightSpan.textContent = found.weight ?? '--';
                if (materialSpan) materialSpan.textContent = found.material ?? '--';
            }
        }

        if (color && colorImages && colorImages[color.toLowerCase().trim()]) {
            updateThumbnails(colorImages[color.toLowerCase().trim()]);
        }
    }

    // تحديث الـ Thumbnails
    function updateThumbnails(images) {
        const container = document.querySelector('.wrap-slick3-dots');
        if (!container) return;

        container.innerHTML = '';
        images.forEach((img, i) => {
            const el = document.createElement('img');
            let imgSrc = fixImagePath(img);
            el.src = imgSrc;
            el.style.cssText = 'width:60px; cursor:pointer; margin:5px; border:2px solid transparent;';
            el.onclick = function() {
                changeImage(imgSrc, i);
            };
            container.appendChild(el);
        });

        if (images.length > 0) {
            changeImage(fixImagePath(images[0]), 0);
        }
    }

    // Load existing design
    function loadExistingDesign() {
        if (!existingDesign || !existingDesign.designs || !canvas) {
            console.log('No existing design to load');
            return;
        }

        console.log('Loading existing design:', existingDesign);

        try {
            canvasViews = {};

            existingDesign.designs.forEach(viewDesign => {
                const viewIndex = viewDesign.view_index;
                canvasViews[viewIndex] = {
                    objects: [],
                    version: '1.0'
                };

                viewDesign.elements.forEach(el => {
                    if (el.type === 'text') {
                        canvasViews[viewIndex].objects.push({
                            type: 'textbox',
                            text: el.content,
                            left: el.position_x,
                            top: el.position_y,
                            fill: el.color,
                            fontFamily: el.font_family,
                            angle: el.rotation,
                            fontSize: el.font_size || 20,
                            textBaseline: 'bottom',
                            width: 150,
                            scaleX: 1,
                            scaleY: 1
                        });
                    }
                    else if (el.type === 'image') {
                        let imagePath = fixImagePath(el.content);
                        const imageObj = {
                            type: 'image',
                            src: imagePath,
                            left: el.position_x,
                            top: el.position_y,
                            angle: el.rotation
                        };

                        if (el.scale_x && el.scale_y) {
                            imageObj.scaleX = el.scale_x;
                            imageObj.scaleY = el.scale_y;
                            if (el.original_width) imageObj.width = el.original_width;
                            if (el.original_height) imageObj.height = el.original_height;
                        } else if (el.width && el.height) {
                            imageObj.width = el.width;
                            imageObj.height = el.height;
                            imageObj.scaleX = 1;
                            imageObj.scaleY = 1;
                        } else {
                            imageObj.scaleX = 1;
                            imageObj.scaleY = 1;
                        }

                        canvasViews[viewIndex].objects.push(imageObj);
                    }
                });
            });

            const initialView = canvasViews[0] ? 0 : currentView;

            if (canvasViews[initialView] && productImages && productImages[initialView]) {
                canvas.loadFromJSON(canvasViews[initialView], function() {
                    fixImageScales();
                    loadProductImage(fixImagePath(productImages[initialView]));
                    canvas.renderAll();
                    console.log(`Loaded view ${initialView} with ${canvasViews[initialView].objects.length} objects`);
                });
            }
        } catch (error) {
            console.error('Error loading existing design:', error);
        }
    }

    // Fix image scales
    function fixImageScales() {
        if (!canvas) return;

        const objects = canvas.getObjects();
        objects.forEach(obj => {
            if (obj.type === 'image' && obj.needsScaleCalculation) {
                const targetWidth = obj.calculatedWidth;
                const targetHeight = obj.calculatedHeight;

                if (targetWidth && targetHeight && obj.width && obj.height) {
                    const scaleX = targetWidth / obj.width;
                    const scaleY = targetHeight / obj.height;
                    obj.scale(scaleX);
                    obj.set({
                        scaleX: scaleX,
                        scaleY: scaleY
                    });
                }

                delete obj.needsScaleCalculation;
                delete obj.calculatedWidth;
                delete obj.calculatedHeight;
            }
        });
        canvas.renderAll();
    }

    // دالة لحفظ الـ view الحالي بشكل آمن
    function saveCurrentView() {
        if (!canvas) return;

        try {
            const objects = canvas.getObjects();
            const currentObjects = objects.filter(obj => obj !== canvas.backgroundImage);
            const savedObjects = [];

            currentObjects.forEach(obj => {
                try {
                    if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                        savedObjects.push({
                            type: obj.type,
                            text: obj.text,
                            left: obj.left,
                            top: obj.top,
                            fontSize: obj.fontSize,
                            fill: obj.fill,
                            fontFamily: obj.fontFamily,
                            angle: obj.angle,
                            width: obj.width,
                            scaleX: obj.scaleX || 1,
                            scaleY: obj.scaleY || 1,
                            textBaseline: 'bottom',
                            hasControls: true,
                            hasBorders: true
                        });
                    } else if (obj.type === 'image') {
                        savedObjects.push({
                            type: obj.type,
                            src: obj.src,
                            left: obj.left,
                            top: obj.top,
                            angle: obj.angle,
                            scaleX: obj.scaleX || 1,
                            scaleY: obj.scaleY || 1,
                            width: obj.width,
                            height: obj.height,
                            originalWidth: obj.originalWidth,
                            originalHeight: obj.originalHeight,
                            hasControls: true,
                            hasBorders: true
                        });
                    } else {
                        const objJSON = obj.toJSON();
                        savedObjects.push(objJSON);
                    }
                } catch (err) {
                    console.warn('Error saving object:', err);
                }
            });

            canvasViews[currentView] = {
                objects: savedObjects,
                version: '1.0',
                timestamp: Date.now()
            };

            console.log(`View ${currentView} saved with ${savedObjects.length} objects`);
        } catch (error) {
            console.error('Error saving view:', error);
        }
    }

    // Submit handler
    async function handleSubmit() {
        const variantId = document.getElementById('variant_id').value;
        if (!variantId) {
            alert('اختار المقاس واللون الأول ❗');
            return;
        }

        if (!canvas) {
            alert('خطأ في تحميل التصميم ❗');
            return;
        }

        try {
            saveCurrentView();

            const designsPayload = [];
            for (const viewIndex in canvasViews) {
                if (!canvasViews[viewIndex] || !canvasViews[viewIndex].objects) continue;

                const objects = canvasViews[viewIndex].objects || [];
                if (objects.length === 0) continue;

                const elements = objects.map(obj => {
                    if (obj.type === 'image') {
                        return {
                            type: 'image',
                            content: obj.src || null,
                            position_x: Math.round(obj.left || 0),
                            position_y: Math.round(obj.top || 0),
                            width: obj.width ? Math.round(obj.width * (obj.scaleX || 1)) : null,
                            height: obj.height ? Math.round(obj.height * (obj.scaleY || 1)) : null,
                            rotation: Math.round(obj.angle || 0),
                            scale_x: obj.scaleX || 1,
                            scale_y: obj.scaleY || 1,
                            original_width: obj.originalWidth || obj.width || null,
                            original_height: obj.originalHeight || obj.height || null,
                            z_index: obj.zIndex || 0,
                        };
                    }

                    return {
                        type: 'text',
                        content: obj.text || null,
                        position_x: Math.round(obj.left || 0),
                        position_y: Math.round(obj.top || 0),
                        rotation: Math.round(obj.angle || 0),
                        color: obj.fill || null,
                        font_family: obj.fontFamily || null,
                        font_size: obj.fontSize || null,
                        z_index: obj.zIndex || 0,
                    };
                });

                designsPayload.push({ view_index: parseInt(viewIndex), elements });
            }

            const previewImage = canvas.toDataURL({ format: 'png', quality: 0.8 });
            const existingDesignId = document.getElementById('design_id').value;

            const payload = {
                product_id: {{ $product->id }},
                variant_id: variantId,
                view: currentView.toString(),
                designs: designsPayload,
                preview_image: previewImage,
            };

            if (existingDesignId) payload.design_id = existingDesignId;

            const response = await fetch("{{ route('design.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || 'حصل خطأ في حفظ التصميم');
                return;
            }

            const designIdInput = document.getElementById('design_id');
            if (designIdInput) designIdInput.value = data.design_id;

            document.getElementById('addToCartForm').submit();

        } catch (err) {
            console.error('Submit error:', err);
            alert('حصل خطأ، حاول تاني');
        }
    }

    // Initialize everything
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');

        if (!initCanvas()) {
            console.error('Failed to initialize canvas');
            return;
        }

        const sizeSelect = document.getElementById('sizeSelect');
        const colorSelect = document.getElementById('colorSelect');

        if (sizeSelect) sizeSelect.addEventListener('change', loadColorsBySize);
        if (colorSelect) colorSelect.addEventListener('change', onColorChange);

        setupControls();
        setupImageUpload();

        if (productImages && productImages.length > 0 && productImages[0]) {
            loadProductImage(fixImagePath(productImages[0]));
        }

        if (existingVariant) {
            if (sizeSelect) sizeSelect.value = existingVariant.size;
            loadColorsBySize();
            setTimeout(() => {
                if (colorSelect) colorSelect.value = existingVariant.color;
                onColorChange();
            }, 100);
        }

        loadExistingDesign();

        console.log('Initialization complete');
    });
</script> --}}

<script>
    // ============================================================
    // Fix Fabric.js textBaseline error - IMPORTANT!
    // ============================================================
    if (typeof fabric !== 'undefined') {
        if (fabric.Text) {
            fabric.Text.prototype.textBaseline = 'bottom';
        }
        if (fabric.IText) {
            fabric.IText.prototype.textBaseline = 'bottom';
        }
        if (fabric.Textbox) {
            fabric.Textbox.prototype.textBaseline = 'bottom';
        }
    }

    // ============================================================
    // البيانات من Laravel
    // ============================================================
    const variants = @json($product->variants);
    const productImages = @json($baseImages);
    const colorImages = @json($colorImages);
    const existingVariant = @json($existingVariantData ?? null);
    const existingDesign = @json($existingDesign ?? null);

    // ============================================================
    // Fabric.js Setup
    // ============================================================
    let canvas;
    let canvasViews = {};
    let currentView = 0;
    const imageCache = {};
    const uploadedImagesCache = {};

    // Helper function to fix image paths
    function fixImagePath(path) {
        if (!path) return null;
        let cleanPath = path.replace(/^\/design\/edit\//, '');
        if (!cleanPath.startsWith('/') && !cleanPath.startsWith('http')) {
            cleanPath = '/' + cleanPath;
        }
        return cleanPath;
    }

    // دالة لتحميل الصورة بشكل Promise
    function loadImagePromise(src, options = {}) {
        return new Promise((resolve, reject) => {
            if (!src) {
                reject('No image source');
                return;
            }

            fabric.Image.fromURL(src, (img) => {
                resolve(img);
            }, (error) => {
                reject(error);
            }, options);
        });
    }

    // Initialize canvas
    function initCanvas() {
        try {
            const canvasElement = document.getElementById('fabricCanvas');
            if (!canvasElement) {
                console.error('Canvas element not found');
                return false;
            }

            canvas = new fabric.Canvas('fabricCanvas', {
                selection: true,
                preserveObjectStacking: true,
                width: 500,
                height: 500,
                backgroundColor: 'transparent',
                renderOnAddRemove: true
            });

            if (!canvas) {
                console.error('Failed to create fabric canvas');
                return false;
            }

            console.log('Canvas initialized successfully');

            canvas.on('object:modified', function(e) {
                console.log('Object modified:', e.target.type);
                saveCurrentView();
            });

            canvas.on('object:added', function(e) {
                console.log('Object added:', e.target.type);
                saveCurrentView();
            });

            canvas.on('object:removed', function(e) {
                console.log('Object removed');
                saveCurrentView();
            });

            return true;
        } catch (error) {
            console.error('Error initializing canvas:', error);
            return false;
        }
    }

    // تحميل صورة المنتج كـ background
    function loadProductImage(src) {
        if (!canvas || !src) {
            console.error('Canvas not initialized or no image source');
            return;
        }

        let cleanSrc = fixImagePath(src);

        if (imageCache[cleanSrc]) {
            const cachedImg = imageCache[cleanSrc];
            canvas.setBackgroundImage(
                cachedImg,
                canvas.renderAll.bind(canvas),
                {
                    scaleX: canvas.width / cachedImg.width,
                    scaleY: canvas.height / cachedImg.height,
                    crossOrigin: 'anonymous'
                }
            );
            return;
        }

        fabric.Image.fromURL(cleanSrc, function(img) {
            if (img && canvas) {
                imageCache[cleanSrc] = img;
                canvas.setBackgroundImage(
                    img,
                    canvas.renderAll.bind(canvas),
                    {
                        scaleX: canvas.width / img.width,
                        scaleY: canvas.height / img.height,
                        crossOrigin: 'anonymous'
                    }
                );
            }
        }, {
            crossOrigin: 'anonymous'
        });
    }

    // تغيير الصورة - النسخة المتزامنة
// تغيير الصورة - النسخة النهائية مع دعم localStorage
async function changeImage(src, index) {
    if (!canvas) {
        console.error('Canvas not initialized');
        return;
    }

    try {
        // حفظ التصميم الحالي
        await saveCurrentView();

        // تحديث الـ view الحالي
        currentView = index;
        const savedView = canvasViews[index];

        // مسح الكانفاس
        canvas.clear();

        // تحميل الخلفية
        loadProductImage(src);

        // إعادة إنشاء الكائنات من البيانات المحفوظة
        if (savedView && savedView.objects && savedView.objects.length > 0) {
            console.log(`Loading saved design for view ${index} with ${savedView.objects.length} objects`);

            // معالجة النصوص أولاً
            for (const objData of savedView.objects) {
                try {
                    if (objData.type === 'i-text' || objData.type === 'text' || objData.type === 'textbox') {
                        const text = new fabric.Textbox(objData.text || 'اكتب هنا', {
                            left: objData.left || 150,
                            top: objData.top || 150,
                            fontSize: objData.fontSize || 20,
                            fill: objData.fill || '#000000',
                            fontFamily: objData.fontFamily || 'Cairo',
                            angle: objData.angle || 0,
                            width: objData.width || 150,
                            scaleX: objData.scaleX || 1,
                            scaleY: objData.scaleY || 1,
                            textBaseline: 'bottom',
                            hasControls: true,
                            hasBorders: true
                        });
                        canvas.add(text);
                    }
                } catch (err) {
                    console.warn('Error recreating text object:', err);
                }
            }

            // ثم معالجة الصور
            const imagePromises = [];

            for (const objData of savedView.objects) {
                if (objData.type === 'image' && objData.src) {
                    const promise = (async () => {
                        try {
                            let imagePath = objData.src;
                            let img = null;

                            // إذا كانت الصورة مخزنة في localStorage
                            if (imagePath && imagePath.startsWith('local://')) {
                                const imageId = imagePath.replace('local://', '');
                                const base64Image = localStorage.getItem(imageId);

                                if (base64Image) {
                                    console.log('Loading image from localStorage:', imageId);
                                    img = await loadImagePromise(base64Image);
                                } else {
                                    console.warn('Image not found in localStorage:', imageId);
                                }
                            }
                            // إذا كانت صورة عادية
                            else {
                                // تأكد من أن المسار صحيح
                                if (imagePath && !imagePath.startsWith('/') && !imagePath.startsWith('http') && !imagePath.startsWith('data:')) {
                                    imagePath = '/' + imagePath;
                                }

                                // لو الصورة في الكاش
                                if (uploadedImagesCache[imagePath]) {
                                    img = fabric.util.object.clone(uploadedImagesCache[imagePath]);
                                } else {
                                    // تحميل الصورة
                                    img = await loadImagePromise(imagePath);
                                    if (img) {
                                        uploadedImagesCache[imagePath] = img;
                                    }
                                }
                            }

                            if (img) {
                                img.set({
                                    left: objData.left || 100,
                                    top: objData.top || 100,
                                    angle: objData.angle || 0,
                                    scaleX: objData.scaleX || 1,
                                    scaleY: objData.scaleY || 1,
                                    hasControls: true,
                                    hasBorders: true
                                });
                                canvas.add(img);
                            }
                        } catch (err) {
                            console.warn('Error loading image:', objData.src, err);
                        }
                    })();
                    imagePromises.push(promise);
                }
            }

            // انتظار تحميل كل الصور
            if (imagePromises.length > 0) {
                await Promise.all(imagePromises);
            }
        } else {
            console.log(`Creating new design for view ${index}`);
        }

        // إعادة الرسم النهائية
        canvas.renderAll();

        // تحديث الـ thumbnail المحدد
        document.querySelectorAll('.wrap-slick3-dots img').forEach(t => t.style.borderColor = 'transparent');
        if (event && event.target) {
            event.target.style.borderColor = 'red';
        }
    } catch (error) {
        console.error('Error changing image:', error);
    }
}

    // إضافة نص
    function addText() {
        if (!canvas) {
            console.error('Canvas not initialized');
            return;
        }

        try {
            const text = new fabric.Textbox('اكتب هنا', {
                left: 150,
                top: 150,
                fontSize: 20,
                fill: '#000000',
                fontFamily: 'Cairo',
                padding: 5,
                cornerColor: 'red',
                cornerSize: 8,
                transparentCorners: false,
                textBaseline: 'bottom',
                width: 150,
                hasControls: true,
                hasBorders: true
            });

            canvas.add(text);
            canvas.setActiveObject(text);
            canvas.renderAll();
            saveCurrentView();
        } catch (error) {
            console.error('Error adding text:', error);
        }
    }

    // حذف العنصر المحدد
    function deleteSelected() {
        if (!canvas) return;

        try {
            const obj = canvas.getActiveObject();
            if (obj) {
                canvas.remove(obj);
                canvas.renderAll();
                saveCurrentView();
            }
        } catch (error) {
            console.error('Error deleting object:', error);
        }
    }

    // أدوات التحكم
    function setupControls() {
        const textColor = document.getElementById('textColor');
        const fontFamily = document.getElementById('fontFamily');
        const fontSize = document.getElementById('fontSize');
        const rotateText = document.getElementById('rotateText');

        if (textColor) {
            textColor.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj) { obj.set('fill', this.value); canvas.renderAll(); }
            });
        }

        if (fontFamily) {
            fontFamily.addEventListener('change', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('fontFamily', this.value); canvas.renderAll(); }
            });
        }

        if (fontSize) {
            fontSize.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('fontSize', parseInt(this.value)); canvas.renderAll(); }
            });
        }

        if (rotateText) {
            rotateText.addEventListener('input', function() {
                if (!canvas) return;
                const obj = canvas.getActiveObject();
                if (obj && obj.set) { obj.set('angle', parseInt(this.value)); canvas.renderAll(); }
            });
        }
    }

 // رفع صورة - النسخة المعدلة
// رفع صورة - نسخة محسنة
function setupImageUpload() {
    const uploadImage = document.getElementById('uploadImage');
    if (!uploadImage) return;

    uploadImage.addEventListener('change', function(e) {
        if (!canvas) return;

        const file = e.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = async function(event) {
            try {
                const base64Image = event.target.result;
                const img = await loadImagePromise(base64Image);

                if (img && canvas) {
                    const maxWidth = 200;
                    let scale = 1;
                    if (img.width > maxWidth) {
                        scale = maxWidth / img.width;
                    }

                    img.scale(scale);

                    // إنشاء معرف فريد للصورة
                    const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                    // حفظ الصورة في localStorage
                    try {
                        localStorage.setItem(imageId, base64Image);
                        console.log('Image saved to localStorage with ID:', imageId);

                        // حفظ المسار في الصورة
                        img.set({
                            left: 100,
                            top: 100,
                            originalWidth: img.width,
                            originalHeight: img.height,
                            hasControls: true,
                            hasBorders: true,
                            src: 'local://' + imageId  // حفظ المرجع
                        });
                    } catch (e) {
                        console.warn('localStorage full, using base64 directly');
                        img.set({
                            left: 100,
                            top: 100,
                            originalWidth: img.width,
                            originalHeight: img.height,
                            hasControls: true,
                            hasBorders: true,
                            src: base64Image  // حفظ base64 مباشرة
                        });
                    }

                    canvas.add(img);
                    canvas.setActiveObject(img);
                    canvas.renderAll();

                    // حفظ التغيير فوراً
                    await saveCurrentView();
                }
            } catch (err) {
                console.error('Error loading uploaded image:', err);
            }
        };
        reader.readAsDataURL(file);
    });
}

    // المقاس → الألوان
    function loadColorsBySize() {
        const size = document.getElementById('sizeSelect').value;
        const colorSelect = document.getElementById('colorSelect');
        if (!colorSelect) return;

        colorSelect.innerHTML = '<option value="">اختر اللون</option>';
        if (!size || !variants) return;

        const filtered = variants.filter(v => v.size === size && v.quantity > 0);
        const colors = [...new Set(filtered.map(v => v.color))];

        colors.forEach(color => {
            const option = document.createElement('option');
            option.value = color;
            option.textContent = color;
            colorSelect.appendChild(option);
        });
    }

    function onColorChange() {
        const size = document.getElementById('sizeSelect').value;
        const color = document.getElementById('colorSelect').value;

        if (variants && size && color) {
            const found = variants.find(v => v.size === size && v.color === color);
            if (found) {
                const variantIdInput = document.getElementById('variant_id');
                const availableQtySpan = document.getElementById('availableQty');
                const weightSpan = document.getElementById('weight');
                const materialSpan = document.getElementById('material');

                if (variantIdInput) variantIdInput.value = found.id;
                if (availableQtySpan) availableQtySpan.textContent = found.quantity ?? '--';
                if (weightSpan) weightSpan.textContent = found.weight ?? '--';
                if (materialSpan) materialSpan.textContent = found.material ?? '--';
            }
        }

        if (color && colorImages && colorImages[color.toLowerCase().trim()]) {
            updateThumbnails(colorImages[color.toLowerCase().trim()]);
        }
    }

    // تحديث الـ Thumbnails
    function updateThumbnails(images) {
        const container = document.querySelector('.wrap-slick3-dots');
        if (!container) return;

        container.innerHTML = '';
        images.forEach((img, i) => {
            const el = document.createElement('img');
            let imgSrc = fixImagePath(img);
            el.src = imgSrc;
            el.style.cssText = 'width:60px; cursor:pointer; margin:5px; border:2px solid transparent;';
            el.onclick = function() {
                changeImage(imgSrc, i);
            };
            container.appendChild(el);
        });

        if (images.length > 0) {
            changeImage(fixImagePath(images[0]), 0);
        }
    }

    // Load existing design
    async function loadExistingDesign() {
        if (!existingDesign || !existingDesign.designs || !canvas) {
            console.log('No existing design to load');
            return;
        }

        console.log('Loading existing design:', existingDesign);

        try {
            canvasViews = {};

            existingDesign.designs.forEach(viewDesign => {
                const viewIndex = viewDesign.view_index;
                canvasViews[viewIndex] = {
                    objects: [],
                    version: '1.0'
                };

                viewDesign.elements.forEach(el => {
                    if (el.type === 'text') {
                        canvasViews[viewIndex].objects.push({
                            type: 'textbox',
                            text: el.content,
                            left: el.position_x,
                            top: el.position_y,
                            fill: el.color,
                            fontFamily: el.font_family,
                            angle: el.rotation,
                            fontSize: el.font_size || 20,
                            textBaseline: 'bottom',
                            width: 150,
                            scaleX: 1,
                            scaleY: 1
                        });
                    }
                    else if (el.type === 'image') {
                        let imagePath = fixImagePath(el.content);
                        const imageObj = {
                            type: 'image',
                            src: imagePath,
                            left: el.position_x,
                            top: el.position_y,
                            angle: el.rotation
                        };

                        if (el.scale_x && el.scale_y) {
                            imageObj.scaleX = el.scale_x;
                            imageObj.scaleY = el.scale_y;
                            if (el.original_width) imageObj.width = el.original_width;
                            if (el.original_height) imageObj.height = el.original_height;
                        } else if (el.width && el.height) {
                            imageObj.width = el.width;
                            imageObj.height = el.height;
                            imageObj.scaleX = 1;
                            imageObj.scaleY = 1;
                        } else {
                            imageObj.scaleX = 1;
                            imageObj.scaleY = 1;
                        }

                        canvasViews[viewIndex].objects.push(imageObj);
                    }
                });
            });

            const initialView = canvasViews[0] ? 0 : currentView;

            if (canvasViews[initialView] && productImages && productImages[initialView]) {
                // Load the view
                if (canvasViews[initialView].objects.length > 0) {
                    for (const objData of canvasViews[initialView].objects) {
                        try {
                            if (objData.type === 'textbox') {
                                const text = new fabric.Textbox(objData.text || 'اكتب هنا', {
                                    left: objData.left || 150,
                                    top: objData.top || 150,
                                    fontSize: objData.fontSize || 20,
                                    fill: objData.fill || '#000000',
                                    fontFamily: objData.fontFamily || 'Cairo',
                                    angle: objData.angle || 0,
                                    width: objData.width || 150,
                                    scaleX: objData.scaleX || 1,
                                    scaleY: objData.scaleY || 1,
                                    textBaseline: 'bottom',
                                    hasControls: true,
                                    hasBorders: true
                                });
                                canvas.add(text);
                            } else if (objData.type === 'image' && objData.src) {
                                try {
                                    const img = await loadImagePromise(objData.src);
                                    if (img) {
                                        img.set({
                                            left: objData.left || 100,
                                            top: objData.top || 100,
                                            angle: objData.angle || 0,
                                            scaleX: objData.scaleX || 1,
                                            scaleY: objData.scaleY || 1,
                                            hasControls: true,
                                            hasBorders: true
                                        });
                                        canvas.add(img);
                                    }
                                } catch (err) {
                                    console.warn('Error loading image in existing design:', objData.src, err);
                                }
                            }
                        } catch (err) {
                            console.warn('Error loading object:', err);
                        }
                    }
                }

                loadProductImage(fixImagePath(productImages[initialView]));
                canvas.renderAll();
                console.log(`Loaded view ${initialView} with ${canvasViews[initialView].objects.length} objects`);
            }
        } catch (error) {
            console.error('Error loading existing design:', error);
        }
    }

    // Fix image scales
    function fixImageScales() {
        if (!canvas) return;

        const objects = canvas.getObjects();
        objects.forEach(obj => {
            if (obj.type === 'image' && obj.needsScaleCalculation) {
                const targetWidth = obj.calculatedWidth;
                const targetHeight = obj.calculatedHeight;

                if (targetWidth && targetHeight && obj.width && obj.height) {
                    const scaleX = targetWidth / obj.width;
                    const scaleY = targetHeight / obj.height;
                    obj.scale(scaleX);
                    obj.set({
                        scaleX: scaleX,
                        scaleY: scaleY
                    });
                }

                delete obj.needsScaleCalculation;
                delete obj.calculatedWidth;
                delete obj.calculatedHeight;
            }
        });
        canvas.renderAll();
    }

    // دالة لحفظ الـ view الحالي بشكل آمن
 // دالة لحفظ الـ view الحالي بشكل آمن - نسخة متزامنة ومحسنة
async function saveCurrentView() {
    if (!canvas) return;

    try {
        const objects = canvas.getObjects();
        const currentObjects = objects.filter(obj => obj !== canvas.backgroundImage);
        const savedObjects = [];

        // معالجة كل كائن
        for (const obj of currentObjects) {
            try {
                if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                    savedObjects.push({
                        type: obj.type,
                        text: obj.text,
                        left: obj.left,
                        top: obj.top,
                        fontSize: obj.fontSize,
                        fill: obj.fill,
                        fontFamily: obj.fontFamily,
                        angle: obj.angle,
                        width: obj.width,
                        scaleX: obj.scaleX || 1,
                        scaleY: obj.scaleY || 1,
                        textBaseline: 'bottom',
                        hasControls: true,
                        hasBorders: true
                    });
                }
                else if (obj.type === 'image') {
                    let imageSrc = obj.src;

                    // إذا كانت الصورة base64 (بدأت بـ data:image)
                    if (imageSrc && imageSrc.startsWith('data:image')) {
                        console.log('Saving base64 image to cache...');

                        // إنشاء معرف فريد للصورة
                        const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);

                        // تخزين الصورة في localStorage مؤقتاً
                        try {
                            localStorage.setItem(imageId, imageSrc);
                            imageSrc = 'local://' + imageId;
                            console.log('Image saved to localStorage with ID:', imageId);
                        } catch (e) {
                            console.warn('localStorage might be full, trying to clean old items');
                            // تنظيف localStorage قديم
                            for (let i = 0; i < localStorage.length; i++) {
                                const key = localStorage.key(i);
                                if (key && key.startsWith('img_') && Date.now() - parseInt(key.split('_')[1]) > 3600000) {
                                    localStorage.removeItem(key);
                                }
                            }
                            // محاولة مرة أخرى
                            try {
                                localStorage.setItem(imageId, imageSrc);
                                imageSrc = 'local://' + imageId;
                            } catch (e2) {
                                console.error('Cannot save image to localStorage');
                            }
                        }
                    }

                    savedObjects.push({
                        type: obj.type,
                        src: imageSrc,
                        left: obj.left,
                        top: obj.top,
                        angle: obj.angle,
                        scaleX: obj.scaleX || 1,
                        scaleY: obj.scaleY || 1,
                        width: obj.width,
                        height: obj.height,
                        originalWidth: obj.originalWidth,
                        originalHeight: obj.originalHeight,
                        hasControls: true,
                        hasBorders: true
                    });
                }
            } catch (err) {
                console.warn('Error saving object:', err);
            }
        }

        canvasViews[currentView] = {
            objects: savedObjects,
            version: '1.0',
            timestamp: Date.now()
        };

        console.log(`View ${currentView} saved with ${savedObjects.length} objects`);
    } catch (error) {
        console.error('Error saving view:', error);
    }
}

    // Submit handler
    async function handleSubmit() {
        const variantId = document.getElementById('variant_id').value;
        if (!variantId) {
            alert('اختار المقاس واللون الأول ❗');
            return;
        }

        if (!canvas) {
            alert('خطأ في تحميل التصميم ❗');
            return;
        }

        try {
            saveCurrentView();

            const designsPayload = [];
            for (const viewIndex in canvasViews) {
                if (!canvasViews[viewIndex] || !canvasViews[viewIndex].objects) continue;

                const objects = canvasViews[viewIndex].objects || [];
                if (objects.length === 0) continue;

                const elements = objects.map(obj => {
                    if (obj.type === 'image') {
                        return {
                            type: 'image',
                            content: obj.src || null,
                            position_x: Math.round(obj.left || 0),
                            position_y: Math.round(obj.top || 0),
                            width: obj.width ? Math.round(obj.width * (obj.scaleX || 1)) : null,
                            height: obj.height ? Math.round(obj.height * (obj.scaleY || 1)) : null,
                            rotation: Math.round(obj.angle || 0),
                            scale_x: obj.scaleX || 1,
                            scale_y: obj.scaleY || 1,
                            original_width: obj.originalWidth || obj.width || null,
                            original_height: obj.originalHeight || obj.height || null,
                            z_index: obj.zIndex || 0,
                        };
                    }

                    return {
                        type: 'text',
                        content: obj.text || null,
                        position_x: Math.round(obj.left || 0),
                        position_y: Math.round(obj.top || 0),
                        rotation: Math.round(obj.angle || 0),
                        color: obj.fill || null,
                        font_family: obj.fontFamily || null,
                        font_size: obj.fontSize || null,
                        z_index: obj.zIndex || 0,
                    };
                });

                designsPayload.push({ view_index: parseInt(viewIndex), elements });
            }

            const previewImage = canvas.toDataURL({ format: 'png', quality: 0.8 });
            const existingDesignId = document.getElementById('design_id').value;

            const payload = {
                product_id: {{ $product->id }},
                variant_id: variantId,
                view: currentView.toString(),
                designs: designsPayload,
                preview_image: previewImage,
            };

            if (existingDesignId) payload.design_id = existingDesignId;

            const response = await fetch("{{ route('design.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const data = await response.json();

            if (!response.ok) {
                alert(data.error || 'حصل خطأ في حفظ التصميم');
                return;
            }

            const designIdInput = document.getElementById('design_id');
            if (designIdInput) designIdInput.value = data.design_id;

            document.getElementById('addToCartForm').submit();

        } catch (err) {
            console.error('Submit error:', err);
            alert('حصل خطأ، حاول تاني');
        }
    }

    // Initialize everything
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM loaded, initializing...');

        if (!initCanvas()) {
            console.error('Failed to initialize canvas');
            return;
        }

        const sizeSelect = document.getElementById('sizeSelect');
        const colorSelect = document.getElementById('colorSelect');

        if (sizeSelect) sizeSelect.addEventListener('change', loadColorsBySize);
        if (colorSelect) colorSelect.addEventListener('change', onColorChange);

        setupControls();
        setupImageUpload();

        if (productImages && productImages.length > 0 && productImages[0]) {
            loadProductImage(fixImagePath(productImages[0]));
        }

        if (existingVariant) {
            if (sizeSelect) sizeSelect.value = existingVariant.size;
            loadColorsBySize();
            setTimeout(() => {
                if (colorSelect) colorSelect.value = existingVariant.color;
                onColorChange();
            }, 100);
        }

        setTimeout(() => {
            loadExistingDesign();
        }, 100);

        console.log('Initialization complete');
    });
// تنظيف localStorage القديم بشكل دوري
function cleanOldLocalStorage() {
    const oneHourAgo = Date.now() - 3600000;
    for (let i = 0; i < localStorage.length; i++) {
        const key = localStorage.key(i);
        if (key && key.startsWith('img_')) {
            const timestamp = parseInt(key.split('_')[1]);
            if (timestamp && timestamp < oneHourAgo) {
                localStorage.removeItem(key);
                console.log('Cleaned old image from localStorage:', key);
            }
        }
    }
}

// Run cleanup every hour
setInterval(cleanOldLocalStorage, 3600000);
</script>
@endsection



{{-- /////////////////////////////////////////////////////////////////////////////////////////////////////// --}}


<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Arabic Tshirt Designer</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        body {
            margin: 0;
            background: #d9d9d9;
            font-family: 'Cairo', Arial, sans-serif;
            overflow-x: hidden;
        }

        .designer-wrapper {
            display: flex;
            min-height: 100vh;
            padding: 20px;
        }

        .sidebar {
            width: 86px;
            background: #1f1c1d;
            display: flex;
            flex-direction: column;
            align-items: center;
            overflow: hidden;
            padding-top: 14px;
            flex-shrink: 0;
            border-radius: 14px 0 0 14px;
        }

        .new-badge {
            background: white;
            color: #ff5b1f;
            padding: 4px 12px;
            border-radius: 30px;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .menu-btn {
            width: 100%;
            border: none;
            background: transparent;
            color: #d8d8d8;
            padding: 18px 5px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 8px;
            transition: .2s;
            border-right: 3px solid transparent;
            font-size: 14px;
            cursor: pointer;
        }

        .menu-btn.active {
            background: #f3f3f3;
            color: #222;
            border-right-color: #3047ff;
        }

        .sidebar-icon {
            font-size: 32px;
            line-height: 1;
        }

        .left-panel {
            width: 560px;
            background: #f3f3f3;
            border-radius: 0 14px 14px 0;
            overflow: hidden;
            flex-shrink: 0;
            display: flex;
            flex-direction: column;
        }

        .top-header {
            height: 58px;
            border-bottom: 1px solid #e1e1e1;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
            position: relative;
            font-size: 20px;
            letter-spacing: 3px;
        }

        .close-btn,
        .back-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            font-size: 28px;
            cursor: pointer;
        }

        .close-btn {
            right: 16px;
        }

        .back-btn {
            left: 16px;
        }

        .panel-content {
            padding: 24px;
            height: calc(100vh - 58px);
            overflow-y: auto;
        }

        .panel-box {
            display: none;
        }

        .panel-box.active {
            display: block;
        }

        .main-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
        }

        .feature-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            max-width: 340px;
            margin: auto;
        }

        .feature-item {
            text-align: center;
            font-size: 18px;
        }

        .feature-icon {
            width: 90px;
            height: 70px;
            border: 2px solid #6f7480;
            border-radius: 6px;
            margin: auto auto 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
            font-size: 40px;
            color: #3047ff;
        }

        .preview-section {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            direction: rtl;
            padding: 20px;
        }

        .preview-card {
            background: white;
            border-radius: 20px;
            padding: 20px;
            width: 100%;
            max-width: 950px;
        }

        .canvas-box {
            width: 100%;
            max-width: 500px;
            margin: auto;
        }

        #fabricCanvas {
            width: 100%;
            border-radius: 20px;
            border: 1px solid #ddd;
            background: white;
        }

        .tools-box {
            background: white;
            border: 1px solid #eee;
            border-radius: 20px;
            padding: 20px;
        }

        @media(max-width:991px) {
            .designer-wrapper {
                flex-direction: column;
                padding: 0;
            }

            .left-panel {
                width: 100%;
                border-radius: 0;
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                border-radius: 0;
                flex-direction: row;
                overflow-x: auto;
            }

            .panel-content {
                height: auto;
            }
        }
    </style>
</head>

<body>

    <div class="designer-wrapper">

        <div class="sidebar">

            <div class="new-badge">New</div>

            <button class="menu-btn active" data-target="home">
                <span class="sidebar-icon">✦</span>
                تصميم AI
            </button>

            <button class="menu-btn" data-target="upload">
                <span class="sidebar-icon">☁</span>
                رفع
            </button>

            <button class="menu-btn" data-target="text">
                <span class="sidebar-icon">T</span>
                إضافة نص
            </button>

            <button class="menu-btn" data-target="art">
                <span class="sidebar-icon">▣</span>
                إضافة رسومات
            </button>

            <button class="menu-btn" data-target="details">
                <span class="sidebar-icon">💧</span>
                تفاصيل المنتج
            </button>

        </div>

        <div class="left-panel">

            <div class="top-header">
                <button class="back-btn">‹</button>
                تفاصيل المنتج والتصميم
                <button class="close-btn">✕</button>
            </div>

            <div class="panel-content">

                <div id="home" class="panel-box active">

                    <h2 class="main-title">ماذا تريد أن تفعل؟</h2>

                    <div class="feature-grid">

                        <div class="feature-item">
                            <div class="feature-icon">☁</div>
                            رفع تصميم
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">T</div>
                            Add Text
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">▣</div>
                            Add Art
                        </div>

                        <div class="feature-item">
                            <div class="feature-icon">👕</div>
                            تغيير المنتج
                        </div>

                    </div>
                </div>

                <div id="upload" class="panel-box">

                    <h2 class="main-title">رفع تصميم</h2>

                    <div class="text-center bg-white p-5 rounded-4 border">

                        <input type="file" id="uploadImage" class="form-control form-control-lg mb-4">

                        <button class="btn btn-primary btn-lg px-5">
                            رفع الملف
                        </button>

                        <div class="mt-4 fs-5 text-muted">
                            PNG - JPG - SVG - PDF
                        </div>

                    </div>

                </div>

                <div id="text" class="panel-box">

                    <h2 class="main-title">إضافة نص</h2>

                    <input type="text" class="form-control form-control-lg mb-4" placeholder="اكتب النص هنا">

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            نوع الخط
                        </label>

                        <select id="fontFamily" class="form-select form-select-lg">
                            <option>Arial</option>
                            <option>Cairo</option>
                            <option>Tajawal</option>
                            <option>Tahoma</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            لون الخط
                        </label>

                        <input type="color" id="textColor" class="form-control form-control-color" value="#000000">
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">
                            حجم الخط
                        </label>

                        <input type="range" id="fontSize" min="10" max="100" value="20" class="form-range">
                    </div>

                    <button class="btn btn-primary btn-lg w-100">
                        ➕ أضف للتصميم
                    </button>

                </div>

                <div id="art" class="panel-box">

                    <h2 class="main-title">تصنيفات الرسومات</h2>

                    <input type="text" class="form-control form-control-lg mb-4" placeholder="ابحث عن رسومات">

                    <div class="row g-3">

                        <div class="col-6">
                            <div class="tools-box text-center">
                                الأكثر شهرة
                            </div>
                        </div>

                        <div class="col-6">
                            <div class="tools-box text-center">
                                إيموجي
                            </div>
                        </div>

                    </div>

                </div>

                <div id="details" class="panel-box">

                    <h2 class="main-title">Product Details</h2>

                    <div class="tools-box">

                        <h5 class="fw-bold mb-3">ألوان المنتج</h5>

                        <div class="d-flex gap-2 flex-wrap mb-4">
                            <div style="width:35px;height:35px;background:#111827;border-radius:8px"></div>
                            <div style="width:35px;height:35px;background:#2563eb;border-radius:8px"></div>
                            <div style="width:35px;height:35px;background:#dc2626;border-radius:8px"></div>
                        </div>

                        <h5 class="fw-bold mb-3">المقاسات</h5>

                        <div class="d-flex gap-2 flex-wrap">
                            <span class="badge text-bg-light p-3">S</span>
                            <span class="badge text-bg-light p-3">M</span>
                            <span class="badge text-bg-light p-3">L</span>
                            <span class="badge text-bg-light p-3">XL</span>
                        </div>

                    </div>
                </div>

            </div>

            <div class="preview-section">

                <div class="preview-card w-100">

                    <div class="row g-4 align-items-center">

                        <div class="col-md-2">

                            <div class="d-flex flex-column gap-3 align-items-center">

                                <img src="https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?q=80&w=400"
                                    class="img-fluid rounded-4 border border-primary" alt="front">

                                <img src="https://images.unsplash.com/photo-1503341504253-dff4815485f1?q=80&w=400"
                                    class="img-fluid rounded-4" alt="back">

                            </div>

                        </div>

                        <div class="col-md-7">
                            <div class="canvas-box">
                                <canvas id="fabricCanvas" width="500" height="500"></canvas>
                            </div>
                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

    </div>

    <script>
        const buttons = document.querySelectorAll('.menu-btn');
  const panels = document.querySelectorAll('.panel-box');

  buttons.forEach(button => {

    button.addEventListener('click', function () {

      buttons.forEach(btn => btn.classList.remove('active'));
      panels.forEach(panel => panel.classList.remove('active'));

      this.classList.add('active');

      const target = this.getAttribute('data-target');
      document.getElementById(target).classList.add('active');

    });

  });

    </script>

</body>

</html>




{{-- أخر حاجه فى صفحه التعديل --}}
@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --sidebar-n-bg: #1f1c1d;
        --main-bg: #f3f3f3;
        --border-color: #e1e1e1;
        --accent-blue: #ff6e26;
    }

    .designer-container {
        display: flex;
        min-height: calc(100vh - 90px);
    }

    /* sidebar-n */
    .sidebar-n {
        width: 90px;
        background-color: #222;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 20px;
        z-index: 1000;
        flex-shrink: 0;
    }

    .new-tag {
        background: white;
        color: #ff5b1f;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 10px;
        border-radius: 20px;
        margin-bottom: 15px;
    }

    .nav-item-n {
        width: 100%;
        background: none;
        border: none;
        color: #d8d8d8;
        padding: 18px 5px;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 13px;
        cursor: pointer;
        border-left: 4px solid transparent;
        transition: 0.2s;
    }

    .nav-item-n.active {
        background-color: white;
        color: #222;
        border-right: 4px solid var(--accent-blue);
    }

    .panel-v {
        width: 100%;
        max-width: 500px;
        background-color: white;
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .panel-v-header {
        height: 65px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
        position: relative;
    }

    .panel-v-header #header-title {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        font-weight: bold;
        font-size: 16px;
    }

    .btn-header {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #555;
        padding: 5px;
    }

    .panel-v-body {
        padding: 25px;
        overflow-y: auto;
        flex-grow: 1;
    }

    /* Grid Home */
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .icon-wrapper {
        border: 1px solid #6f7480;
        border-radius: 8px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .icon-wrapper i {
        font-size: 45px;
        color: var(--accent-blue);
    }

    .bor10 {
        width: 100% !important;
        margin-top: 50px !important;
        clear: both !important;
    }

    .feature-card {
        cursor: pointer;
        text-align: center;
    }

    .feature-card:hover .icon-wrapper {
        border-color: var(--accent-blue);
        background: #f8f9ff;
    }

    /* Content Styling */
    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .main-title {
        font-size: 26px;
        font-weight: 700;
        text-align: right;
        margin-bottom: 35px;
    }

    /* Upload Area */
    .upload-zone {
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        margin-bottom: 25px;
    }

    .btn-upload-main {
        background: var(--accent-blue);
        color: white;
        font-weight: 700;
        padding: 10px 25px;
    }

    /* Tools Styling (Product & Text) */
    .color-swatch {
        width: 25px;
        height: 25px;
        border-radius: 50px;
        border: 1px solid #ddd;
        cursor: pointer;
    }

    .color-swatch.active {
        outline: 2px solid var(--accent-blue);
        outline-offset: 2px;
    }

    .size-btn {
        border: 1px solid #ddd;
        background: white;
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: 600;
    }

    .tool-label {
        font-weight: 700;
        margin-bottom: 10px;
        display: block;
        font-size: 14px;
    }

    /* Preview Area */
    .preview-pane {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #eee;
    }

    .tshirt-mockup {
        max-width: 80%;
        mix-blend-mode: multiply;
    }

    /* =============================================
           FIX #2: Canvas Overlay — يجبر المستخدم يختار مقاس ولون أول
           ============================================= */
    #canvasOverlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.55);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999;
        border-radius: 4px;
        cursor: pointer;
        transition: opacity 0.3s;
    }

    #canvasOverlay .overlay-icon {
        font-size: 48px;
        color: #fff;
        margin-bottom: 12px;
    }

    #canvasOverlay .overlay-text {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        text-align: center;
        padding: 0 20px;
    }

    #canvasOverlay .overlay-btn {
        margin-top: 16px;
        background: var(--accent-blue);
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
    }

    @media (max-width: 991px) {
        .designer-container {
            flex-direction: column;
        }

        .sidebar-n {
            width: 100%;
            flex-direction: row;
            justify-content: space-around;
            padding: 10px;
        }

        .panel-v {
            max-width: 100%;
        }
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

                        <!-- الصور الصغيرة -->
                        <div class="wrap-slick3-dots">
                            <ul class="slick3-dots" role="tablist">
                                <li class="slick-active" role="presentation">
                                    @foreach ($baseImages as $index => $img)
                                    <img src="{{ asset($img) }}"
                                        style="width:70px;height:70px;object-fit:cover;cursor:pointer;margin-bottom:10px;"
                                        onclick="changeImage('{{ asset($img) }}', {{ $index }})">
                                    @endforeach
                                </li>
                            </ul>
                        </div>

                        <!-- الـ Canvas -->
                        <div class="slick3 gallery-lb flex-grow-1">
                            <div id="designArea" style="position:relative;width:100%;max-width:500px;">

                                <canvas id="fabricCanvas" width="500" height="500"
                                    style="width:100%;display:block;"></canvas>

                                <!-- FIX #2: Overlay يمنع التصميم قبل اختيار المقاس واللون -->
                                <div id="canvasOverlay">
                                    <i class="bi bi-palette overlay-icon"></i>
                                    <div class="overlay-text">اختر المقاس واللون أولاً لبدء التصميم</div>
                                    <button class="overlay-btn" onclick="navigateTo('details')">اختر المقاس
                                        واللون</button>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- التفاصيل -->
            <div class="col-md-6 col-lg-5 p-b-30">
                <div class="p-l-50 p-lr-0-lg text-right">

                    <div class="designer-container" dir="rtl">
                        <aside class="sidebar-n">
                            <div class="new-tag">New</div>
                            <button class="nav-item-n" onclick="navigateTo('upload')" id="btn-upload"><i
                                    class="bi bi-cloud-arrow-up"></i><span>رفع</span></button>
                            <button class="nav-item-n" onclick="navigateTo('text')" id="btn-text"><i
                                    class="bi bi-fonts"></i><span>نص</span></button>
                            <button class="nav-item-n" onclick="navigateTo('art')" id="btn-art"><i
                                    class="bi bi-palette"></i><span>رسومات</span></button>
                            <button class="nav-item-n" onclick="navigateTo('details')" id="btn-details"><i
                                    class="bi bi-info-circle"></i><span>التفاصيل</span></button>
                        </aside>

                        <main class="panel-v">
                            <div class="panel-v-header">
                                <button class="btn-header" id="back-btn" onclick="goBack()"
                                    style="visibility: hidden;">‹</button>
                                <span id="header-title" class="fw-bold">تفاصيل المنتج والتصميم</span>
                                <button class="btn-header" id="closeDesignerBtn" onclick="resetToHome()"
                                    style="display: none;">✕</button>
                            </div>

                            <div class="panel-v-body">

                                <section id="sec-home" class="content-section active">
                                    <h2 class="main-title">معلومات المنتج</h2>
                                    <h4 class="mtext-105 cl2 js-name-detail p-b-14 black">{{ $product->name }}</h4>
                                    <span class="mtext-106 black cl2">{{ $product->price }} ج.م</span>
                                    <p class="stext-102 cl3 p-t-23">الكميه المتاحة : <span id="availableQty">{{
                                            $product->quantity }}</span></p>
                                    <p class="stext-102 cl3 p-t-23">{{ $product->description }}</p>
                                    <div class="feature-grid">
                                        <div class="feature-card" onclick="navigateTo('upload')">
                                            <div class="icon-wrapper"><i class="bi bi-cloud-arrow-up"></i></div>
                                            <div class="feature-title">رفع تصميم</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('text')">
                                            <div class="icon-wrapper"><i class="bi bi-fonts"></i></div>
                                            <div class="feature-title">إضافة نص</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('art')">
                                            <div class="icon-wrapper"><i class="bi bi-palette"></i></div>
                                            <div class="feature-title">إضافة رسومات</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('details')">
                                            <div class="icon-wrapper"><i class="bi bi-tag"></i></div>
                                            <div class="feature-title">تغيير المنتج</div>
                                        </div>
                                    </div>
                                </section>

                                <section id="sec-upload" class="content-section">
                                    <h2 class="main-title">رفع صورة</h2>
                                    <div class="upload-zone">
                                        <label for="uploadImageInput" class="btn btn-upload-main mb-3">تصفح جهاز
                                            الكمبيوتر</label>
                                        <input type="file" id="uploadImageInput" accept="image/*" hidden>
                                        <p class="fw-bold">أو اسحب وأفلت في أي مكان</p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">حجم الصورة</label>
                                        <input type="range" id="imageSize" min="10" max="80" value="20"
                                            class="form-range">
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">تدوير الصورة</label>
                                        <input type="range" id="imageRotate" min="0" max="360" class="form-range">
                                    </div>
                                </section>

                                <section id="sec-text" class="content-section">
                                    <h2 class="main-title">إضافة نص</h2>
                                    <input type="text" onclick="addText()" class="form-control form-control-lg mb-4"
                                        placeholder="اكتب النص هنا">
                                    <div class="mb-3">
                                        <label class="tool-label">نوع الخط</label>
                                        <select class="form-select" id="fontFamily">
                                            <option value="Cairo">Cairo (افتراضي)</option>
                                            <option value="Arial">Arial</option>
                                            <option value="Tahoma">Tahoma</option>
                                            <option value="Verdana">Verdana</option>
                                            <option value="Courier New">Courier</option>
                                            <option value="Tajawal">Tajawal</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="tool-label">لون الخط</label>
                                        <input type="color" id="textColor" class="form-control form-control-color w-100"
                                            value="#3047ff">
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">حجم الخط</label>
                                        <input type="range" id="textSize" min="10" max="80" value="20"
                                            class="form-range">
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">تدوير النص</label>
                                        <input type="range" id="textRotate" min="0" max="360" class="form-range">
                                    </div>
                                </section>

                                <section id="sec-art" class="content-section">
                                    <h2 class="main-title">تصنيفات الرسومات</h2>
                                    <input type="text" class="form-control mb-4" placeholder="ابحث عن رسومات...">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="border p-3 rounded text-center fw-bold bg-light cursor-pointer">
                                                🔥 الأكثر شهرة</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border p-3 rounded text-center fw-bold bg-light cursor-pointer">
                                                🤩 إيموجي</div>
                                        </div>
                                    </div>
                                </section>

                                <section id="sec-details" class="content-section">
                                    <h2 class="main-title">تفاصيل المنتج</h2>

                                    <!-- المقاسات -->
                                    <div class="mb-4">
                                        <label class="tool-label">المقاسات</label>
                                        <div class="d-flex gap-2 flex-wrap" id="sizesContainer">
                                            @php
                                            $sizes = $product->variants
                                            ->where('quantity', '>', 0)
                                            ->pluck('size')
                                            ->unique();
                                            @endphp
                                            @foreach ($sizes as $size)
                                            <button type="button" class="size-btn" data-size="{{ $size }}">{{ $size
                                                }}</button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- الألوان -->
                                    <div class="mb-4">
                                        <label class="tool-label">ألوان المنتج المتوفرة</label>
                                        <div class="d-flex gap-2 flex-wrap" id="colorsContainer">
                                            <p class="text-muted" id="noColorsMsg">اختر مقاس أولاً</p>
                                        </div>
                                    </div>

                                    <!-- الفورم -->
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST"
                                        id="addToCartForm">
                                        @csrf
                                        <input type="hidden" name="cart_item_id" value="{{ request('cart_item_id') }}">
                                        <input type="hidden" name="variant_id" id="variant_id">
                                        <input type="hidden" name="design_id" id="design_id">
                                        <button type="button" onclick="handleSubmit()" class="zoom-btn m-t-20"
                                            dir="ltr">
                                            <span class="icon">→</span>
                                            <span class="btn-text"> إضافة إلى السلة </span>
                                            <span class="hover-bg"></span>
                                        </button>
                                    </form>
                                </section>

                            </div>
                        </main>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <div class="tab01">
                    <ul class="nav nav-tabs" role="tablist" dir="rtl">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#description" role="tab"
                                aria-expanded="true">وصف المنتج</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#information" role="tab"
                                aria-expanded="false">معلومات إضافية</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#reviews" role="tab"
                                aria-expanded="false">التعليقات</a>
                        </li>
                    </ul>

                    <div class="tab-content p-t-43">
                        <div class="tab-pane fade active show" id="description" role="tabpanel" aria-expanded="true"
                            dir="rtl">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">{{ $product->description }}</p>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="information" role="tabpanel" aria-expanded="false" dir="rtl">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">وزن</span>
                                            <span id="weight">--</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">خامات</span>
                                            <span id="material">--</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">الألوان المتاحة</span>
                                            {{ $product->variants->where('quantity', '>',
                                            0)->pluck('color')->unique()->implode('، ') }}
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">المقاسات</span>
                                            @php
                                            $sizes = $product->variants
                                            ->where('quantity', '>', 0)
                                            ->pluck('size')
                                            ->unique();
                                            @endphp
                                            {{ $sizes->implode(' , ') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-expanded="false">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <div class="p-b-30 m-lr-15-sm">

                                        @forelse($product->reviews as $review)
                                        <div class="flex-w flex-t p-b-68" dir="rtl">
                                            <div class="wrap-pic-s size-109 bor0 of-hidden m-l-18 m-t-6">
                                                <x-user-avatar :user="$review->user" alt="AVATAR" />
                                            </div>
                                            <div class="size-207">
                                                <div class="flex-w flex-sb-m p-b-17">
                                                    <span class="mtext-107 cl2 black">{{ $review->name }}</span>
                                                    <span class="fs-18 cl11">
                                                        @php
                                                        $fullStars = floor($review->rating);
                                                        $halfStar = $review->rating - $fullStars >= 0.5;
                                                        @endphp
                                                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$fullStars) <i
                                                            class="zmdi zmdi-star"></i>
                                                            @elseif($i == $fullStars + 1 && $halfStar)
                                                            <i class="zmdi zmdi-star-half"></i>
                                                            @else
                                                            <i class="zmdi zmdi-star-outline"></i>
                                                            @endif
                                                            @endfor
                                                    </span>
                                                </div>
                                                <p class="stext-102 cl6" dir="rtl">{{ $review->message }}
                                                </p>
                                                <small class="stext-102 cl8" style="font-size: 12px;">{{
                                                    $review->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="alert alert-info text-center" dir="rtl"
                                            style="background: #f8f9fa; border: 1px solid #d1ecf1; color: #0c5460; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                            <i class="zmdi zmdi-comment-outline" style="font-size: 24px;"></i>
                                            <p style="margin-top: 10px; margin-bottom: 0;">لا توجد تعليقات على هذا
                                                المنتج بعد. كن أول من يقيّم!</p>
                                        </div>
                                        @endforelse

                                        <form class="w-full" method="POST" action="{{ route('storeReview') }}"
                                            id="reviewForm">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <h5 class="mtext-108 black cl2 p-b-7" dir="rtl">إضافة مراجعة</h5>
                                            <p class="stext-102 cl6" dir="rtl">لن يتم نشر عنوان بريدك الإلكتروني.
                                                الحقول
                                                المطلوبة مُشار إليها بعلامة *</p>

                                            <div class="flex-w flex-m p-t-50 p-b-23" dir="rtl">
                                                <span class="stext-102 cl3 m-l-16">ما هو تقييمك؟</span>
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
                                                    <label class="stext-102 cl3" for="message">اكتب تقييمك <span
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
                                                        value="{{ old('name', auth()->check() ? auth()->user()->name : session('review_data.name')) }}" @auth readonly @endauth required>
                                                    @error('name')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6 p-b-5">
                                                    <label class="stext-102 cl3" for="email">البريد الإلكتروني
                                                        <span class="text-danger">*</span></label>
                                                    <input class="size-111 bor8 stext-102 cl2 black p-lr-20" id="email"
                                                        type="email" name="email"
                                                        value="{{ old('email', auth()->check() ? auth()->user()->email : session('review_data.email')) }}"
                                                        @auth readonly @endauth required>
                                                    @error('email')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>

                                            <button type="submit"
                                                class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">إرسال</button>
                                        </form>

                                        @if (session('success'))
                                        <div class="alert alert-success text-center" dir="rtl"
                                            style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 10px; margin-top: 20px;">
                                            <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
                                        </div>
                                        @endif

                                        @if ($errors->any())
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
    </div>
</section>

{{-- Fonts & Fabric.js --}}
<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<script>
    // ============================================================
        // Fix Fabric.js textBaseline error
        // ============================================================
        if (typeof fabric !== 'undefined') {
            if (fabric.Text) fabric.Text.prototype.textBaseline = 'alphabetic';
            if (fabric.IText) fabric.IText.prototype.textBaseline = 'alphabetic';
            if (fabric.Textbox) fabric.Textbox.prototype.textBaseline = 'alphabetic';
        }

        // ============================================================
        // البيانات من Laravel
        // ============================================================
        const variants = @json($product->variants);
        const productImages = @json($baseImages);
        const colorImages = @json($colorImages);
        const existingVariant = @json($existingVariantData ?? null);
        const existingDesign = @json($existingDesign ?? null);

        // ============================================================
        // Fabric.js Setup
        // ============================================================
        let canvas;
        let canvasViews = {};
        let currentView = 0;
        const imageCache = {};
        const uploadedImagesCache = {};

        // -------------------------------------------------------
        // Helper: تصحيح المسارات
        // -------------------------------------------------------
        function fixImagePath(path) {
            if (!path) return null;
            let cleanPath = path.replace(/^\/design\/edit\//, '');
            if (!cleanPath.startsWith('/') && !cleanPath.startsWith('http')) {
                cleanPath = '/' + cleanPath;
            }
            return cleanPath;
        }

        // -------------------------------------------------------
        // Helper: تحميل صورة Fabric بشكل Promise
        // -------------------------------------------------------
        function loadImagePromise(src, options = {}) {
            return new Promise((resolve, reject) => {
                if (!src) {
                    reject('No image source');
                    return;
                }
                const defaultOpts = {
                    crossOrigin: 'anonymous',
                    ...options
                };
                fabric.Image.fromURL(
                    src,
                    (img) => {
                        if (img) resolve(img);
                        else reject('Failed to load image: ' + src);
                    },
                    defaultOpts
                );
            });
        }

        // -------------------------------------------------------
        // Initialize canvas
        // -------------------------------------------------------
        function initCanvas() {
            try {
                const canvasElement = document.getElementById('fabricCanvas');
                if (!canvasElement) {
                    console.error('Canvas element not found');
                    return false;
                }

                canvas = new fabric.Canvas('fabricCanvas', {
                    selection: true,
                    preserveObjectStacking: true,
                    width: 500,
                    height: 500,
                    backgroundColor: 'transparent',
                    renderOnAddRemove: true
                });

                if (!canvas) {
                    console.error('Failed to create fabric canvas');
                    return false;
                }

                fabric.Object.prototype.transparentCorners = false;
                fabric.Object.prototype.cornerStyle = 'circle';
                fabric.Object.prototype.cornerColor = '#ffffff';
                fabric.Object.prototype.cornerStrokeColor = '#999';
                fabric.Object.prototype.borderColor = '#4A90E2';
                fabric.Object.prototype.cornerSize = 18;

                // زر الحذف العالمي
                fabric.Object.prototype.controls.deleteControl = new fabric.Control({
                    x: 0.5,
                    y: -0.5,
                    offsetY: -10,
                    offsetX: 10,
                    cursorStyle: 'pointer',
                    mouseUpHandler: deleteObject,
                    render: renderDeleteIcon,
                    cornerSize: 24
                });

                canvas.on('object:modified', () => saveCurrentView());
                canvas.on('object:added', () => saveCurrentView());
                canvas.on('object:removed', () => saveCurrentView());

                console.log('Canvas initialized successfully');
                return true;
            } catch (error) {
                console.error('Error initializing canvas:', error);
                return false;
            }
        }

        function applyCustomControls(obj) {
            obj.set({
                transparentCorners: false,
                cornerStyle: 'circle',
                cornerColor: '#ffffff',
                cornerStrokeColor: '#999',
                borderColor: '#4A90E2',
                cornerSize: 18,
                padding: 8
            });
            obj.controls.deleteControl = new fabric.Control({
                x: 0.5,
                y: -0.5,
                offsetY: 0,
                offsetX: 0,
                cursorStyle: 'pointer',
                mouseUpHandler: deleteObject,
                render: renderDeleteIcon,
                cornerSize: 24
            });
        }

        // -------------------------------------------------------
        // تحميل صورة المنتج كـ background
        // -------------------------------------------------------
        function loadProductImage(src) {
            if (!canvas || !src) return;
            const cleanSrc = fixImagePath(src);

            if (imageCache[cleanSrc]) {
                const cachedImg = imageCache[cleanSrc];
                canvas.setBackgroundImage(cachedImg, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / cachedImg.width,
                    scaleY: canvas.height / cachedImg.height,
                    crossOrigin: 'anonymous'
                });
                return;
            }

            fabric.Image.fromURL(cleanSrc, function(img) {
                if (img && canvas) {
                    imageCache[cleanSrc] = img;
                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                        scaleX: canvas.width / img.width,
                        scaleY: canvas.height / img.height,
                        crossOrigin: 'anonymous'
                    });
                }
            }, {
                crossOrigin: 'anonymous'
            });
        }

        // -------------------------------------------------------
        // تغيير الصورة مع حفظ واسترجاع كامل للتصميم
        // -------------------------------------------------------
        async function changeImage(src, index) {
            if (!canvas) return;

            try {
                await saveCurrentView();
                currentView = index;
                const savedView = canvasViews[index];

                canvas.clear();
                loadProductImage(src);

                if (savedView && savedView.objects && savedView.objects.length > 0) {
                    console.log(`Loading saved design for view ${index} with ${savedView.objects.length} objects`);

                    // ---- النصوص أولاً ----
                    for (const objData of savedView.objects) {
                        if (objData.type === 'i-text' || objData.type === 'text' || objData.type === 'textbox') {
                            try {
                                const text = new fabric.Text(objData.text || 'اكتب هنا', {
                                    left: objData.left || 150,
                                    top: objData.top || 150,
                                    fontSize: objData.fontSize || 20,
                                    fill: objData.fill || '#000000',
                                    fontFamily: objData.fontFamily || 'Cairo',
                                    angle: objData.angle || 0,
                                    textAlign: objData.textAlign || 'center',
                                    scaleX: typeof objData.scaleX === 'number' ?
                                        objData.scaleX : 1,
                                    scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                    hasControls: true,
                                    hasBorders: true
                                });
                                applyCustomControls(text);
                                canvas.add(text);
                            } catch (err) {
                                console.warn('Error recreating text object:', err);
                            }
                        }
                    }

                    // ---- الصور ثانياً (بالتسلسل للحفاظ على الترتيب) ----
                    // FIX #4: تحميل الصور بالتسلسل بدل Promise.all لضمان الترتيب
                    for (const objData of savedView.objects) {
                        if (objData.type === 'image' && objData._customSrc) {
                            try {
                                let imageSrc = objData._customSrc;
                                let img = null;

                                if (imageSrc.startsWith('local://')) {
                                    const imageId = imageSrc.replace('local://', '');
                                    const base64Data = localStorage.getItem(imageId);
                                    if (base64Data) {
                                        img = await loadImagePromise(base64Data);
                                    } else {
                                        console.warn('Image not found in localStorage:', imageId);
                                    }
                                } else {
                                    if (!imageSrc.startsWith('/') && !imageSrc.startsWith('http') && !imageSrc
                                        .startsWith('data:')) {
                                        imageSrc = '/' + imageSrc;
                                    }
                                    if (uploadedImagesCache[imageSrc]) {
                                        img = fabric.util.object.clone(uploadedImagesCache[imageSrc]);
                                    } else {
                                        img = await loadImagePromise(imageSrc);
                                        if (img) uploadedImagesCache[imageSrc] = img;
                                    }
                                }

                                if (img) {
                                    img.set({
                                        left: objData.left || 100,
                                        top: objData.top || 100,
                                        angle: objData.angle || 0,
                                        // FIX #3: استرجاع الـ scaleX/scaleY الحقيقيين للصور أيضاً
                                        scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                        scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                        hasControls: true,
                                        hasBorders: true
                                    });
                                    // FIX #4: حفظ المرجع للمسار الأصلي
                                    img._customSrc = objData._customSrc;
                                    applyCustomControls(img);
                                    canvas.add(img);
                                }
                            } catch (err) {
                                console.warn('Error loading image:', objData._customSrc, err);
                            }
                        }
                    }
                } else {
                    console.log(`New design for view ${index}`);
                }

                canvas.renderAll();
            } catch (error) {
                console.error('Error changing image:', error);
            }
        }

        // -------------------------------------------------------
        // إضافة نص
        // -------------------------------------------------------
        function addText() {
            if (!canvas) return;

            try {
                const text = new fabric.Textbox('اكتب هنا', {
                    left: 150,
                    top: 150,
                    fontSize: 20,
                    fill: '#000000',
                    fontFamily: 'Cairo',
                    padding: 5,
                    width: 150,
                    hasControls: true,
                    hasBorders: true
                });

                applyCustomControls(text);
                canvas.add(text);
                canvas.setActiveObject(text);
                canvas.renderAll();
                saveCurrentView();
            } catch (error) {
                console.error('Error adding text:', error);
            }
        }

        // -------------------------------------------------------
        // أدوات التحكم (نص وصورة)
        // -------------------------------------------------------
        function setupControls() {
            const textColor = document.getElementById('textColor');
            const fontFamily = document.getElementById('fontFamily');
            const textSize = document.getElementById('textSize');
            const textRotate = document.getElementById('textRotate');
            const imageSize = document.getElementById('imageSize');
            const imageRotate = document.getElementById('imageRotate');

            if (textColor) {
                textColor.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fill', this.value);
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (fontFamily) {
                fontFamily.addEventListener('change', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fontFamily', this.value);
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (textSize) {
                textSize.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fontSize', parseInt(this.value));
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (textRotate) {
                textRotate.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('angle', parseInt(this.value));
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (imageSize) {
                imageSize.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type === 'image') {
                        obj.scale(parseInt(this.value) / 100);
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (imageRotate) {
                imageRotate.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type === 'image') {
                        obj.set('angle', parseInt(this.value));
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
        }

        // -------------------------------------------------------
        // رفع صورة
        // -------------------------------------------------------
        function setupImageUpload() {
            const uploadInput = document.getElementById('uploadImageInput');
            if (!uploadInput) return;

            uploadInput.addEventListener('change', function(e) {
                if (!canvas) return;
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = async function(event) {
                    try {
                        const base64Image = event.target.result;
                        const img = await loadImagePromise(base64Image);

                        if (img && canvas) {
                            const maxWidth = 200;
                            if (img.width > maxWidth) img.scale(maxWidth / img.width);

                            // FIX #4: حفظ المسار في خاصية مخصصة _customSrc
                            const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2,
                                9);
                            let customSrc = base64Image;

                            try {
                                localStorage.setItem(imageId, base64Image);
                                customSrc = 'local://' + imageId;
                                console.log('Image saved to localStorage:', imageId);
                            } catch (e) {
                                console.warn('localStorage full, storing base64 directly');
                            }

                            img.set({
                                left: 100,
                                top: 100,
                                hasControls: true,
                                hasBorders: true
                            });
                            // FIX #4: الخاصية المخصصة بدلاً من img.src الغير موثوقة في Fabric
                            img._customSrc = customSrc;

                            applyCustomControls(img);
                            canvas.add(img);
                            canvas.setActiveObject(img);
                            canvas.renderAll();
                            await saveCurrentView();
                        }
                    } catch (err) {
                        console.error('Error loading uploaded image:', err);
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        // -------------------------------------------------------
        // تحديث الـ Thumbnails
        // -------------------------------------------------------
        function updateThumbnails(images) {
            const container = document.querySelector('.wrap-slick3-dots ul li');
            if (!container) return;

            container.innerHTML = '';
            images.forEach((img, i) => {
                const imgSrc = fixImagePath(img);
                const el = document.createElement('img');
                el.src = imgSrc;
                el.style.cssText =
                    'width:60px;cursor:pointer;margin:5px;border:2px solid transparent;border-radius:5px;';
                el.onclick = (function(index, src) {
                    return function() {
                        changeImage(src, index);
                    };
                })(i, imgSrc);
                container.appendChild(el);
            });

            if (images.length > 0) {
                setTimeout(() => changeImage(fixImagePath(images[0]), 0), 100);
            }
        }

        // -------------------------------------------------------
        // تحميل تصميم موجود (تعديل من السلة)
        // -------------------------------------------------------
        async function loadExistingDesign() {
            if (!existingDesign || !existingDesign.designs || !canvas) return;

            console.log('Loading existing design:', existingDesign);

            try {
                canvasViews = {};

                existingDesign.designs.forEach(viewDesign => {
                    const viewIndex = viewDesign.view_index;
                    canvasViews[viewIndex] = {
                        objects: [],
                        version: '1.0'
                    };

                    viewDesign.elements.forEach(el => {
                        if (el.type === 'text') {
                            canvasViews[viewIndex].objects.push({
                                type: 'textbox',
                                text: el.content,
                                left: el.position_x,
                                top: el.position_y,
                                fill: el.color,
                                fontFamily: el.font_family,
                                angle: el.rotation,
                                fontSize: el.font_size || 20,
                                width: 150,
                                scaleX: 1,
                                scaleY: 1
                            });
                        } else if (el.type === 'image') {
                            const imagePath = fixImagePath(el.content);
                            const imageObj = {
                                type: 'image',
                                _customSrc: imagePath,
                                left: el.position_x,
                                top: el.position_y,
                                angle: el.rotation,
                                scaleX: el.scale_x || 1,
                                scaleY: el.scale_y || 1,
                                width: el.original_width || el.width || null,
                                height: el.original_height || el.height || null
                            };
                            canvasViews[viewIndex].objects.push(imageObj);
                        }
                    });
                });

                const initialView = canvasViews[0] !== undefined ? 0 : currentView;

                if (canvasViews[initialView] && productImages && productImages[initialView]) {
                    loadProductImage(fixImagePath(productImages[initialView]));

                    for (const objData of canvasViews[initialView].objects) {
                        try {
                            if (objData.type === 'textbox') {
                                const text = new fabric.Textbox(objData.text || 'اكتب هنا', {
                                    left: objData.left || 150,
                                    top: objData.top || 150,
                                    fontSize: objData.fontSize || 20,
                                    fill: objData.fill || '#000000',
                                    fontFamily: objData.fontFamily || 'Cairo',
                                    angle: objData.angle || 0,
                                    width: objData.width || 150,
                                    scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                    scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                    hasControls: true,
                                    hasBorders: true
                                });
                                applyCustomControls(text);
                                canvas.add(text);
                            } else if (objData.type === 'image' && objData._customSrc) {
                                const img = await loadImagePromise(objData._customSrc);
                                if (img) {
                                    img.set({
                                        left: objData.left || 100,
                                        top: objData.top || 100,
                                        angle: objData.angle || 0,
                                        scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                        scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                        hasControls: true,
                                        hasBorders: true
                                    });
                                    img._customSrc = objData._customSrc;
                                    applyCustomControls(img);
                                    canvas.add(img);
                                }
                            }
                        } catch (err) {
                            console.warn('Error loading object:', err);
                        }
                    }

                    canvas.renderAll();
                }
            } catch (error) {
                console.error('Error loading existing design:', error);
            }
        }

        // -------------------------------------------------------
        // FIX #3 + #4: حفظ الـ view الحالي بشكل صحيح كامل
        // -------------------------------------------------------
        async function saveCurrentView() {
            if (!canvas) return;

            try {
                const objects = canvas.getObjects();
                const currentObjects = objects.filter(obj => obj !== canvas.backgroundImage);
                const savedObjects = [];

                for (const obj of currentObjects) {
                    try {
                        if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                            savedObjects.push({
                                type: obj.type,
                                text: obj.text,
                                left: obj.left,
                                top: obj.top,
                                fontSize: obj.fontSize,
                                fill: obj.fill,
                                fontFamily: obj.fontFamily,
                                angle: obj.angle,
                                width: obj.width,
                                // FIX #3: حفظ القيم الحقيقية وليس || 1
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                hasControls: true,
                                hasBorders: true
                            });
                        } else if (obj.type === 'image') {
                            // FIX #4: استخدم _customSrc المخصصة بدل .src المدمجة
                            let customSrc = obj._customSrc;

                            // fallback: لو مفيش _customSrc جرب getSrc()
                            if (!customSrc) {
                                const fabricSrc = obj.getSrc ? obj.getSrc() : null;
                                if (fabricSrc && fabricSrc.startsWith('data:image')) {
                                    const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                                    try {
                                        localStorage.setItem(imageId, fabricSrc);
                                        customSrc = 'local://' + imageId;
                                    } catch (e) {
                                        customSrc = fabricSrc;
                                    }
                                } else {
                                    customSrc = fabricSrc;
                                }
                                obj._customSrc = customSrc;
                            }

                            savedObjects.push({
                                type: obj.type,
                                _customSrc: customSrc,
                                left: obj.left,
                                top: obj.top,
                                angle: obj.angle,
                                // FIX #3: حفظ القيم الحقيقية
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                width: obj.width,
                                height: obj.height,
                                hasControls: true,
                                hasBorders: true
                            });
                        }
                    } catch (err) {
                        console.warn('Error saving object:', err);
                    }
                }

                canvasViews[currentView] = {
                    objects: savedObjects,
                    version: '1.0',
                    timestamp: Date.now()
                };

                console.log(`View ${currentView} saved with ${savedObjects.length} objects`);
            } catch (error) {
                console.error('Error saving view:', error);
            }
        }

        // -------------------------------------------------------
        // Submit — إرسال التصميم للسيرفر
        // -------------------------------------------------------
        async function handleSubmit() {
            const variantId = document.getElementById('variant_id').value;
            if (!variantId) {
                alert('اختار المقاس واللون أولاً ❗');
                navigateTo('details');
                return;
            }

            if (!canvas) {
                alert('خطأ في تحميل التصميم ❗');
                return;
            }

            try {
                await saveCurrentView();

                const designsPayload = [];

                for (const viewIndex in canvasViews) {
                    const view = canvasViews[viewIndex];
                    if (!view || !view.objects || view.objects.length === 0) continue;

                    const elements = view.objects.map(obj => {
                        if (obj.type === 'image') {
                            return {
                                type: 'image',
                                content: obj._customSrc || null,
                                position_x: Math.round(obj.left || 0),
                                position_y: Math.round(obj.top || 0),
                                width: obj.width ? Math.round(obj.width * (obj.scaleX || 1)) : null,
                                height: obj.height ? Math.round(obj.height * (obj.scaleY || 1)) : null,
                                rotation: Math.round(obj.angle || 0),
                                scale_x: obj.scaleX || 1,
                                scale_y: obj.scaleY || 1,
                                original_width: obj.width || null,
                                original_height: obj.height || null,
                                z_index: obj.zIndex || 0
                            };
                        }
                        return {
                            type: 'text',
                            content: obj.text || null,
                            position_x: Math.round(obj.left || 0),
                            position_y: Math.round(obj.top || 0),
                            rotation: Math.round(obj.angle || 0),
                            color: obj.fill || null,
                            font_family: obj.fontFamily || null,
                            font_size: obj.fontSize || null,
                            z_index: obj.zIndex || 0
                        };
                    });

                    designsPayload.push({
                        view_index: parseInt(viewIndex),
                        elements
                    });
                }

                const previewImage = canvas.toDataURL({
                    format: 'png',
                    quality: 0.8
                });
                const existingDesignId = document.getElementById('design_id').value;

                const payload = {
                    product_id: {{ $product->id }},
                    variant_id: variantId,
                    view: currentView.toString(),
                    designs: designsPayload,
                    preview_image: previewImage
                };

                if (existingDesignId) payload.design_id = existingDesignId;

                const response = await fetch("{{ route('design.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    alert(data.error || 'حصل خطأ في حفظ التصميم');
                    return;
                }

                const designIdInput = document.getElementById('design_id');
                if (designIdInput) designIdInput.value = data.design_id;

                document.getElementById('addToCartForm').submit();
            } catch (err) {
                console.error('Submit error:', err);
                alert('حصل خطأ، حاول تاني');
            }
        }

        // ============================================================
        // FIX #1 + #2: إدارة المقاسات والألوان مع sessionStorage
        // ============================================================
        let selectedSize = null;
        let selectedColor = null;

        @php
            $variantsData = [];
            foreach ($product->variants as $variant) {
                if ($variant->quantity > 0) {
                    $size = $variant->size;
                    $color = $variant->color;
                    if (!isset($variantsData[$size])) {
                        $variantsData[$size] = [];
                    }
                    $variantsData[$size][$color] = [
                        'id' => $variant->id,
                        'quantity' => $variant->quantity,
                        'weight' => $variant->weight,
                        'material' => $variant->material,
                        'color_code' => $variant->color_code ?? null,
                    ];
                }
            }
        @endphp

        const variantsData = @json($variantsData);
        const colorImagesData = @json($colorImages);

        console.log('Variants Data:', variantsData);

        function getColorCodeFromName(colorName) {
            const colorMap = {
                'أحمر': '#ff0000',
                'احمر': '#ff0000',
                'red': '#ff0000',
                'أزرق': '#0000ff',
                'ازرق': '#0000ff',
                'blue': '#0000ff',
                'أخضر': '#00ff00',
                'اخضر': '#00ff00',
                'green': '#00ff00',
                'أصفر': '#ffff00',
                'اصفر': '#ffff00',
                'yellow': '#ffff00',
                'أسود': '#000000',
                'اسود': '#000000',
                'black': '#000000',
                'أبيض': '#ffffff',
                'ابيض': '#ffffff',
                'white': '#ffffff',
                'رمادي': '#808080',
                'gray': '#808080',
                'grey': '#808080',
                'بني': '#8b4513',
                'brown': '#8b4513',
                'بنفسجي': '#800080',
                'purple': '#800080',
                'برتقالي': '#ffa500',
                'orange': '#ffa500'
            };
            return colorMap[colorName.toLowerCase().trim()] || '#cccccc';
        }

        function displayColorsForSize(size) {
            const colorsContainer = document.getElementById('colorsContainer');
            if (!colorsContainer) return;

            if (!variantsData[size] || Object.keys(variantsData[size]).length === 0) {
                colorsContainer.innerHTML = '<p class="text-muted">لا توجد ألوان متاحة لهذا المقاس</p>';
                return;
            }

            const colors = Object.keys(variantsData[size]);
            let html = '';

            colors.forEach(color => {
                const colorData = variantsData[size][color];
                const colorCode = colorData.color_code || getColorCodeFromName(color);

                html += `
            <button type="button"
                    class="color-btn"
                    data-color="${color}"
                    data-variant-id="${colorData.id}"
                    data-quantity="${colorData.quantity}"
                    data-weight="${colorData.weight || '--'}"
                    data-material="${colorData.material || '--'}"
                    style="
                        width:40px; height:40px;
                        border-radius:50%;
                        background:${colorCode};
                        border:2px solid #ddd;
                        cursor:pointer;
                        transition:all 0.2s;
                        position:relative;
                        box-shadow:0 2px 4px rgba(0,0,0,0.1);
                    "
                    title="${color}">
                <span style="
                    position:absolute; bottom:-22px; left:50%;
                    transform:translateX(-50%);
                    font-size:10px; white-space:nowrap;
                    display:none;
                    background:rgba(0,0,0,0.7); color:white;
                    padding:2px 6px; border-radius:4px; z-index:100;
                " class="color-label">${color}</span>
            </button>
        `;
            });

            colorsContainer.innerHTML = html;

            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectColor(this);
                });
                btn.addEventListener('mouseenter', function() {
                    const label = this.querySelector('.color-label');
                    if (label) label.style.display = 'block';
                });
                btn.addEventListener('mouseleave', function() {
                    const label = this.querySelector('.color-label');
                    if (label) label.style.display = 'none';
                });
            });
        }

        // FIX #1: selectColor يحفظ في sessionStorage
        function selectColor(button) {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.style.border = '2px solid #ddd';
                btn.style.transform = 'scale(1)';
            });

            button.style.border = '3px solid #ff6e26';
            button.style.transform = 'scale(1.1)';

            selectedColor = button.dataset.color;

            const variantId = button.dataset.variantId;
            const quantity = button.dataset.quantity;
            const weight = button.dataset.weight;
            const material = button.dataset.material;

            // FIX #1: حفظ في sessionStorage
            sessionStorage.setItem('selectedColor', selectedColor);
            sessionStorage.setItem('selectedVariantId', variantId);

            document.getElementById('variant_id').value = variantId;

            const availableQtySpan = document.getElementById('availableQty');
            const weightSpan = document.getElementById('weight');
            const materialSpan = document.getElementById('material');

            if (availableQtySpan) availableQtySpan.textContent = quantity;
            if (weightSpan) weightSpan.textContent = weight;
            if (materialSpan) materialSpan.textContent = material;

            // تحديث صور المنتج للون المختار
            const colorKey = selectedColor.toLowerCase().trim();
            if (colorImagesData && colorImagesData[colorKey]) {
                updateThumbnails(colorImagesData[colorKey]);
            } else if (productImages && productImages.length > 0) {
                // لو مفيش صور للون دا استخدم الصور الأساسية
                updateThumbnails(productImages);
            }

            // FIX #2: إخفاء الـ overlay بعد اختيار المقاس واللون
            hideCanvasOverlay();

            console.log(`Selected: ${selectedSize} - ${selectedColor}`);
        }

        // FIX #1: selectSize يحفظ في sessionStorage
        function selectSize(button) {
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.style.background = 'white';
                btn.style.color = '#333';
                btn.style.border = '1px solid #ddd';
            });

            button.classList.add('active');
            button.style.background = '#ff6e26';
            button.style.color = 'white';
            button.style.border = '1px solid #ff6e26';

            selectedSize = button.dataset.size;
            selectedColor = null;

            // FIX #1: حفظ المقاس وإزالة اللون القديم
            sessionStorage.setItem('selectedSize', selectedSize);
            sessionStorage.removeItem('selectedColor');
            sessionStorage.removeItem('selectedVariantId');

            displayColorsForSize(selectedSize);
        }

        function initSizesAndColors() {
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectSize(this);
                });
            });
        }

        // -------------------------------------------------------
        // FIX #2: Overlay functions
        // -------------------------------------------------------
        function hideCanvasOverlay() {
            const overlay = document.getElementById('canvasOverlay');
            if (overlay) {
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 300);
            }
        }

        function showCanvasOverlay() {
            const overlay = document.getElementById('canvasOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
            }
        }

        // ============================================================
        // Navigation
        // ============================================================
        let navigationHistory = ['home'];

        function navigateTo(sectionId, addToHistory = true) {
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            const section = document.getElementById('sec-' + sectionId);
            if (section) section.classList.add('active');

            document.querySelectorAll('.nav-item-n').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById('btn-' + sectionId);
            if (activeBtn) activeBtn.classList.add('active');

            if (addToHistory && navigationHistory[navigationHistory.length - 1] !== sectionId) {
                navigationHistory.push(sectionId);
            }

            updateUI(sectionId);
        }

        function goBack() {
            if (navigationHistory.length > 1) {
                navigationHistory.pop();
                navigateTo(navigationHistory[navigationHistory.length - 1], false);
            }
        }

        function resetToHome() {
            navigationHistory = ['home'];
            navigateTo('home', false);
        }

        function updateUI(id) {
            const backBtn = document.getElementById('back-btn');
            const closeBtn = document.getElementById('closeDesignerBtn');

            if (backBtn) backBtn.style.visibility = (id === 'home') ? 'hidden' : 'visible';
            if (closeBtn) closeBtn.style.display = (id === 'home') ? 'none' : 'inline-block';

            const titles = {
                'home': 'تفاصيل المنتج والتصميم',
                'upload': 'رفع تصميم',
                'text': 'إضافة نص',
                'art': 'الرسومات',
                'details': 'تفاصيل المنتج'
            };

            const headerTitle = document.getElementById('header-title');
            if (headerTitle) headerTitle.innerText = titles[id] || 'المصمم';
        }

        // -------------------------------------------------------
        // تنظيف localStorage القديم
        // -------------------------------------------------------
        function cleanOldLocalStorage() {
            const oneHourAgo = Date.now() - 3600000;
            for (let i = localStorage.length - 1; i >= 0; i--) {
                const key = localStorage.key(i);
                if (key && key.startsWith('img_')) {
                    const parts = key.split('_');
                    const timestamp = parseInt(parts[1]);
                    if (timestamp && timestamp < oneHourAgo) {
                        localStorage.removeItem(key);
                    }
                }
            }
        }

        // ============================================================
        // Delete control helpers
        // ============================================================
        function deleteObject(eventData, transform) {
            const target = transform.target;
            const cnv = target.canvas;
            cnv.remove(target);
            cnv.requestRenderAll();
            saveCurrentView();
            return true;
        }

        function renderDeleteIcon(ctx, left, top, styleOverride, fabricObject) {
            const size = this.cornerSize;
            ctx.save();
            ctx.beginPath();
            ctx.arc(left, top, size / 2, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
            ctx.lineWidth = 1;
            ctx.strokeStyle = '#ccc';
            ctx.stroke();
            ctx.fillStyle = '#ff3b30';
            ctx.font = '18px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('✕', left, top + 1);
            ctx.restore();
        }

        // ============================================================
        // Initialize everything
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');

            if (!initCanvas()) {
                console.error('Failed to initialize canvas');
                return;
            }

            setupControls();
            setupImageUpload();
            initSizesAndColors();

            // تحميل صورة الخلفية الافتراضية
            if (productImages && productImages.length > 0 && productImages[0]) {
                loadProductImage(fixImagePath(productImages[0]));
            }

            // FIX #1: استرجاع المقاس واللون من sessionStorage
            const savedSize = sessionStorage.getItem('selectedSize');
            const savedColor = sessionStorage.getItem('selectedColor');
            const savedVariantId = sessionStorage.getItem('selectedVariantId');

            if (savedSize) {
                const sizeBtn = document.querySelector(`.size-btn[data-size="${savedSize}"]`);
                if (sizeBtn) {
                    selectSize(sizeBtn);

                    if (savedColor) {
                        // نستنى شوية للألوان تتحمل
                        setTimeout(() => {
                            const colorBtn = document.querySelector(
                                `.color-btn[data-color="${savedColor}"]`);
                            if (colorBtn) {
                                selectColor(colorBtn);
                                // تأكيد الـ variant_id
                                if (savedVariantId) {
                                    document.getElementById('variant_id').value = savedVariantId;
                                }
                            }
                        }, 150);
                    }
                }
            } else {
                // FIX #2: لو مفيش اختيار سابق، افتح صفحة التفاصيل تلقائياً
                navigateTo('details');
            }

            // تحميل تصميم موجود لو كان في تعديل
            setTimeout(() => {
                loadExistingDesign();
            }, 200);

            // تنظيف دوري للـ localStorage
            setInterval(cleanOldLocalStorage, 3600000);

            console.log('Initialization complete');
        });
</script>

@endsection









{{---- NEW EDITOR -----------------------------------}}



@extends('layouts.master')

@section('content')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
<link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">

<style>
    :root {
        --sidebar-n-bg: #1f1c1d;
        --main-bg: #f3f3f3;
        --border-color: #e1e1e1;
        --accent-blue: #ff6e26;
    }

    .designer-container {
        display: flex;
        min-height: calc(100vh - 90px);
    }

    /* sidebar-n */
    .sidebar-n {
        width: 90px;
        background-color: #222;
        display: flex;
        flex-direction: column;
        align-items: center;
        padding-top: 20px;
        z-index: 1000;
        flex-shrink: 0;
    }

    .new-tag {
        background: white;
        color: #ff5b1f;
        font-size: 11px;
        font-weight: 800;
        padding: 2px 10px;
        border-radius: 20px;
        margin-bottom: 15px;
    }

    .nav-item-n {
        width: 100%;
        background: none;
        border: none;
        color: #d8d8d8;
        padding: 18px 5px;
        display: flex;
        flex-direction: column;
        align-items: center;
        font-size: 13px;
        cursor: pointer;
        border-left: 4px solid transparent;
        transition: 0.2s;
    }

    .nav-item-n.active {
        background-color: white;
        color: #222;
        border-right: 4px solid var(--accent-blue);
    }

    .panel-v {
        width: 100%;
        max-width: 500px;
        background-color: white;
        border: 1px solid var(--border-color);
        border-right: none;
        display: flex;
        flex-direction: column;
        flex-shrink: 0;
    }

    .panel-v-header {
        height: 65px;
        border-bottom: 1px solid var(--border-color);
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0 15px;
        position: relative;
    }

    .panel-v-header #header-title {
        position: absolute;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        font-weight: bold;
        font-size: 16px;
    }

    .btn-header {
        background: none;
        border: none;
        font-size: 22px;
        cursor: pointer;
        color: #555;
        padding: 5px;
    }

    .panel-v-body {
        padding: 25px;
        overflow-y: auto;
        flex-grow: 1;
    }

    /* Grid Home */
    .feature-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }

    .icon-wrapper {
        border: 1px solid #6f7480;
        border-radius: 8px;
        height: 100px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 10px;
    }

    .icon-wrapper i {
        font-size: 45px;
        color: var(--accent-blue);
    }

    .bor10 {
        width: 100% !important;
        margin-top: 50px !important;
        clear: both !important;
    }

    .feature-card {
        cursor: pointer;
        text-align: center;
    }

    .feature-card:hover .icon-wrapper {
        border-color: var(--accent-blue);
        background: #f8f9ff;
    }

    /* Content Styling */
    .content-section {
        display: none;
    }

    .content-section.active {
        display: block;
    }

    .main-title {
        font-size: 26px;
        font-weight: 700;
        text-align: right;
        margin-bottom: 35px;
    }

    /* Upload Area */
    .upload-zone {
        border: 2px dashed #ddd;
        border-radius: 12px;
        padding: 40px 20px;
        text-align: center;
        margin-bottom: 25px;
    }

    .btn-upload-main {
        background: var(--accent-blue);
        color: white;
        font-weight: 700;
        padding: 10px 25px;
    }

    /* Tools Styling (Product & Text) */
    .color-swatch {
        width: 25px;
        height: 25px;
        border-radius: 50px;
        border: 1px solid #ddd;
        cursor: pointer;
    }

    .color-swatch.active {
        outline: 2px solid var(--accent-blue);
        outline-offset: 2px;
    }

    .size-btn {
        border: 1px solid #ddd;
        background: white;
        padding: 10px 15px;
        border-radius: 5px;
        font-weight: 600;
    }

    .tool-label {
        font-weight: 700;
        margin-bottom: 10px;
        display: block;
        font-size: 14px;
    }

    /* Preview Area */
    .preview-pane {
        flex-grow: 1;
        display: flex;
        justify-content: center;
        align-items: center;
        background: #eee;
    }

    .tshirt-mockup {
        max-width: 80%;
        mix-blend-mode: multiply;
    }

    /* =============================================
           FIX #2: Canvas Overlay — يجبر المستخدم يختار مقاس ولون أول
           ============================================= */
    #canvasOverlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.55);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        z-index: 999;
        border-radius: 4px;
        cursor: pointer;
        transition: opacity 0.3s;
    }

    #canvasOverlay .overlay-icon {
        font-size: 48px;
        color: #fff;
        margin-bottom: 12px;
    }

    #canvasOverlay .overlay-text {
        color: #fff;
        font-size: 16px;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        text-align: center;
        padding: 0 20px;
    }

    #canvasOverlay .overlay-btn {
        margin-top: 16px;
        background: var(--accent-blue);
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        font-family: 'Cairo', sans-serif;
        cursor: pointer;
    }

    @media (max-width: 991px) {
        .designer-container {
            flex-direction: column;
        }

        .sidebar-n {
            width: 100%;
            flex-direction: row;
            justify-content: space-around;
            padding: 10px;
        }

        .panel-v {
            max-width: 100%;
        }
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

                        <!-- الصور الصغيرة -->
                        <div class="wrap-slick3-dots">
                            <ul class="slick3-dots" role="tablist">
                                <li class="slick-active" role="presentation">
                                    @foreach ($baseImages as $index => $img)
                                    <img src="{{ asset($img) }}"
                                        style="width:70px;height:70px;object-fit:cover;cursor:pointer;margin-bottom:10px;"
                                        onclick="changeImage('{{ asset($img) }}', {{ $index }})">
                                    @endforeach
                                </li>
                            </ul>
                        </div>

                        <!-- الـ Canvas -->
                        <div class="slick3 gallery-lb flex-grow-1">
                            <div id="designArea" style="position:relative;width:100%;max-width:500px;">

                                <canvas id="fabricCanvas" width="500" height="500"
                                    style="width:100%;display:block;"></canvas>

                                <!-- FIX #2: Overlay يمنع التصميم قبل اختيار المقاس واللون -->
                                <div id="canvasOverlay">
                                    <i class="bi bi-palette overlay-icon"></i>
                                    <div class="overlay-text">اختر المقاس واللون أولاً لبدء التصميم</div>
                                    <button class="overlay-btn" onclick="navigateTo('details')">اختر المقاس
                                        واللون</button>
                                </div>

                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- التفاصيل -->
            <div class="col-md-6 col-lg-5 p-b-30">
                <div class="p-l-50 p-lr-0-lg text-right">

                    <div class="designer-container" dir="rtl">
                        <aside class="sidebar-n">
                            <div class="new-tag">New</div>
                            <button class="nav-item-n" onclick="navigateTo('upload')" id="btn-upload"><i
                                    class="bi bi-cloud-arrow-up"></i><span>رفع</span></button>
                            <button class="nav-item-n" onclick="navigateTo('text')" id="btn-text"><i
                                    class="bi bi-fonts"></i><span>نص</span></button>
                            <button class="nav-item-n" onclick="navigateTo('art')" id="btn-art"><i
                                    class="bi bi-palette"></i><span>رسومات</span></button>
                            <button class="nav-item-n" onclick="navigateTo('details')" id="btn-details"><i
                                    class="bi bi-info-circle"></i><span>التفاصيل</span></button>
                        </aside>

                        <main class="panel-v">
                            <div class="panel-v-header">
                                <button class="btn-header" id="back-btn" onclick="goBack()"
                                    style="visibility: hidden;">‹</button>
                                <span id="header-title" class="fw-bold">تفاصيل المنتج والتصميم</span>
                                <button class="btn-header" id="closeDesignerBtn" onclick="resetToHome()"
                                    style="display: none;">✕</button>
                            </div>

                            <div class="panel-v-body">

                                <section id="sec-home" class="content-section active">
                                    <h2 class="main-title">معلومات المنتج</h2>
                                    <h4 class="mtext-105 cl2 js-name-detail p-b-14 black">{{ $product->name }}</h4>
                                    <span class="mtext-106 black cl2">{{ $product->price }} ج.م</span>
                                    <p class="stext-102 cl3 p-t-23">الكميه المتاحة : <span id="availableQty">{{
                                            $product->quantity }}</span></p>
                                    <p class="stext-102 cl3 p-t-23">{{ $product->description }}</p>
                                    <div class="feature-grid">
                                        <div class="feature-card" onclick="navigateTo('upload')">
                                            <div class="icon-wrapper"><i class="bi bi-cloud-arrow-up"></i></div>
                                            <div class="feature-title">رفع تصميم</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('text')">
                                            <div class="icon-wrapper"><i class="bi bi-fonts"></i></div>
                                            <div class="feature-title">إضافة نص</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('art')">
                                            <div class="icon-wrapper"><i class="bi bi-palette"></i></div>
                                            <div class="feature-title">إضافة رسومات</div>
                                        </div>
                                        <div class="feature-card" onclick="navigateTo('details')">
                                            <div class="icon-wrapper"><i class="bi bi-tag"></i></div>
                                            <div class="feature-title">تغيير المنتج</div>
                                        </div>
                                    </div>
                                </section>

                                <section id="sec-upload" class="content-section">
                                    <h2 class="main-title">رفع صورة</h2>
                                    <div class="upload-zone">
                                        <label for="uploadImageInput" class="btn btn-upload-main mb-3">تصفح جهاز
                                            الكمبيوتر</label>
                                        <input type="file" id="uploadImageInput" accept="image/*" hidden>
                                        <p class="fw-bold">أو اسحب وأفلت في أي مكان</p>
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">حجم الصورة</label>
                                        <input type="range" id="imageSize" min="10" max="80" value="20"
                                            class="form-range">
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">تدوير الصورة</label>
                                        <input type="range" id="imageRotate" min="0" max="360" class="form-range">
                                    </div>
                                </section>

                                <section id="sec-text" class="content-section">
                                    <h2 class="main-title">إضافة نص</h2>
                                    <input type="text" onclick="addText()" class="form-control form-control-lg mb-4"
                                        placeholder="اكتب النص هنا">
                                    <div class="mb-3">
                                        <label class="tool-label">نوع الخط</label>
                                        <select class="form-select" id="fontFamily">
                                            <option value="Cairo">Cairo (افتراضي)</option>
                                            <option value="Arial">Arial</option>
                                            <option value="Tahoma">Tahoma</option>
                                            <option value="Verdana">Verdana</option>
                                            <option value="Courier New">Courier</option>
                                            <option value="Tajawal">Tajawal</option>
                                        </select>
                                    </div>
                                    <div class="mb-3">
                                        <label class="tool-label">لون الخط</label>
                                        <input type="color" id="textColor" class="form-control form-control-color w-100"
                                            value="#3047ff">
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">حجم الخط</label>
                                        <input type="range" id="textSize" min="10" max="80" value="20"
                                            class="form-range">
                                    </div>
                                    <div class="mb-4">
                                        <label class="tool-label">تدوير النص</label>
                                        <input type="range" id="textRotate" min="0" max="360" class="form-range">
                                    </div>
                                </section>

                                <section id="sec-art" class="content-section">
                                    <h2 class="main-title">تصنيفات الرسومات</h2>
                                    <input type="text" class="form-control mb-4" placeholder="ابحث عن رسومات...">
                                    <div class="row g-3">
                                        <div class="col-6">
                                            <div class="border p-3 rounded text-center fw-bold bg-light cursor-pointer">
                                                🔥 الأكثر شهرة</div>
                                        </div>
                                        <div class="col-6">
                                            <div class="border p-3 rounded text-center fw-bold bg-light cursor-pointer">
                                                🤩 إيموجي</div>
                                        </div>
                                    </div>
                                </section>

                                <section id="sec-details" class="content-section">
                                    <h2 class="main-title">تفاصيل المنتج</h2>

                                    <!-- المقاسات -->
                                    <div class="mb-4">
                                        <label class="tool-label">المقاسات</label>
                                        <div class="gap-2 flex-wrap" id="sizesContainer">
                                            @php
                                            $sizes = $product->variants
                                            ->where('quantity', '>', 0)
                                            ->pluck('size')
                                            ->unique();
                                            @endphp
                                            @foreach ($sizes as $size)
                                            <button type="button" class="size-btn" data-size="{{ $size }}">{{ $size
                                                }}</button>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- الألوان -->
                                    <div class="mb-4">
                                        <label class="tool-label">ألوان المنتج المتوفرة</label>
                                        <div class="d-flex gap-2 flex-wrap" id="colorsContainer">
                                            <p class="text-muted" id="noColorsMsg">اختر مقاس أولاً</p>
                                        </div>
                                    </div>

                                    <!-- الفورم -->
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST"
                                        id="addToCartForm">
                                        @csrf
                                        <input type="hidden" name="cart_item_id" value="{{ request('cart_item_id') }}">
                                        <input type="hidden" name="variant_id" id="variant_id">
                                        <input type="hidden" name="design_id" id="design_id">
                                        <button type="button" onclick="handleSubmit()" class="zoom-btn m-t-20"
                                            dir="ltr">
                                            <span class="icon">→</span>
                                            <span class="btn-text"> إضافة إلى السلة </span>
                                            <span class="hover-bg"></span>
                                        </button>
                                    </form>
                                </section>

                            </div>
                        </main>
                    </div>
                </div>
            </div>

            <!-- Tabs -->
            <div class="bor10 m-t-50 p-t-43 p-b-40">
                <div class="tab01">
                    <ul class="nav nav-tabs" role="tablist" dir="rtl">
                        <li class="nav-item p-b-10">
                            <a class="nav-link active" data-toggle="tab" href="#description" role="tab"
                                aria-expanded="true">وصف المنتج</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#information" role="tab"
                                aria-expanded="false">معلومات إضافية</a>
                        </li>
                        <li class="nav-item p-b-10">
                            <a class="nav-link" data-toggle="tab" href="#reviews" role="tab"
                                aria-expanded="false">التعليقات</a>
                        </li>
                    </ul>

                    <div class="tab-content p-t-43">
                        <div class="tab-pane fade active show" id="description" role="tabpanel" aria-expanded="true"
                            dir="rtl">
                            <div class="how-pos2 p-lr-15-md">
                                <p class="stext-102 cl6">{{ $product->description }}</p>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="information" role="tabpanel" aria-expanded="false" dir="rtl">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <ul class="p-lr-28 p-lr-15-sm">
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">وزن</span>
                                            <span id="weight">--</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">خامات</span>
                                            <span id="material">--</span>
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">الألوان المتاحة</span>
                                            {{ $product->variants->where('quantity', '>',
                                            0)->pluck('color')->unique()->implode('، ') }}
                                        </li>
                                        <li class="flex-w flex-t p-b-7">
                                            <span class="stext-102 cl3 size-205">المقاسات</span>
                                            @php
                                            $sizes = $product->variants
                                            ->where('quantity', '>', 0)
                                            ->pluck('size')
                                            ->unique();
                                            @endphp
                                            {{ $sizes->implode(' , ') }}
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>

                        <div class="tab-pane fade" id="reviews" role="tabpanel" aria-expanded="false">
                            <div class="row">
                                <div class="col-sm-10 col-md-8 col-lg-6 m-lr-auto">
                                    <div class="p-b-30 m-lr-15-sm">

                                        @forelse($product->reviews as $review)
                                        <div class="flex-w flex-t p-b-68" dir="rtl">
                                            <div class="wrap-pic-s size-109 bor0 of-hidden m-l-18 m-t-6">
                                                <x-user-avatar :user="$review->user" alt="AVATAR" />
                                            </div>
                                            <div class="size-207">
                                                <div class="flex-w flex-sb-m p-b-17">
                                                    <span class="mtext-107 cl2 black">{{ $review->name }}</span>
                                                    <span class="fs-18 cl11">
                                                        @php
                                                        $fullStars = floor($review->rating);
                                                        $halfStar = $review->rating - $fullStars >= 0.5;
                                                        @endphp
                                                        @for ($i = 1; $i <= 5; $i++) @if ($i <=$fullStars) <i
                                                            class="zmdi zmdi-star"></i>
                                                            @elseif($i == $fullStars + 1 && $halfStar)
                                                            <i class="zmdi zmdi-star-half"></i>
                                                            @else
                                                            <i class="zmdi zmdi-star-outline"></i>
                                                            @endif
                                                            @endfor
                                                    </span>
                                                </div>
                                                <p class="stext-102 cl6" dir="rtl">{{ $review->message }}
                                                </p>
                                                <small class="stext-102 cl8" style="font-size: 12px;">{{
                                                    $review->created_at->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                        @empty
                                        <div class="alert alert-info text-center" dir="rtl"
                                            style="background: #f8f9fa; border: 1px solid #d1ecf1; color: #0c5460; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
                                            <i class="zmdi zmdi-comment-outline" style="font-size: 24px;"></i>
                                            <p style="margin-top: 10px; margin-bottom: 0;">لا توجد تعليقات على هذا
                                                المنتج بعد. كن أول من يقيّم!</p>
                                        </div>
                                        @endforelse

                                        <form class="w-full" method="POST" action="{{ route('storeReview') }}"
                                            id="reviewForm">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <h5 class="mtext-108 black cl2 p-b-7" dir="rtl">إضافة مراجعة</h5>
                                            <p class="stext-102 cl6" dir="rtl">لن يتم نشر عنوان بريدك الإلكتروني.
                                                الحقول
                                                المطلوبة مُشار إليها بعلامة *</p>

                                            <div class="flex-w flex-m p-t-50 p-b-23" dir="rtl">
                                                <span class="stext-102 cl3 m-l-16">ما هو تقييمك؟</span>
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
                                                    <label class="stext-102 cl3" for="message">اكتب تقييمك <span
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
                                                        value="{{ old('name', auth()->check() ? auth()->user()->name : session('review_data.name')) }}" @auth readonly @endauth required>
                                                    @error('name')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                                <div class="col-sm-6 p-b-5">
                                                    <label class="stext-102 cl3" for="email">البريد الإلكتروني
                                                        <span class="text-danger">*</span></label>
                                                    <input class="size-111 bor8 stext-102 cl2 black p-lr-20" id="email"
                                                        type="email" name="email"
                                                        value="{{ old('email', auth()->check() ? auth()->user()->email : session('review_data.email')) }}"
                                                        @auth readonly @endauth required>
                                                    @error('email')
                                                    <small class="text-danger">{{ $message }}</small>
                                                    @enderror
                                                </div>
                                            </div>

                                            <button type="submit"
                                                class="flex-c-m stext-101 cl0 size-112 bg7 bor11 hov-btn3 p-lr-15 trans-04 m-b-10">إرسال</button>
                                        </form>

                                        @if (session('success'))
                                        <div class="alert alert-success text-center" dir="rtl"
                                            style="background: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 10px; margin-top: 20px;">
                                            <i class="zmdi zmdi-check-circle"></i> {{ session('success') }}
                                        </div>
                                        @endif

                                        @if ($errors->any())
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
    </div>
</section>

{{-- Fonts & Fabric.js --}}
<link href="https://fonts.googleapis.com/css2?family=Cairo&family=Tajawal&display=swap" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/fabric.js/5.3.1/fabric.min.js"></script>

<script>
    // ============================================================
        // Fix Fabric.js textBaseline error
        // ============================================================
        if (typeof fabric !== 'undefined') {
            if (fabric.Text) fabric.Text.prototype.textBaseline = 'alphabetic';
            if (fabric.IText) fabric.IText.prototype.textBaseline = 'alphabetic';
            if (fabric.Textbox) fabric.Textbox.prototype.textBaseline = 'alphabetic';
        }

        // ============================================================
        // البيانات من Laravel
        // ============================================================
        const variants = @json($product->variants);
        const productImages = @json($baseImages);
        const colorImages = @json($colorImages);
        const existingVariant = @json($existingVariantData ?? null);
        const existingDesign = @json($existingDesign ?? null);

        // ============================================================
        // Fabric.js Setup
        // ============================================================
        let canvas;
        let canvasViews = {};
        let currentView = 0;
        const imageCache = {};
        const uploadedImagesCache = {};

        // -------------------------------------------------------
        // Helper: تصحيح المسارات
        // -------------------------------------------------------
        function fixImagePath(path) {
            if (!path) return null;
            let cleanPath = path.replace(/^\/design\/edit\//, '');
            if (!cleanPath.startsWith('/') && !cleanPath.startsWith('http')) {
                cleanPath = '/' + cleanPath;
            }
            return cleanPath;
        }

        // -------------------------------------------------------
        // Helper: تحميل صورة Fabric بشكل Promise
        // -------------------------------------------------------
        function loadImagePromise(src, options = {}) {
            return new Promise((resolve, reject) => {
                if (!src) {
                    reject('No image source');
                    return;
                }
                const defaultOpts = {
                    crossOrigin: 'anonymous',
                    ...options
                };
                fabric.Image.fromURL(
                    src,
                    (img) => {
                        if (img) resolve(img);
                        else reject('Failed to load image: ' + src);
                    },
                    defaultOpts
                );
            });
        }

        // -------------------------------------------------------
        // Initialize canvas
        // -------------------------------------------------------
        function initCanvas() {
            try {
                const canvasElement = document.getElementById('fabricCanvas');
                if (!canvasElement) {
                    console.error('Canvas element not found');
                    return false;
                }

                canvas = new fabric.Canvas('fabricCanvas', {
                    selection: true,
                    preserveObjectStacking: true,
                    width: 500,
                    height: 500,
                    backgroundColor: 'transparent',
                    renderOnAddRemove: true
                });

                if (!canvas) {
                    console.error('Failed to create fabric canvas');
                    return false;
                }

                fabric.Object.prototype.transparentCorners = false;
                fabric.Object.prototype.cornerStyle = 'circle';
                fabric.Object.prototype.cornerColor = '#ffffff';
                fabric.Object.prototype.cornerStrokeColor = '#999';
                fabric.Object.prototype.borderColor = '#4A90E2';
                fabric.Object.prototype.cornerSize = 18;

                // زر الحذف العالمي
                fabric.Object.prototype.controls.deleteControl = new fabric.Control({
                    x: 0.5,
                    y: -0.5,
                    offsetY: -10,
                    offsetX: 10,
                    cursorStyle: 'pointer',
                    mouseUpHandler: deleteObject,
                    render: renderDeleteIcon,
                    cornerSize: 24
                });

                canvas.on('object:modified', () => saveCurrentView());
                canvas.on('object:added', () => saveCurrentView());
                canvas.on('object:removed', () => saveCurrentView());

                console.log('Canvas initialized successfully');
                return true;
            } catch (error) {
                console.error('Error initializing canvas:', error);
                return false;
            }
        }

        function applyCustomControls(obj) {
            obj.set({
                transparentCorners: false,
                cornerStyle: 'circle',
                cornerColor: '#ffffff',
                cornerStrokeColor: '#999',
                borderColor: '#4A90E2',
                cornerSize: 18,
                padding: 8
            });
            obj.controls.deleteControl = new fabric.Control({
                x: 0.5,
                y: -0.5,
                offsetY: 0,
                offsetX: 0,
                cursorStyle: 'pointer',
                mouseUpHandler: deleteObject,
                render: renderDeleteIcon,
                cornerSize: 24
            });
        }

        // -------------------------------------------------------
        // تحميل صورة المنتج كـ background
        // -------------------------------------------------------
        function loadProductImage(src) {
            if (!canvas || !src) return;
            const cleanSrc = fixImagePath(src);

            if (imageCache[cleanSrc]) {
                const cachedImg = imageCache[cleanSrc];
                canvas.setBackgroundImage(cachedImg, canvas.renderAll.bind(canvas), {
                    scaleX: canvas.width / cachedImg.width,
                    scaleY: canvas.height / cachedImg.height,
                    crossOrigin: 'anonymous'
                });
                return;
            }

            fabric.Image.fromURL(cleanSrc, function(img) {
                if (img && canvas) {
                    imageCache[cleanSrc] = img;
                    canvas.setBackgroundImage(img, canvas.renderAll.bind(canvas), {
                        scaleX: canvas.width / img.width,
                        scaleY: canvas.height / img.height,
                        crossOrigin: 'anonymous'
                    });
                }
            }, {
                crossOrigin: 'anonymous'
            });
        }

        // -------------------------------------------------------
        // تغيير الصورة مع حفظ واسترجاع كامل للتصميم
        // -------------------------------------------------------
        async function changeImage(src, index) {
            if (!canvas) return;

            try {
                await saveCurrentView();
                currentView = index;
                const savedView = canvasViews[index];

                canvas.clear();
                loadProductImage(src);

                if (savedView && savedView.objects && savedView.objects.length > 0) {
                    console.log(`Loading saved design for view ${index} with ${savedView.objects.length} objects`);

                    // ---- النصوص أولاً ----
                    for (const objData of savedView.objects) {
                        if (objData.type === 'i-text' || objData.type === 'text' || objData.type === 'textbox') {
                            try {
                                const text = new fabric.Text(objData.text || 'اكتب هنا', {
                                    left: objData.left || 150,
                                    top: objData.top || 150,
                                    fontSize: objData.fontSize || 20,
                                    fill: objData.fill || '#000000',
                                    fontFamily: objData.fontFamily || 'Cairo',
                                    angle: objData.angle || 0,
                                    textAlign: objData.textAlign || 'center',
                                    scaleX: typeof objData.scaleX === 'number' ?
                                        objData.scaleX : 1,
                                    scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                    hasControls: true,
                                    hasBorders: true
                                });
                                applyCustomControls(text);
                                canvas.add(text);
                            } catch (err) {
                                console.warn('Error recreating text object:', err);
                            }
                        }
                    }

                    // ---- الصور ثانياً (بالتسلسل للحفاظ على الترتيب) ----
                    // FIX #4: تحميل الصور بالتسلسل بدل Promise.all لضمان الترتيب
                    for (const objData of savedView.objects) {
                        if (objData.type === 'image' && objData._customSrc) {
                            try {
                                let imageSrc = objData._customSrc;
                                let img = null;

                                if (imageSrc.startsWith('local://')) {
                                    const imageId = imageSrc.replace('local://', '');
                                    const base64Data = localStorage.getItem(imageId);
                                    if (base64Data) {
                                        img = await loadImagePromise(base64Data);
                                    } else {
                                        console.warn('Image not found in localStorage:', imageId);
                                    }
                                } else {
                                    if (!imageSrc.startsWith('/') && !imageSrc.startsWith('http') && !imageSrc
                                        .startsWith('data:')) {
                                        imageSrc = '/' + imageSrc;
                                    }
                                    if (uploadedImagesCache[imageSrc]) {
                                        img = fabric.util.object.clone(uploadedImagesCache[imageSrc]);
                                    } else {
                                        img = await loadImagePromise(imageSrc);
                                        if (img) uploadedImagesCache[imageSrc] = img;
                                    }
                                }

                                if (img) {
                                    img.set({
                                        left: objData.left || 100,
                                        top: objData.top || 100,
                                        angle: objData.angle || 0,
                                        // FIX #3: استرجاع الـ scaleX/scaleY الحقيقيين للصور أيضاً
                                        scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                        scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                        hasControls: true,
                                        hasBorders: true
                                    });
                                    // FIX #4: حفظ المرجع للمسار الأصلي
                                    img._customSrc = objData._customSrc;
                                    applyCustomControls(img);
                                    canvas.add(img);
                                }
                            } catch (err) {
                                console.warn('Error loading image:', objData._customSrc, err);
                            }
                        }
                    }
                } else {
                    console.log(`New design for view ${index}`);
                }

                canvas.renderAll();
            } catch (error) {
                console.error('Error changing image:', error);
            }
        }

        // -------------------------------------------------------
        // إضافة نص
        // -------------------------------------------------------
        function addText() {
            if (!canvas) return;

            try {
                const text = new fabric.Textbox('اكتب هنا', {
                    left: 150,
                    top: 150,
                    fontSize: 20,
                    fill: '#000000',
                    fontFamily: 'Cairo',
                    padding: 5,
                    width: 150,
                    hasControls: true,
                    hasBorders: true
                });

                applyCustomControls(text);
                canvas.add(text);
                canvas.setActiveObject(text);
                canvas.renderAll();
                saveCurrentView();
            } catch (error) {
                console.error('Error adding text:', error);
            }
        }

        // -------------------------------------------------------
        // أدوات التحكم (نص وصورة)
        // -------------------------------------------------------
        function setupControls() {
            const textColor = document.getElementById('textColor');
            const fontFamily = document.getElementById('fontFamily');
            const textSize = document.getElementById('textSize');
            const textRotate = document.getElementById('textRotate');
            const imageSize = document.getElementById('imageSize');
            const imageRotate = document.getElementById('imageRotate');

            if (textColor) {
                textColor.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fill', this.value);
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (fontFamily) {
                fontFamily.addEventListener('change', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fontFamily', this.value);
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (textSize) {
                textSize.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('fontSize', parseInt(this.value));
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (textRotate) {
                textRotate.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type.includes('text')) {
                        obj.set('angle', parseInt(this.value));
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (imageSize) {
                imageSize.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type === 'image') {
                        obj.scale(parseInt(this.value) / 100);
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
            if (imageRotate) {
                imageRotate.addEventListener('input', function() {
                    const obj = canvas.getActiveObject();
                    if (obj && obj.type === 'image') {
                        obj.set('angle', parseInt(this.value));
                        canvas.renderAll();
                        saveCurrentView();
                    }
                });
            }
        }

        // -------------------------------------------------------
        // رفع صورة
        // -------------------------------------------------------
        function setupImageUpload() {
            const uploadInput = document.getElementById('uploadImageInput');
            if (!uploadInput) return;

            uploadInput.addEventListener('change', function(e) {
                if (!canvas) return;
                const file = e.target.files[0];
                if (!file) return;

                const reader = new FileReader();
                reader.onload = async function(event) {
                    try {
                        const base64Image = event.target.result;
                        const img = await loadImagePromise(base64Image);

                        if (img && canvas) {
                            const maxWidth = 200;
                            if (img.width > maxWidth) img.scale(maxWidth / img.width);

                            // FIX #4: حفظ المسار في خاصية مخصصة _customSrc
                            const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2,
                                9);
                            let customSrc = base64Image;

                            try {
                                localStorage.setItem(imageId, base64Image);
                                customSrc = 'local://' + imageId;
                                console.log('Image saved to localStorage:', imageId);
                            } catch (e) {
                                console.warn('localStorage full, storing base64 directly');
                            }

                            img.set({
                                left: 100,
                                top: 100,
                                hasControls: true,
                                hasBorders: true
                            });
                            // FIX #4: الخاصية المخصصة بدلاً من img.src الغير موثوقة في Fabric
                            img._customSrc = customSrc;

                            applyCustomControls(img);
                            canvas.add(img);
                            canvas.setActiveObject(img);
                            canvas.renderAll();
                            await saveCurrentView();
                        }
                    } catch (err) {
                        console.error('Error loading uploaded image:', err);
                    }
                };
                reader.readAsDataURL(file);
            });
        }

        // -------------------------------------------------------
        // تحديث الـ Thumbnails
        // -------------------------------------------------------
        function updateThumbnails(images) {
            const container = document.querySelector('.wrap-slick3-dots ul li');
            if (!container) return;

            container.innerHTML = '';
            images.forEach((img, i) => {
                const imgSrc = fixImagePath(img);
                const el = document.createElement('img');
                el.src = imgSrc;
                el.style.cssText =
                    'width:60px;cursor:pointer;margin:5px;border:2px solid transparent;border-radius:5px;';
                el.onclick = (function(index, src) {
                    return function() {
                        changeImage(src, index);
                    };
                })(i, imgSrc);
                container.appendChild(el);
            });

            if (images.length > 0) {
                setTimeout(() => changeImage(fixImagePath(images[0]), 0), 100);
            }
        }

        // -------------------------------------------------------
        // تحميل تصميم موجود (تعديل من السلة)
        // -------------------------------------------------------
        async function loadExistingDesign() {
            if (!existingDesign || !existingDesign.designs || !canvas) return;

            console.log('Loading existing design:', existingDesign);

            try {
                canvasViews = {};

                existingDesign.designs.forEach(viewDesign => {
                    const viewIndex = viewDesign.view_index;
                    canvasViews[viewIndex] = {
                        objects: [],
                        version: '1.0'
                    };

                    viewDesign.elements.forEach(el => {
                        if (el.type === 'text') {
                            canvasViews[viewIndex].objects.push({
                                type: 'textbox',
                                text: el.content,
                                left: el.position_x,
                                top: el.position_y,
                                fill: el.color,
                                fontFamily: el.font_family,
                                angle: el.rotation,
                                fontSize: el.font_size || 20,
                                width: 150,
                                scaleX: 1,
                                scaleY: 1
                            });
                        } else if (el.type === 'image') {
                            const imagePath = fixImagePath(el.content);
                            const imageObj = {
                                type: 'image',
                                _customSrc: imagePath,
                                left: el.position_x,
                                top: el.position_y,
                                angle: el.rotation,
                                scaleX: el.scale_x || 1,
                                scaleY: el.scale_y || 1,
                                width: el.original_width || el.width || null,
                                height: el.original_height || el.height || null
                            };
                            canvasViews[viewIndex].objects.push(imageObj);
                        }
                    });
                });

                const initialView = canvasViews[0] !== undefined ? 0 : currentView;

                if (canvasViews[initialView] && productImages && productImages[initialView]) {
                    loadProductImage(fixImagePath(productImages[initialView]));

                    for (const objData of canvasViews[initialView].objects) {
                        try {
                            if (objData.type === 'textbox') {
                                const text = new fabric.Textbox(objData.text || 'اكتب هنا', {
                                    left: objData.left || 150,
                                    top: objData.top || 150,
                                    fontSize: objData.fontSize || 20,
                                    fill: objData.fill || '#000000',
                                    fontFamily: objData.fontFamily || 'Cairo',
                                    angle: objData.angle || 0,
                                    width: objData.width || 150,
                                    scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                    scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                    hasControls: true,
                                    hasBorders: true
                                });
                                applyCustomControls(text);
                                canvas.add(text);
                            } else if (objData.type === 'image' && objData._customSrc) {
                                const img = await loadImagePromise(objData._customSrc);
                                if (img) {
                                    img.set({
                                        left: objData.left || 100,
                                        top: objData.top || 100,
                                        angle: objData.angle || 0,
                                        scaleX: typeof objData.scaleX === 'number' ? objData.scaleX : 1,
                                        scaleY: typeof objData.scaleY === 'number' ? objData.scaleY : 1,
                                        hasControls: true,
                                        hasBorders: true
                                    });
                                    img._customSrc = objData._customSrc;
                                    applyCustomControls(img);
                                    canvas.add(img);
                                }
                            }
                        } catch (err) {
                            console.warn('Error loading object:', err);
                        }
                    }

                    canvas.renderAll();
                }
            } catch (error) {
                console.error('Error loading existing design:', error);
            }
        }

        // -------------------------------------------------------
        // FIX #3 + #4: حفظ الـ view الحالي بشكل صحيح كامل
        // -------------------------------------------------------
        async function saveCurrentView() {
            if (!canvas) return;

            try {
                const objects = canvas.getObjects();
                const currentObjects = objects.filter(obj => obj !== canvas.backgroundImage);
                const savedObjects = [];

                for (const obj of currentObjects) {
                    try {
                        if (obj.type === 'i-text' || obj.type === 'text' || obj.type === 'textbox') {
                            savedObjects.push({
                                type: obj.type,
                                text: obj.text,
                                left: obj.left,
                                top: obj.top,
                                fontSize: obj.fontSize,
                                fill: obj.fill,
                                fontFamily: obj.fontFamily,
                                angle: obj.angle,
                                width: obj.width,
                                // FIX #3: حفظ القيم الحقيقية وليس || 1
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                hasControls: true,
                                hasBorders: true
                            });
                        } else if (obj.type === 'image') {
                            // FIX #4: استخدم _customSrc المخصصة بدل .src المدمجة
                            let customSrc = obj._customSrc;

                            // fallback: لو مفيش _customSrc جرب getSrc()
                            if (!customSrc) {
                                const fabricSrc = obj.getSrc ? obj.getSrc() : null;
                                if (fabricSrc && fabricSrc.startsWith('data:image')) {
                                    const imageId = 'img_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
                                    try {
                                        localStorage.setItem(imageId, fabricSrc);
                                        customSrc = 'local://' + imageId;
                                    } catch (e) {
                                        customSrc = fabricSrc;
                                    }
                                } else {
                                    customSrc = fabricSrc;
                                }
                                obj._customSrc = customSrc;
                            }

                            savedObjects.push({
                                type: obj.type,
                                _customSrc: customSrc,
                                left: obj.left,
                                top: obj.top,
                                angle: obj.angle,
                                // FIX #3: حفظ القيم الحقيقية
                                scaleX: obj.scaleX,
                                scaleY: obj.scaleY,
                                width: obj.width,
                                height: obj.height,
                                hasControls: true,
                                hasBorders: true
                            });
                        }
                    } catch (err) {
                        console.warn('Error saving object:', err);
                    }
                }

                canvasViews[currentView] = {
                    objects: savedObjects,
                    version: '1.0',
                    timestamp: Date.now()
                };

                console.log(`View ${currentView} saved with ${savedObjects.length} objects`);
            } catch (error) {
                console.error('Error saving view:', error);
            }
        }

        // -------------------------------------------------------
        // Submit — إرسال التصميم للسيرفر
        // -------------------------------------------------------
        async function handleSubmit() {
            const variantId = document.getElementById('variant_id').value;
            if (!variantId) {
                alert('اختار المقاس واللون أولاً ❗');
                navigateTo('details');
                return;
            }

            if (!canvas) {
                alert('خطأ في تحميل التصميم ❗');
                return;
            }

            try {
                await saveCurrentView();

                const designsPayload = [];

                for (const viewIndex in canvasViews) {
                    const view = canvasViews[viewIndex];
                    if (!view || !view.objects || view.objects.length === 0) continue;

                    const elements = view.objects.map(obj => {
                        if (obj.type === 'image') {
                            return {
                                type: 'image',
                                content: obj._customSrc || null,
                                position_x: Math.round(obj.left || 0),
                                position_y: Math.round(obj.top || 0),
                                width: obj.width ? Math.round(obj.width * (obj.scaleX || 1)) : null,
                                height: obj.height ? Math.round(obj.height * (obj.scaleY || 1)) : null,
                                rotation: Math.round(obj.angle || 0),
                                scale_x: obj.scaleX || 1,
                                scale_y: obj.scaleY || 1,
                                original_width: obj.width || null,
                                original_height: obj.height || null,
                                z_index: obj.zIndex || 0
                            };
                        }
                        return {
                            type: 'text',
                            content: obj.text || null,
                            position_x: Math.round(obj.left || 0),
                            position_y: Math.round(obj.top || 0),
                            rotation: Math.round(obj.angle || 0),
                            color: obj.fill || null,
                            font_family: obj.fontFamily || null,
                            font_size: obj.fontSize || null,
                            z_index: obj.zIndex || 0
                        };
                    });

                    designsPayload.push({
                        view_index: parseInt(viewIndex),
                        elements
                    });
                }

                const previewImage = canvas.toDataURL({
                    format: 'png',
                    quality: 0.8
                });
                const existingDesignId = document.getElementById('design_id').value;

                const payload = {
                    product_id: {{ $product->id }},
                    variant_id: variantId,
                    view: currentView.toString(),
                    designs: designsPayload,
                    preview_image: previewImage
                };

                if (existingDesignId) payload.design_id = existingDesignId;

                const response = await fetch("{{ route('design.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify(payload)
                });

                const data = await response.json();

                if (!response.ok) {
                    alert(data.error || 'حصل خطأ في حفظ التصميم');
                    return;
                }

                const designIdInput = document.getElementById('design_id');
                if (designIdInput) designIdInput.value = data.design_id;

                document.getElementById('addToCartForm').submit();
            } catch (err) {
                console.error('Submit error:', err);
                alert('حصل خطأ، حاول تاني');
            }
        }

        // ============================================================
        // FIX #1 + #2: إدارة المقاسات والألوان مع sessionStorage
        // ============================================================
        let selectedSize = null;
        let selectedColor = null;

        @php
            $variantsData = [];
            foreach ($product->variants as $variant) {
                if ($variant->quantity > 0) {
                    $size = $variant->size;
                    $color = $variant->color;
                    if (!isset($variantsData[$size])) {
                        $variantsData[$size] = [];
                    }
                    $variantsData[$size][$color] = [
                        'id' => $variant->id,
                        'quantity' => $variant->quantity,
                        'weight' => $variant->weight,
                        'material' => $variant->material,
                        'color_code' => $variant->color_code ?? null,
                    ];
                }
            }
        @endphp

        const variantsData = @json($variantsData);
        const colorImagesData = @json($colorImages);

        console.log('Variants Data:', variantsData);

        function getColorCodeFromName(colorName) {
            const colorMap = {
                'أحمر': '#ff0000',
                'احمر': '#ff0000',
                'red': '#ff0000',
                'أزرق': '#0000ff',
                'ازرق': '#0000ff',
                'blue': '#0000ff',
                'أخضر': '#00ff00',
                'اخضر': '#00ff00',
                'green': '#00ff00',
                'أصفر': '#ffff00',
                'اصفر': '#ffff00',
                'yellow': '#ffff00',
                'أسود': '#000000',
                'اسود': '#000000',
                'black': '#000000',
                'أبيض': '#ffffff',
                'ابيض': '#ffffff',
                'white': '#ffffff',
                'رمادي': '#808080',
                'gray': '#808080',
                'grey': '#808080',
                'بني': '#8b4513',
                'brown': '#8b4513',
                'بنفسجي': '#800080',
                'purple': '#800080',
                'برتقالي': '#ffa500',
                'orange': '#ffa500'
            };
            return colorMap[colorName.toLowerCase().trim()] || '#cccccc';
        }

        function displayColorsForSize(size) {
            const colorsContainer = document.getElementById('colorsContainer');
            if (!colorsContainer) return;

            if (!variantsData[size] || Object.keys(variantsData[size]).length === 0) {
                colorsContainer.innerHTML = '<p class="text-muted">لا توجد ألوان متاحة لهذا المقاس</p>';
                return;
            }

            const colors = Object.keys(variantsData[size]);
            let html = '';

            colors.forEach(color => {
                const colorData = variantsData[size][color];
                const colorCode = colorData.color_code || getColorCodeFromName(color);

                html += `
            <button type="button"
                    class="color-btn"
                    data-color="${color}"
                    data-variant-id="${colorData.id}"
                    data-quantity="${colorData.quantity}"
                    data-weight="${colorData.weight || '--'}"
                    data-material="${colorData.material || '--'}"
                    style="
                        width:40px; height:40px;
                        border-radius:50%;
                        background:${colorCode};
                        border:2px solid #ddd;
                        cursor:pointer;
                        transition:all 0.2s;
                        position:relative;
                        box-shadow:0 2px 4px rgba(0,0,0,0.1);
                    "
                    title="${color}">
                <span style="
                    position:absolute; bottom:-22px; left:50%;
                    transform:translateX(-50%);
                    font-size:10px; white-space:nowrap;
                    display:none;
                    background:rgba(0,0,0,0.7); color:white;
                    padding:2px 6px; border-radius:4px; z-index:100;
                " class="color-label">${color}</span>
            </button>
        `;
            });

            colorsContainer.innerHTML = html;

            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectColor(this);
                });
                btn.addEventListener('mouseenter', function() {
                    const label = this.querySelector('.color-label');
                    if (label) label.style.display = 'block';
                });
                btn.addEventListener('mouseleave', function() {
                    const label = this.querySelector('.color-label');
                    if (label) label.style.display = 'none';
                });
            });
        }

        // FIX #1: selectColor يحفظ في sessionStorage
        function selectColor(button) {
            document.querySelectorAll('.color-btn').forEach(btn => {
                btn.style.border = '2px solid #ddd';
                btn.style.transform = 'scale(1)';
            });

            button.style.border = '3px solid #ff6e26';
            button.style.transform = 'scale(1.1)';

            selectedColor = button.dataset.color;

            const variantId = button.dataset.variantId;
            const quantity = button.dataset.quantity;
            const weight = button.dataset.weight;
            const material = button.dataset.material;

            // FIX #1: حفظ في sessionStorage
            sessionStorage.setItem('selectedColor', selectedColor);
            sessionStorage.setItem('selectedVariantId', variantId);

            document.getElementById('variant_id').value = variantId;

            const availableQtySpan = document.getElementById('availableQty');
            const weightSpan = document.getElementById('weight');
            const materialSpan = document.getElementById('material');

            if (availableQtySpan) availableQtySpan.textContent = quantity;
            if (weightSpan) weightSpan.textContent = weight;
            if (materialSpan) materialSpan.textContent = material;

            // تحديث صور المنتج للون المختار
            const colorKey = selectedColor.toLowerCase().trim();
            if (colorImagesData && colorImagesData[colorKey]) {
                updateThumbnails(colorImagesData[colorKey]);
            } else if (productImages && productImages.length > 0) {
                // لو مفيش صور للون دا استخدم الصور الأساسية
                updateThumbnails(productImages);
            }

            // FIX #2: إخفاء الـ overlay بعد اختيار المقاس واللون
            hideCanvasOverlay();

            console.log(`Selected: ${selectedSize} - ${selectedColor}`);
        }

        // FIX #1: selectSize يحفظ في sessionStorage
        function selectSize(button) {
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.classList.remove('active');
                btn.style.background = 'white';
                btn.style.color = '#333';
                btn.style.border = '1px solid #ddd';
            });

            button.classList.add('active');
            button.style.background = '#ff6e26';
            button.style.color = 'white';
            button.style.border = '1px solid #ff6e26';

            selectedSize = button.dataset.size;
            selectedColor = null;

            // FIX #1: حفظ المقاس وإزالة اللون القديم
            sessionStorage.setItem('selectedSize', selectedSize);
            sessionStorage.removeItem('selectedColor');
            sessionStorage.removeItem('selectedVariantId');

            displayColorsForSize(selectedSize);
        }

        function initSizesAndColors() {
            document.querySelectorAll('.size-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    selectSize(this);
                });
            });
        }

        // -------------------------------------------------------
        // FIX #2: Overlay functions
        // -------------------------------------------------------
        function hideCanvasOverlay() {
            const overlay = document.getElementById('canvasOverlay');
            if (overlay) {
                overlay.style.opacity = '0';
                overlay.style.pointerEvents = 'none';
                setTimeout(() => {
                    overlay.style.display = 'none';
                }, 300);
            }
        }

        function showCanvasOverlay() {
            const overlay = document.getElementById('canvasOverlay');
            if (overlay) {
                overlay.style.display = 'flex';
                overlay.style.opacity = '1';
                overlay.style.pointerEvents = 'auto';
            }
        }

        // ============================================================
        // Navigation
        // ============================================================
        let navigationHistory = ['home'];

        function navigateTo(sectionId, addToHistory = true) {
            document.querySelectorAll('.content-section').forEach(s => s.classList.remove('active'));
            const section = document.getElementById('sec-' + sectionId);
            if (section) section.classList.add('active');

            document.querySelectorAll('.nav-item-n').forEach(btn => btn.classList.remove('active'));
            const activeBtn = document.getElementById('btn-' + sectionId);
            if (activeBtn) activeBtn.classList.add('active');

            if (addToHistory && navigationHistory[navigationHistory.length - 1] !== sectionId) {
                navigationHistory.push(sectionId);
            }

            updateUI(sectionId);
        }

        function goBack() {
            if (navigationHistory.length > 1) {
                navigationHistory.pop();
                navigateTo(navigationHistory[navigationHistory.length - 1], false);
            }
        }

        function resetToHome() {
            navigationHistory = ['home'];
            navigateTo('home', false);
        }

        function updateUI(id) {
            const backBtn = document.getElementById('back-btn');
            const closeBtn = document.getElementById('closeDesignerBtn');

            if (backBtn) backBtn.style.visibility = (id === 'home') ? 'hidden' : 'visible';
            if (closeBtn) closeBtn.style.display = (id === 'home') ? 'none' : 'inline-block';

            const titles = {
                'home': 'تفاصيل المنتج والتصميم',
                'upload': 'رفع تصميم',
                'text': 'إضافة نص',
                'art': 'الرسومات',
                'details': 'تفاصيل المنتج'
            };

            const headerTitle = document.getElementById('header-title');
            if (headerTitle) headerTitle.innerText = titles[id] || 'المصمم';
        }

        // -------------------------------------------------------
        // تنظيف localStorage القديم
        // -------------------------------------------------------
        function cleanOldLocalStorage() {
            const oneHourAgo = Date.now() - 3600000;
            for (let i = localStorage.length - 1; i >= 0; i--) {
                const key = localStorage.key(i);
                if (key && key.startsWith('img_')) {
                    const parts = key.split('_');
                    const timestamp = parseInt(parts[1]);
                    if (timestamp && timestamp < oneHourAgo) {
                        localStorage.removeItem(key);
                    }
                }
            }
        }

        // ============================================================
        // Delete control helpers
        // ============================================================
        function deleteObject(eventData, transform) {
            const target = transform.target;
            const cnv = target.canvas;
            cnv.remove(target);
            cnv.requestRenderAll();
            saveCurrentView();
            return true;
        }

        function renderDeleteIcon(ctx, left, top, styleOverride, fabricObject) {
            const size = this.cornerSize;
            ctx.save();
            ctx.beginPath();
            ctx.arc(left, top, size / 2, 0, Math.PI * 2);
            ctx.fillStyle = '#ffffff';
            ctx.fill();
            ctx.lineWidth = 1;
            ctx.strokeStyle = '#ccc';
            ctx.stroke();
            ctx.fillStyle = '#ff3b30';
            ctx.font = '18px Arial';
            ctx.textAlign = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText('✕', left, top + 1);
            ctx.restore();
        }

        // ============================================================
        // Initialize everything
        // ============================================================
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, initializing...');

            if (!initCanvas()) {
                console.error('Failed to initialize canvas');
                return;
            }

            setupControls();
            setupImageUpload();
            initSizesAndColors();

            // تحميل صورة الخلفية الافتراضية
            if (productImages && productImages.length > 0 && productImages[0]) {
                loadProductImage(fixImagePath(productImages[0]));
            }

            // FIX #1: استرجاع المقاس واللون من sessionStorage
            const savedSize = sessionStorage.getItem('selectedSize');
            const savedColor = sessionStorage.getItem('selectedColor');
            const savedVariantId = sessionStorage.getItem('selectedVariantId');

            if (savedSize) {
                const sizeBtn = document.querySelector(`.size-btn[data-size="${savedSize}"]`);
                if (sizeBtn) {
                    selectSize(sizeBtn);

                    if (savedColor) {
                        // نستنى شوية للألوان تتحمل
                        setTimeout(() => {
                            const colorBtn = document.querySelector(
                                `.color-btn[data-color="${savedColor}"]`);
                            if (colorBtn) {
                                selectColor(colorBtn);
                                // تأكيد الـ variant_id
                                if (savedVariantId) {
                                    document.getElementById('variant_id').value = savedVariantId;
                                }
                            }
                        }, 150);
                    }
                }
            } else {
                // FIX #2: لو مفيش اختيار سابق، افتح صفحة التفاصيل تلقائياً
                navigateTo('details');
            }

            // تحميل تصميم موجود لو كان في تعديل
            setTimeout(() => {
                loadExistingDesign();
            }, 200);

            // تنظيف دوري للـ localStorage
            setInterval(cleanOldLocalStorage, 3600000);

            console.log('Initialization complete');
        });
</script>

@endsection