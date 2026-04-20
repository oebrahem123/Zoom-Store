@extends('admin.layout')

@section('content')


<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">تعديل المنتج</h4>
                <p class="card-description">
                    قم بتحديث بيانات المنتج المطلوبة
                </p>

                {{-- Form Start --}}
                <form class="forms-sample" method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.products.update', $product->id) }}" style="text-align:right" dir="rtl">
                    @csrf()

                    {{-- اسم المنتج --}}
                    <div class="form-group">
                        <span class="text-danger">@error('name'){{ $message }}@enderror</span>
                        <label for="name">اسم المنتج</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ old('name', $product->name) }}" placeholder="الاسم">
                    </div>

                    {{-- السعر --}}
                    <div class="form-group">
                        <span class="text-danger">@error('price'){{ $message }}@enderror</span>
                        <label for="price">سعر المنتج</label>
                        <input type="number" class="form-control" name="price" id="price"
                            value="{{ old('price', $product->price) }}" placeholder="السعر">
                    </div>

                    {{-- الكمية --}}
                    <div class="form-group">
                        <span class="text-danger">@error('quantity'){{ $message }}@enderror</span>
                        <label for="quantity">الكمية المتاحة</label>
                        <input type="number" class="form-control" name="quantity" id="quantity"
                            value="{{ old('quantity', $product->quantity) }}" placeholder="الكمية">
                    </div>

                    {{-- الوصف --}}
                    <div class="form-group">
                        <span class="text-danger">@error('description'){{ $message }}@enderror</span>
                        <label for="description">وصف المنتج</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="وصف المنتج">{{ old('description', $product->description) }}</textarea>
                    </div>

                    {{-- القسم --}}
                    <div class="form-group">
                        <label for="category_id">قسم المنتج</label>
                        <select class="form-control" name="category_id">
                            @foreach ($allcategories as $item)
                            <option value="{{ $item->id }}" {{ $item->id == old('category_id', $product->category_id) ?
                                'selected' : '' }}>
                                {{ $item->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!--Edit photo-->
                    <div class="file-upload-wrapper my-4">
                        <input type="file" class="d-none" name="photo" id="photo" accept="image/*"
                            aria-describedby="photoFeedback">
                        <div id="imagePreviewContainer"
                            class="image-preview-custom p-3 d-flex flex-column align-items-center justify-content-center text-center"
                            style="min-height: 250px; border-radius: 0.75rem;
               display: {{ isset($product->imagepath) && $product->imagepath ? 'flex' : 'none' }};">

                            {{-- الصورة الحالية للمنتج --}}
                            <img id="imagePreview"
                                src="{{ isset($product->imagepath) && $product->imagepath ? asset($product->imagepath) : '#' }}"
                                alt="صورة المنتج" class="img-fluid rounded preview-image-size mb-2">

                            {{-- زر "تعديل الصورة" فقط --}}
                            <label for="photo" class="btn btn-orange action-button-style m-0">
                                تعديل الصورة
                            </label>

                        </div>

                        @error('photo')
                        <div id="photoFeedback" class="invalid-feedback d-block mt-2">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <!-- End Edit photo-->
                    <h5 class="mt-4">المقاسات والألوان</h5>

                    <div id="variants">

                        @foreach($product->variants as $index => $variant)

                        <div class="variant-item mb-3">

                            <input type="hidden" name="variants[{{ $index }}][id]" value="{{ $variant->id }}">

                            <input type="text" name="variants[{{ $index }}][size]" value="{{ $variant->size }}"
                                class="form-control mb-2">

                            <input type="text" name="variants[{{ $index }}][color]" value="{{ $variant->color }}"
                                class="form-control mb-2">

                            <input type="number" name="variants[{{ $index }}][quantity]"
                                value="{{ $variant->quantity }}" class="form-control mb-2">

                            <input type="text" name="variants[{{ $index }}][material]" value="{{ $variant->material }}"
                                class="form-control mb-2">

                            <input type="number" name="variants[{{ $index }}][weight]" value="{{ $variant->weight }}"
                                class="form-control mb-2">

                            <button type="button" class="btn btn-danger remove-variant">حذف</button>

                        </div>

                        @endforeach

                    </div>
                    <div class="" style="margin-bottom: 20px;">
                        <button type="button" class="btn btn-dark mt-3" onclick="addVariant()">
                            +إضافة مقاس جديد
                        </button>
                    </div>
                    <button type="submit" class="btn btn-primary mr-2">حفظ</button>

                </form>
            </div>
        </div>
    </div>





































    <script>
        const fileInput = document.getElementById('photo');
    const uploadBox = document.getElementById('uploadBox');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    // **الدالة الرئيسية للتعامل مع اختيار ملف جديد (عن طريق تعديل الصورة أو السحب)**
    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                // تبديل العرض (في حال تم الاختيار من السحابة)
                uploadBox.style.display = 'none';
                previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    }

    // **دالة "إزالة الصورة" الآن فارغة/ملغاة لأنك لا تحتاجها**
    function removeImage() {
        // يمكنك تركها فارغة أو حذفها بالكامل، ولكن بقاؤها يمنع حدوث أخطاء إذا كانت مستدعاة.
        console.log("وظيفة إزالة الصورة معطلة في صفحة التعديل.");
    }

    fileInput.addEventListener('change', handleFileSelect);

    // **وظيفة السحب والإفلات (Drag & Drop)**
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        uploadBox.addEventListener(eventName, e => {
            e.preventDefault();
            e.stopPropagation();
        }, false);
    });

    uploadBox.addEventListener('drop', (e) => {
        fileInput.files = e.dataTransfer.files;
        handleFileSelect();
    }, false);
    </script>
    <script>
        document.addEventListener('click', function(e){

    if(e.target.classList.contains('remove-variant')){
        e.target.parentElement.remove();
    }

});
    </script>
    <script>
        let index = {{ count($product->variants) }};

function addVariant() {

    let html = `
    <div class="variant-item mb-3">

        <input type="text" name="variants[${index}][size]" placeholder="المقاس" class="form-control mb-2">

        <input type="text" name="variants[${index}][color]" placeholder="اللون" class="form-control mb-2">

        <input type="number" name="variants[${index}][quantity]" placeholder="الكمية" class="form-control mb-2">

        <input type="text" name="variants[${index}][material]" placeholder="الخامة" class="form-control mb-2">

        <input type="number" name="variants[${index}][weight]" placeholder="الوزن" class="form-control mb-2">

        <button type="button" class="btn btn-danger remove-variant">حذف</button>

    </div>
    `;

    document.getElementById('variants').insertAdjacentHTML('beforeend', html);

    index++;
}
    </script>
    @endsection
