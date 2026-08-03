@extends('admin.layout')

@php
    $isCustom = ($productType ?? 'normal') === 'custom';
@endphp

@section('content')
<div class="container-fluid py-2">
    <div class="row min-vh-80">
        <div class="col-lg-8 col-md-10 col-12 m-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">المنتجات</a></li>
                    <li class="breadcrumb-item active">{{ $isCustom ? 'إضافة منتج مخصص' : 'إضافة منتج عادي' }}</li>
                </ol>
            </nav>
            <h3 class="mt-0 mb-5 text-center">
                {{ $isCustom ? 'إضافة منتج مخصص' : 'إضافة منتج عادي' }}
            </h3>
            <p class="lead font-weight-normal opacity-8 mb-7 text-center">
                {{ $isCustom
                    ? 'اختر القالب وسيتم إنشاء مناطق الطباعة تلقائياً بعد الحفظ'
                    : 'يمكنك إضافة منتج جديد للموقع'
                }}
            </p>
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data"
                        action="{{ route('admin.products.store') }}?type={{ $productType }}" style="text-align:right" dir="rtl">
                        @csrf

                        {{-- Section 1: Basic Information --}}
                        <div class="section-block mb-4">
                            <h5 class="section-title mb-3">1. بيانات المنتج الأساسية</h5>

                            <div class="form-group mb-3">
                                <label for="name">اسم المنتج</label>
                                <input type="text" class="form-control" name="name" id="name"
                                    value="{{ old('name') }}" placeholder="اسم المنتج">
                                @error('name')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="price">سعر المنتج</label>
                                <input type="number" step="0.01" class="form-control" name="price" id="price"
                                    value="{{ old('price') }}" placeholder="السعر">
                                @error('price')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <div class="form-group mb-3">
                                <label for="category_id">قسم المنتج</label>
                                <select class="form-control" name="category_id" id="category_id">
                                    @foreach ($allcategories as $item)
                                    <option value="{{ $item->id }}" {{ old('category_id') == $item->id ? 'selected' : '' }}>{{ $item->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="form-group mb-3">
                                <label for="description">وصف المنتج</label>
                                <textarea class="form-control" id="description" name="description" rows="4"
                                    placeholder="وصف المنتج">{{ old('description') }}</textarea>
                                @error('description')
                                <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        {{-- Section 2: Variants --}}
                        <div class="section-block mb-4">
                            <h5 class="section-title mb-3">2. المقاسات والألوان</h5>

                            <div id="variants">
                                <div class="variant-item mb-3 p-3 border rounded">
                                    <input type="text" name="variants[0][size]" placeholder="المقاس (M, L)"
                                        class="form-control mb-2">
                                    <input type="text" name="variants[0][color]" placeholder="اللون (أسود)"
                                        class="form-control mb-2">
                                    <input type="number" name="variants[0][quantity]" placeholder="الكمية"
                                        class="form-control mb-2">
                                    <input type="text" name="variants[0][material]" placeholder="الخامة"
                                        class="form-control mb-2">
                                    <input type="number" step="0.1" name="variants[0][weight]" placeholder="الوزن"
                                        class="form-control mb-2">
                                </div>
                            </div>

                            <button type="button" class="btn btn-dark mb-3" onclick="addVariant()">
                                + إضافة مقاس جديد
                            </button>
                        </div>

                        {{-- Section 3: Product Image --}}
                        <div class="section-block mb-4">
                            <h5 class="section-title mb-3">3. الصورة الرئيسية</h5>

                            <div class="border rounded p-5 text-center mt-2 position-relative">
                                <input type="file" id="photo" name="photo" class="d-none" accept="image/*">

                                <div id="uploadBox" class="drop-zone-custom text-center p-5"
                                    style="display: flex; cursor:pointer;"
                                    onclick="document.getElementById('photo').click()">
                                    <div class="d-flex flex-column align-items-center justify-content-center w-100 h-100">
                                        <div class="cloud-icon-lg mb-3" style="font-size: 48px;">&#9729;</div>
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
                                        onclick="removeImage()">إزالة الصورة</button>
                                </div>

                                @error('photo')
                                <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        {{-- Section 4: Design Settings (adapts to type) --}}
                        @if($isCustom)
                            {{-- Custom: Template selector only --}}
                            <div class="section-block mb-4">
                                <h5 class="section-title mb-3">4. قالب المنتج</h5>

                                <div class="form-group mb-3">
                                    <label for="product_template_id">اختر القالب</label>
                                    <select class="form-control" name="product_template_id" id="product_template_id" required>
                                        <option value="">— اختر قالب —</option>
                                        @foreach ($templates as $templateId => $templateName)
                                        <option value="{{ $templateId }}" {{ old('product_template_id') == $templateId ? 'selected' : '' }}>
                                            {{ $templateName }}
                                        </option>
                                        @endforeach
                                    </select>
                                    <small class="form-text text-muted">سيتم إنشاء مناطق الطباعة تلقائياً بعد حفظ المنتج.</small>
                                </div>
                            </div>
                        @else
                            {{-- Normal: Optional designable settings --}}
                            <div class="section-block mb-4">
                                <h5 class="section-title mb-3">
                                    4. إعدادات التصميم
                                    <small class="text-muted" style="font-size:13px;font-weight:normal;">(اختياري)</small>
                                </h5>

                                <div class="form-group mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_designable" name="is_designable" value="1" {{ old('is_designable') ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_designable">منتج قابل للتصميم (Designable)</label>
                                    </div>
                                </div>

                                <div id="designableOptions" class="{{ old('is_designable') ? '' : 'd-none' }}">
                                    <div class="form-group mb-3">
                                        <label for="print_cost_type">نوع تكلفة الطباعة</label>
                                        <select class="form-control" name="print_cost_type" id="print_cost_type">
                                            <option value="">غير محدد</option>
                                            <option value="per_print" {{ old('print_cost_type') == 'per_print' ? 'selected' : '' }}>لكل طبعة</option>
                                            <option value="per_area" {{ old('print_cost_type') == 'per_area' ? 'selected' : '' }}>لكل منطقة</option>
                                            <option value="fixed" {{ old('print_cost_type') == 'fixed' ? 'selected' : '' }}>سعر ثابت</option>
                                        </select>
                                    </div>

                                    <div class="alert alert-info py-2 px-3" style="font-size:14px;border-radius:8px;">
                                        بعد حفظ المنتج، يمكنك تحرير مناطق الطباعة باستخدام المحرر البصري من صفحة تعديل المنتج.
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="text-center mt-4">
                            <button type="submit" class="btn btn-dark btn-lg px-5">حفظ المنتج</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    let variantIndex = 1;

    function addVariant() {
        const variantsDiv = document.getElementById('variants');
        const div = document.createElement('div');
        div.className = 'variant-item mb-3 p-3 border rounded';
        div.innerHTML = `
            <input type="text" name="variants[${variantIndex}][size]" placeholder="المقاس (M, L)"
                class="form-control mb-2">
            <input type="text" name="variants[${variantIndex}][color]" placeholder="اللون (أسود)"
                class="form-control mb-2">
            <input type="number" name="variants[${variantIndex}][quantity]" placeholder="الكمية"
                class="form-control mb-2">
            <input type="text" name="variants[${variantIndex}][material]" placeholder="الخامة"
                class="form-control mb-2">
            <input type="number" step="0.1" name="variants[${variantIndex}][weight]" placeholder="الوزن"
                class="form-control mb-2">
            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="this.parentElement.remove()">حذف</button>
        `;
        variantsDiv.appendChild(div);
        variantIndex++;
    }

    @if(!$isCustom)
    document.getElementById('is_designable')?.addEventListener('change', function() {
        document.getElementById('designableOptions').classList.toggle('d-none', !this.checked);
    });
    @endif

    document.getElementById('photo')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('imagePreview').src = event.target.result;
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

<style>
    .section-block {
        padding: 20px;
        background: #f8f9fa;
        border-radius: 12px;
        border: 1px solid #e9ecef;
    }
    .section-title {
        color: #333;
        padding-bottom: 10px;
        border-bottom: 2px solid #dee2e6;
        margin-bottom: 15px;
    }
    .variant-item {
        background-color: #fff;
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
</style>
@endsection
