@extends('admin.layout')

@section('content')
<div class="container-fluid py-2">
    <div class="row min-vh-80">
        <div class="col-lg-8 col-md-10 col-12 m-auto">
            <h3 class="mt-0 mb-5 text-center"> إضافة منتج جديد </h3>
            <p class="lead font-weight-normal opacity-8 mb-7 text-center"> يمكنك إضافة منتج جديد للموقع </p>
            <div class="card">
                <div class="card-header p-0 position-relative mt-n5 mx-3 z-index-2">
                    <div class="stepper mb-4">
                        <div class="step active">1. بيانات المنتج</div>
                        <div class="step">2. وصف المنتج</div>
                        <div class="step">3. صور المنتج</div>
                    </div>
                </div>
                <div class="card-body">
                    <form class="multisteps-form__form" method="POST" enctype="multipart/form-data"
                        action="{{ route('admin.products.store') }}" style="text-align:right" dir="rtl">
                        @csrf

                        <!--الخطوة 1: بيانات المنتج الأساسية-->
                        <div id="step1" class="step-content">
                            <h5 class="mb-4"> بيانات المنتج الأساسية </h5>

                            {{-- إسم المنتج --}}
                            <div class="form-group mb-3">
                                <label for="name">إسم المنتج</label>
                                <input type="text" class="form-control form-control-custom" name="name" id="name"
                                    value="{{ old('name') }}" placeholder="الإسم">
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- السعر --}}
                            <div class="form-group mb-3">
                                <label for="price">سعر المنتج</label>
                                <input type="number" class="form-control form-control-custom" name="price" id="price"
                                    value="{{ old('price') }}" placeholder="السعر">
                                @error('price')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- الكمية --}}
                            <div class="form-group mb-3">
                                <label for="quantity">الكميه المتاحه للمنتج</label>
                                <input type="number" class="form-control form-control-custom" name="quantity"
                                    id="quantity" value="{{ old('quantity') }}" placeholder="الكميه">
                                @error('quantity')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- قسم المنتجات --}}
                            <div class="form-group mb-3">
                                <label for="category_id">قسم المنتج</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    @foreach ($allcategories as $item)
                                    <option value="{{ $item->id }}">{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="text-end">
                                <button type="button" class="btn btn-dark" onclick="nextStep()">الخطوة التالية</button>
                            </div>
                        </div>

                        <!--الخطوة 2: وصف المنتج والمقاسات والألوان-->
                        <div id="step2" class="step-content d-none">
                            <h5 class="mb-4"> وصف المنتج والمتغيرات </h5>

                            {{-- الوصف --}}
                            <div class="form-group mb-4">
                                <label for="description">وصف المنتج</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="وصف المنتج">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            {{-- المقاسات والألوان --}}
                            <h5 class="mt-4 mb-3">المقاسات والألوان</h5>
                            <div id="variants">
                                <div class="variant-item mb-3 p-3 border rounded">
                                    <input type="text" name="variants[0][size]" placeholder="المقاس (M, L)"
                                        class="form-control form-control-custom mb-2">
                                    <input type="text" name="variants[0][color]" placeholder="اللون (أسود)"
                                        class="form-control form-control-custom mb-2">
                                    <input type="number" name="variants[0][quantity]" placeholder="الكمية"
                                        class="form-control form-control-custom mb-2">
                                    <input type="text" name="variants[0][material]" placeholder="الخامة"
                                        class="form-control form-control-custom mb-2">
                                    <input type="number" step="0.1" name="variants[0][weight]" placeholder="الوزن"
                                        class="form-control form-control-custom mb-2">
                                </div>
                            </div>

                            <button type="button" class="btn btn-dark mb-3" onclick="addVariant()">
                                + إضافة مقاس جديد
                            </button>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-light" onclick="prevStep()">الخطوة السابقة</button>
                                <button type="button" class="btn btn-dark" onclick="nextStep()">الخطوة التالية</button>
                            </div>
                        </div>

                        <!--الخطوة 3: صور المنتج-->
                        <div id="step3" class="step-content d-none">
                            <h5 class="mb-4"> صور المنتج </h5>

                            <div class="border rounded p-5 text-center mt-3 position-relative">
                                <input type="file" id="photo" name="photo" class="d-none" accept="image/*">

                                <div id="uploadBox" class="drop-zone-custom text-center p-5"
                                    style="display: flex; cursor:pointer;"
                                    onclick="document.getElementById('photo').click()">
                                    <div
                                        class="d-flex flex-column align-items-center justify-content-center w-100 h-100">
                                        <div class="cloud-icon-lg mb-3" style="font-size: 48px;">☁️</div>
                                        <p class="mb-2 text-muted">قم بسحب وإفلات الصورة هنا</p>
                                        <p class="mb-3">أو</p>
                                        <span class="btn btn-primary">اختر صورة</span>
                                    </div>
                                </div>

                                <div id="imagePreviewContainer" class="image-preview-custom text-center p-3 mt-3"
                                    style="display: none;">
                                    <img id="imagePreview" src="#" alt="صورة مختارة" class="img-fluid rounded"
                                        style="max-width: 200px;">
                                    <button type="button" class="btn btn-sm btn-danger mt-2"
                                        onclick="removeImage()">إزالة
                                        الصورة</button>
                                </div>

                                @error('photo')
                                <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-between mt-4">
                                <button type="button" class="btn btn-light" onclick="prevStep()">الخطوة السابقة</button>
                                <button type="submit" class="btn btn-dark">حفظ المنتج</button>
                            </div>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let variantIndex = 1;
    let currentStep = 1;

    function nextStep() {
        if (currentStep === 1) {
            document.getElementById('step1').classList.add('d-none');
            document.getElementById('step2').classList.remove('d-none');
            updateStepper(2);
            currentStep = 2;
        } else if (currentStep === 2) {
            document.getElementById('step2').classList.add('d-none');
            document.getElementById('step3').classList.remove('d-none');
            updateStepper(3);
            currentStep = 3;
        }
    }

    function prevStep() {
        if (currentStep === 2) {
            document.getElementById('step2').classList.add('d-none');
            document.getElementById('step1').classList.remove('d-none');
            updateStepper(1);
            currentStep = 1;
        } else if (currentStep === 3) {
            document.getElementById('step3').classList.add('d-none');
            document.getElementById('step2').classList.remove('d-none');
            updateStepper(2);
            currentStep = 2;
        }
    }

    function updateStepper(activeStep) {
        const steps = document.querySelectorAll('.step');
        steps.forEach((step, index) => {
            if (index + 1 === activeStep) {
                step.classList.add('active');
            } else {
                step.classList.remove('active');
            }
        });
    }

    function addVariant() {
        const variantsDiv = document.getElementById('variants');
        const newVariant = document.createElement('div');
        newVariant.className = 'variant-item mb-3 p-3 border rounded';
        newVariant.innerHTML = `
            <input type="text" name="variants[${variantIndex}][size]" placeholder="المقاس (M, L)"
                class="form-control form-control-custom mb-2">
            <input type="text" name="variants[${variantIndex}][color]" placeholder="اللون (أسود)"
                class="form-control form-control-custom mb-2">
            <input type="number" name="variants[${variantIndex}][quantity]" placeholder="الكمية"
                class="form-control form-control-custom mb-2">
            <input type="text" name="variants[${variantIndex}][material]" placeholder="الخامة"
                class="form-control form-control-custom mb-2">
            <input type="number" step="0.1" name="variants[${variantIndex}][weight]" placeholder="الوزن"
                class="form-control form-control-custom mb-2">
            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="this.parentElement.remove()">حذف</button>
        `;
        variantsDiv.appendChild(newVariant);
        variantIndex++;
    }

    // معاينة الصورة عند الرفع
    document.getElementById('photo')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const previewImg = document.getElementById('imagePreview');
                previewImg.src = event.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'block';
                document.getElementById('uploadBox').style.display = 'none';
            };
            reader.readAsDataURL(file);
        }
    });

    function removeImage() {
        document.getElementById('photo').value = '';
        document.getElementById('imagePreviewContainer').style.display = 'none';
        document.getElementById('uploadBox').style.display = 'flex';
    }
</script>
<script>
    let index = 1;

function addVariant(){
    let html = `
    <div class="variant-item mb-3">

        <input type="text" name="variants[${index}][size]" placeholder="المقاس"
            class="form-control mb-2">

        <input type="text" name="variants[${index}][color]" placeholder="اللون"
            class="form-control mb-2">

        <input type="number" name="variants[${index}][quantity]" placeholder="الكمية"
            class="form-control mb-2">

        <input type="text" name="variants[${index}][material]" placeholder="الخامة"
            class="form-control mb-2">

        <input type="number" step="0.1" name="variants[${index}][weight]" placeholder="الوزن"
            class="form-control mb-2">

    </div>
    `;

    document.getElementById('variants').insertAdjacentHTML('beforeend', html);
    index++;
}
</script>

<style>
    /* تنسيق إضافي للحدود والحواف */
    .variant-item {
        background-color: #f8f9fa;
        transition: all 0.3s ease;
    }

    .variant-item:hover {
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .drop-zone-custom {
        border: 2px dashed #dee2e6;
        border-radius: 0.75rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .drop-zone-custom:hover {
        border-color: #e91e63;
        background-color: rgba(233, 30, 99, 0.05);
    }

    /* الحاوية الرئيسية للخطوات */
    .stepper {
        display: flex;
        /* ترتيب الخطوات بجانب بعض */
        justify-content: space-between;
        /* توزيع متساوي */
        align-items: center;
        /* توسيط عمودي */
        background: linear-gradient(90deg, #1f1f1f, #3a3a3a);
        /* خلفية متدرجة */
        padding: 20px;
        border-radius: 15px;
        position: relative;
    }

    /* كل خطوة على حدة */
    .step {
        text-align: center;
        flex: 1;
        position: relative;
        color: #aaa;
        /* اللون الرمادي للخطوات غير النشطة */
    }

    /* الخطوة النشطة (اللي احنا فيها) */
    .step.active {
        color: #fff;
        /* اللون الأبيض */
    }

    /* الدائرة الصغيرة فوق كل خطوة */
    .step::before {
        content: "";
        width: 12px;
        height: 12px;
        background: #aaa;
        /* رمادي للغير نشطة */
        border-radius: 50%;
        display: block;
        margin: 0 auto 10px;
    }

    /* الدائرة للخطوة النشطة */
    .step.active::before {
        background: #fff;
        /* أبيض */
    }

    /* الخط الواصل بين الدوائر */
    .step::after {
        content: "";
        position: absolute;
        top: 6px;
        right: 50%;
        width: 100%;
        height: 2px;
        background: #fff;
        z-index: 1;
    }

    /* إخفاء الخط بعد آخر خطوة */
    .step:last-child::after {
        display: none;
    }

    /* تلوين الخط الواصل بعد الخطوة النشطة */
    .step.active+.step::after {
        background: #fff;
    }

    /* تصميم الحقول (الإدخال) المخصص */
    .form-control-custom {
        border: none;
        border-bottom: 1px solid #ccc;
        /* خط سفلي بس */
        border-radius: 0;
        background: transparent;
    }

    .form-control-custom:focus {
        box-shadow: none;
        border-color: #000;
    }
</style>
@endsection
