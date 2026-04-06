@extends('admin.layout')

@section('content')
<style>
    .image-preview-custom {
    display: flex;
    flex-direction: column; /* يخلي العناصر فوق بعض */
    align-items: center; /* يخلي الصورة والزرار في النص */
    justify-content: center;
}

.preview-image-size {
    max-width: 250px;
    height: auto;
    display: block;
    margin-bottom: 10px; /* مسافة صغيرة بين الصورة والزر */
}
    </style>
    {{--  -----------------------------------------------  --}}

          <div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">إضافة المنتجات</h4>
                <p class="card-description">
                    بالرجاء إضفه بيانات المنتج الجديد
                </p>
                {{-- إسم المنتج --}}
                <form class="forms-sample" method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.products.store') }}" style="text-align:right" dir="rtl">
                    @csrf()
                    <div class="form-group">
                        <span class="text-danger">
                            @error('name')
                                {{ $message }}
                            @enderror
                        </span>
                        <label for="exampleInputName1">إسم المنتج</label>
                        <input type="text" class="form-control"name="name" id="name"
                            value="{{ old('name') }}"placeholder="الإسم">
                    </div>
                    {{-- السعر --}}
                    <div class="form-group">
                        @error('price')
                            {{ $message }}
                        @enderror
                        <label for="exampleInputName1">سعر المنتج</label>
                        <input type="number" class="form-control" name="price" id="price" value="{{ old('price') }}"
                            placeholder="السعر">
                    </div>
                    {{-- الكمية --}}
                    <div class="form-group">
                        @error('quantity')
                            {{ $message }}
                        @enderror
                        <label for="exampleInputName1">الكميه المتاحه للمنتج</label>
                        <input type="number" class="form-control" name="quantity" id="quantity"
                            value="{{ old('quantity') }}" placeholder="الكميه">
                    </div>

                    {{-- الوصف --}}
                    <div class="form-group">
                        @error('description')
                            {{ $message }}
                        @enderror
                        <label for="exampleInputName1">وصف المننج</label>
                        <textarea class="form-control" id="description" name="description" rows="4" type="text" placeholder="وصف المنتج"
                            value="{{ old('Description') }}"></textarea>
                    </div>
                    {{-- قسم المنتجات --}}
                    <select class="form-control" name="category_id">
                        @foreach ($allcategories as $item)
                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                        @endforeach
                    </select>
                    {{-- رفع الصورة --}}
                    <!--uploud photo-->

                    <div class="file-upload-wrapper my-4">
                        <input type="file" class="d-none" name="photo" id="photo" accept="image/*"
                            aria-describedby="photoFeedback">

                        <div id="uploadBox"
                            class="drop-zone-custom text-center p-5
        @error('photo') is-invalid-border @enderror"
                            style="display: flex;" data-empty="true">

                            <label for="photo"
                                class="d-flex flex-column align-items-center justify-content-center w-100 h-100 cursor-pointer">
                                <div class="cloud-icon-lg mb-3">☁️</div>

                                <p class="mb-2 text-muted">قم بسحب وإفلات الصورة هنا</p>
                                <p class="mb-3">أو</p>
                                <label for="photo" class="d-flex flex-column align-items-center ...">
                                    <span class="btn btn-primary">
                                        اختر صورة
                                    </span>
                                </label>
                            </label>
                        </div>

                        <div id="imagePreviewContainer" class="image-preview-custom text-center p-3" style="display: none;">
                            <img id="imagePreview" src="#" alt="صورة مختارة"
                                class="img-fluid rounded preview-image-size">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">إزالة
                                الصورة</button>
                        </div>

                        @error('photo')
                            <div id="photoFeedback" class="text-danger mt-2">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    <!-- End uploud photo-->
                    {{-- حفظ المتج --}}
                    <button type="submit" class="btn btn-primary mr-2">حفظ</button>
                </form>
            </div>
        </div>
    </div>
</div>




























    <!--stat js code-->

    <!--start js code uploud photo--->


    <script>
        const fileInput = document.getElementById('photo');
        const uploadBox = document.getElementById('uploadBox');
        const previewContainer = document.getElementById('imagePreviewContainer');
        const imagePreview = document.getElementById('imagePreview');

        // **الدالة الرئيسية للتعامل مع اختيار الملف**
        function handleFileSelect(event) {
            const file = fileInput.files[0];

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // 1. عرض الصورة في عنصر المعاينة
                    imagePreview.src = e.target.result;

                    // 2. إخفاء منطقة الرفع وإظهار المعاينة
                    uploadBox.style.display = 'none';
                    previewContainer.style.display = 'flex'; // استخدام flex لعرض الصورة في المنتصف
                };
                reader.readAsDataURL(file);
            }
        }

        // **التعامل مع زر "إزالة الصورة"**
        function removeImage() {
            // 1. مسح قيمة حقل الإدخال
            fileInput.value = '';

            // 2. إخفاء المعاينة وإظهار منطقة الرفع
            previewContainer.style.display = 'none';
            uploadBox.style.display = 'flex';
            imagePreview.src = '#';
        }

        // ربط الدالة بحدث التغيير في حقل الإدخال
        fileInput.addEventListener('change', handleFileSelect);

        // **إضافة وظيفة السحب والإفلات الأساسية (UX)**
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            uploadBox.addEventListener(eventName, () => uploadBox.classList.add('dragover'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            uploadBox.addEventListener(eventName, () => uploadBox.classList.remove('dragover'), false);
        });

        uploadBox.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            fileInput.files = dt.files;
            handleFileSelect(); // معالجة الملفات المـُسقطة
        }, false);

        function preventDefaults(e) {
            e.preventDefault();
            e.stopPropagation();
        }
    </script>
    <!--End js code uploud photo--->


    <!--End js code-->
@endsection
