@extends('admin.layout')

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title mb-4">تعديل المنتج: {{ $product->name }}</h4>

                @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                {{-- Form Start --}}
                <form class="forms-sample" method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.products.update', $product->id) }}" style="text-align:right" dir="rtl">
                    @csrf()

                    {{-- ============================================================ --}}
                    {{-- Section 1: Product Information --}}
                    {{-- ============================================================ --}}
                    <div class="card mb-4">
                        <div class="card-header" data-toggle="collapse" data-target="#sectionInfo" style="cursor:pointer;background:#f8f9fa;">
                            <h5 class="mb-0">1. معلومات المنتج <i class="ti-angle-down float-left"></i></h5>
                        </div>
                        <div id="sectionInfo" class="collapse show">
                            <div class="card-body">
                                <div class="form-group">
                                    <span class="text-danger">@error('name'){{ $message }}@enderror</span>
                                    <label for="name">اسم المنتج</label>
                                    <input type="text" class="form-control" name="name" id="name"
                                        value="{{ old('name', $product->name) }}" placeholder="الاسم">
                                </div>

                                <div class="form-group">
                                    <span class="text-danger">@error('price'){{ $message }}@enderror</span>
                                    <label for="price">سعر المنتج</label>
                                    <input type="number" step="0.01" class="form-control" name="price" id="price"
                                        value="{{ old('price', $product->price) }}" placeholder="السعر">
                                </div>

                                <div class="form-group">
                                    <label for="category_id">قسم المنتج</label>
                                    <select class="form-control" name="category_id">
                                        @foreach ($allcategories as $item)
                                        <option value="{{ $item->id }}" {{ $item->id == old('category_id', $product->category_id) ? 'selected' : '' }}>
                                            {{ $item->name }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <span class="text-danger">@error('description'){{ $message }}@enderror</span>
                                    <label for="description">وصف المنتج</label>
                                    <textarea class="form-control" id="description" name="description" rows="4"
                                        placeholder="وصف المنتج">{{ old('description', $product->description) }}</textarea>
                                </div>

                                {{-- Main Photo --}}
                                <div class="file-upload-wrapper my-4">
                                    <label>الصورة الرئيسية</label>
                                    <input type="file" class="d-none" name="photo" id="photo" accept="image/*">
                                    <div id="imagePreviewContainer"
                                        class="image-preview-custom p-3 d-flex flex-column align-items-center justify-content-center text-center"
                                        style="min-height: 200px; border-radius: 0.75rem; border: 1px solid #dee2e6;
                                           display: {{ $product->imagepath ? 'flex' : 'none' }};">
                                        <img id="imagePreview"
                                            src="{{ $product->imagepath ? asset($product->imagepath) : '#' }}"
                                            alt="صورة المنتج" class="img-fluid rounded mb-2" style="max-height:200px;">
                                        <label for="photo" class="btn btn-orange btn-sm m-0">تعديل الصورة</label>
                                    </div>
                                    @error('photo')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- Section 2: Variants --}}
                    {{-- ============================================================ --}}
                    <div class="card mb-4">
                        <div class="card-header" data-toggle="collapse" data-target="#sectionVariants" style="cursor:pointer;background:#f8f9fa;">
                            <h5 class="mb-0">2. المقاسات والألوان <i class="ti-angle-down float-left"></i></h5>
                        </div>
                        <div id="sectionVariants" class="collapse show">
                            <div class="card-body">
                                <div id="variants">
                                    @foreach($product->variants as $index => $variant)
                                    <div class="variant-item mb-3 p-3 border rounded">
                                        <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">
                                        <div class="row">
                                            <div class="col-md-3 mb-2">
                                                <input type="text" name="variants[{{ $index }}][size]" value="{{ $variant->size }}"
                                                    class="form-control" placeholder="المقاس">
                                            </div>
                                            <div class="col-md-3 mb-2">
                                                <input type="text" name="variants[{{ $index }}][color]" value="{{ $variant->color }}"
                                                    class="form-control" placeholder="اللون">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="number" name="variants[{{ $index }}][quantity]"
                                                    value="{{ $variant->quantity }}" class="form-control" placeholder="الكمية">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="text" name="variants[{{ $index }}][material]" value="{{ $variant->material }}"
                                                    class="form-control" placeholder="الخامة">
                                            </div>
                                            <div class="col-md-2 mb-2">
                                                <input type="number" step="0.1" name="variants[{{ $index }}][weight]" value="{{ $variant->weight }}"
                                                    class="form-control" placeholder="الوزن">
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-danger mt-1 remove-variant">حذف</button>
                                    </div>
                                    @endforeach
                                </div>
                                <button type="button" class="btn btn-dark mt-2" onclick="addVariant()">+ إضافة مقاس جديد</button>
                            </div>
                        </div>
                    </div>

                    {{-- ============================================================ --}}
                    {{-- Section 4: Design Areas (inside same form as sections 1+2) --}}
                    {{-- ============================================================ --}}
                    <div class="card mb-4">
                        <div class="card-header" data-toggle="collapse" data-target="#sectionDesign" style="cursor:pointer;background:#f8f9fa;">
                            <h5 class="mb-0">4. مناطق الطباعة والإعدادات المتقدمة <i class="ti-angle-down float-left"></i></h5>
                        </div>
                        <div id="sectionDesign" class="collapse show">
                            <div class="card-body">
                                {{-- Product Type Info --}}
                                @if($product->type)
                                <div class="mb-3 p-3" style="background:#f0f4f8;border-radius:8px;">
                                    <div class="row">
                                        <div class="col-md-4">
                                            <strong class="small text-muted">نوع المنتج:</strong>
                                            <span class="badge badge-{{ $product->type->value === 'custom' ? 'info' : 'secondary' }} mr-1">
                                                {{ $product->type->label() }}
                                            </span>
                                        </div>
                                        @if($product->template)
                                        <div class="col-md-4">
                                            <strong class="small text-muted">القالب:</strong>
                                            <span>{{ $product->template->name }}</span>
                                        </div>
                                        <div class="col-md-4">
                                            <strong class="small text-muted">إصدار القالب:</strong>
                                            <span>v{{ $product->template_version ?? $product->template->version }}</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endif

                                <div class="form-group mb-3">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="is_designable" name="is_designable" value="1"
                                            {{ old('is_designable', $product->is_designable) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="is_designable">منتج قابل للتصميم (Designable)</label>
                                    </div>
                                </div>

                                <div id="designableOptions" class="{{ old('is_designable', $product->is_designable) ? '' : 'd-none' }}">
                                    <div class="mb-3">
                                        <a href="{{ route('admin.products.print-areas', $product->id) }}" class="btn btn-dark">
                                            تحرير مناطق الطباعة (بصري)
                                        </a>
                                        @if($product->printAreas->count() > 0)
                                        <span class="text-muted mr-2" style="font-size:13px;">
                                            ({{ $product->printAreas->count() }} منطقة معرفة)
                                        </span>
                                        @endif
                                    </div>

                                    <div class="mt-3">
                                        <a data-toggle="collapse" data-target="#advancedSettings" style="cursor:pointer;font-size:13px;color:#007bff;">
                                            اعدادات متقدمة
                                        </a>
                                        <div id="advancedSettings" class="collapse mt-2 p-3" style="background:#f8f9fa;border-radius:8px;">
                                            <div class="form-group mb-0">
                                                <label for="print_cost_type">نوع تكلفة الطباعة</label>
                                                <select class="form-control" name="print_cost_type" id="print_cost_type">
                                                    <option value="">غير محدد</option>
                                                    <option value="per_print" {{ old('print_cost_type', $product->print_cost_type) == 'per_print' ? 'selected' : '' }}>لكل طبعة</option>
                                                    <option value="per_area" {{ old('print_cost_type', $product->print_cost_type) == 'per_area' ? 'selected' : '' }}>لكل منطقة</option>
                                                    <option value="fixed" {{ old('print_cost_type', $product->print_cost_type) == 'fixed' ? 'selected' : '' }}>سعر ثابت</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">حفظ التغييرات</button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary btn-lg px-4 mr-2">رجوع</a>
                    </div>
                </form>

                {{-- ============================================================ --}}
                {{-- Section 3: Product Images (standalone — NOT inside main form) --}}
                {{-- ============================================================ --}}
                <div class="card mb-4 mt-4">
                    <div class="card-header" data-toggle="collapse" data-target="#sectionImages" style="cursor:pointer;background:#f8f9fa;">
                        <h5 class="mb-0">3. صور المنتج <i class="ti-angle-down float-left"></i></h5>
                    </div>
                    <div id="sectionImages" class="collapse show">
                        <div class="card-body">
                            {{-- Upload Form --}}
                            <div class="mb-4 p-3" style="background:#f0f4f8;border-radius:8px;">
                                <h6 class="mb-3">إضافة صورة جديدة</h6>
                                <form action="{{ route('storeProductImage') }}" method="POST" enctype="multipart/form-data" class="row align-items-end">
                                    @csrf
                                    <input type="hidden" name="product_id" value="{{ $product->id }}">

                                    <div class="col-md-4 mb-2">
                                        <label class="small">عرض المنتج</label>
                                        <select name="view_name" class="form-control">
                                            <option value="front" selected>أمامي</option>
                                            <option value="back">خلفي</option>
                                            <option value="left_sleeve">كم أيسر</option>
                                            <option value="right_sleeve">كم أيمن</option>
                                            <option value="hood">هود</option>
                                            <option value="pocket">جيب</option>
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="small">اللون</label>
                                        <select name="color" class="form-control">
                                            <option value="">بدون لون</option>
                                            @foreach($product->variants->pluck('color')->filter()->unique() as $color)
                                            <option value="{{ strtolower(trim($color)) }}">{{ $color }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-3 mb-2">
                                        <label class="small">اختر صورة</label>
                                        <input type="file" name="photo" class="form-control" required>
                                        @error('photo')
                                        <small class="text-danger">{{ $message }}</small>
                                        @enderror
                                    </div>
                                    <div class="col-md-2 mb-2">
                                        <button type="submit" class="btn btn-primary w-100">رفع</button>
                                    </div>
                                </form>
                            </div>

                            {{-- Gallery --}}
                            @php
                                $groupedPhotos = $product->productphotos->groupBy(function($p) {
                                    return $p->view_name ?? 'general';
                                });
                                $viewLabels = ['front' => 'أمامي', 'back' => 'خلفي', 'left_sleeve' => 'كم أيسر', 'right_sleeve' => 'كم أيمن', 'hood' => 'هود', 'pocket' => 'جيب', 'general' => 'عام'];
                            @endphp

                            @forelse($groupedPhotos as $viewName => $photos)
                            <div class="mb-3">
                                <h6 class="text-muted">{{ $viewLabels[$viewName] ?? $viewName }}</h6>
                                <div class="d-flex flex-wrap">
                                    @foreach($photos as $photo)
                                    <div class="card m-1" style="width:150px;">
                                        <img src="{{ asset($photo->imagepath) }}" style="height:120px;object-fit:cover;border-radius:8px 8px 0 0;">
                                        <div class="p-1 text-center small">
                                            @if($photo->color)<span class="badge badge-secondary">{{ $photo->color }}</span>@endif
                                        </div>
                                        <form action="{{ route('removeproductphoto', $photo->id) }}" method="POST">
                                            @csrf @method('DELETE')
                                            <button class="btn btn-danger btn-sm w-100" style="border-radius:0 0 8px 8px;">حذف</button>
                                        </form>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @empty
                            <p class="text-muted text-center">لا توجد صور إضافية. قم برفع الصور الخاصة بكل عرض من أعلى.</p>
                            @endforelse

                            <div class="text-muted small mt-2">
                                <strong>عدد الصور:</strong> {{ $product->productphotos->count() }} |
                                <strong>العروض:</strong>
                                @foreach($viewLabels as $k => $l)
                                    @if($groupedPhotos->has($k)) {{ $l }} · @endif
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    // Photo change preview
    document.getElementById('photo')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('imagePreview').src = ev.target.result;
                document.getElementById('imagePreviewContainer').style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    });

    // Remove variant
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-variant')) {
            e.target.closest('.variant-item').remove();
        }
    });

    // Add variant
    let index = {{ count($product->variants) }};
    function addVariant() {
        let html = `
        <div class="variant-item mb-3 p-3 border rounded">
            <div class="row">
                <div class="col-md-3 mb-2">
                    <input type="text" name="variants[${index}][size]" placeholder="المقاس" class="form-control">
                </div>
                <div class="col-md-3 mb-2">
                    <input type="text" name="variants[${index}][color]" placeholder="اللون" class="form-control">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="number" name="variants[${index}][quantity]" placeholder="الكمية" class="form-control">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="text" name="variants[${index}][material]" placeholder="الخامة" class="form-control">
                </div>
                <div class="col-md-2 mb-2">
                    <input type="number" step="0.1" name="variants[${index}][weight]" placeholder="الوزن" class="form-control">
                </div>
            </div>
            <button type="button" class="btn btn-sm btn-danger mt-1 remove-variant">حذف</button>
        </div>`;
        document.getElementById('variants').insertAdjacentHTML('beforeend', html);
        index++;
    }

    // Toggle designable options
    document.getElementById('is_designable')?.addEventListener('change', function() {
        document.getElementById('designableOptions').classList.toggle('d-none', !this.checked);
    });
</script>
@endsection
