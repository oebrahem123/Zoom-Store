@extends('admin.layout')

@section('content')
<style>
    .image-preview-custom {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .preview-image-size {
        max-width: 250px;
        height: auto;
        display: block;
        margin-bottom: 10px;
    }

    .drop-zone-custom {
        border: 2px dashed #ddd;
        border-radius: 10px;
        cursor: pointer;
        transition: border-color 0.3s ease;
    }

    .drop-zone-custom:hover {
        border-color: #007bff;
    }

    .dragover {
        border-color: #007bff;
        background-color: #f8f9fa;
    }
</style>

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">إضافة قسم جديد</h4>
                <p class="card-description">
                    قم بإضافة بيانات القسم الجديد
                </p>

                {{-- Form Start --}}
                <form class="forms-sample" method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.categories.store') }}" style="text-align:right" dir="rtl">
                    @csrf

                    {{-- اسم القسم --}}
                    <div class="form-group">
                        <span class="text-danger">
                            @error('name')
                            {{ $message }}
                            @enderror
                        </span>
                        <label for="name">اسم القسم</label>
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name') }}"
                            placeholder="اسم القسم">
                    </div>

                    {{-- وصف القسم --}}
                    <div class="form-group">
                        <span class="text-danger">
                            @error('description')
                            {{ $message }}
                            @enderror
                        </span>
                        <label for="description">وصف القسم (اختياري)</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="وصف القسم">{{ old('description') }}</textarea>
                    </div>

                    {{-- رفع صورة القسم --}}
                    <div class="file-upload-wrapper my-4">
                        <input type="file" class="d-none" name="imagepath" id="imagepath" accept="image/*"
                            aria-describedby="imagepathFeedback">

                        <div id="uploadBox" class="drop-zone-custom text-center p-5
                                    @error('imagepath') is-invalid-border @enderror" style="display: flex;"
                            data-empty="true">

                            <label for="imagepath"
                                class="d-flex flex-column align-items-center justify-content-center w-100 h-100 cursor-pointer">
                                <div class="cloud-icon-lg mb-3">☁️</div>
                                <p class="mb-2 text-muted">قم بسحب وإفلات الصورة هنا</p>
                                <p class="mb-3">أو</p>
                                <label for="imagepath" class="d-flex flex-column align-items-center">
                                    <span class="btn btn-primary">
                                        اختر صورة
                                    </span>
                                </label>
                            </label>
                        </div>

                        <div id="imagePreviewContainer" class="image-preview-custom text-center p-3"
                            style="display: none;">
                            <img id="imagePreview" src="#" alt="صورة مختارة"
                                class="img-fluid rounded preview-image-size">
                            <button type="button" class="btn btn-sm btn-danger mt-2" onclick="removeImage()">
                                إزالة الصورة
                            </button>
                        </div>

                        @error('imagepath')
                        <div id="imagepathFeedback" class="text-danger mt-2">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    {{-- زر الحفظ --}}
                    <button type="submit" class="btn btn-primary mr-2">حفظ القسم</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">إلغاء</a>

                </form>
            </div>
        </div>
    </div>
</div>


<script>
    const fileInput = document.getElementById('imagepath');
    const uploadBox = document.getElementById('uploadBox');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    // الدالة الرئيسية للتعامل مع اختيار الملف
    function handleFileSelect(event) {
        const file = fileInput.files[0];

        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                // عرض الصورة في عنصر المعاينة
                imagePreview.src = e.target.result;

                // إخفاء منطقة الرفع وإظهار المعاينة
                uploadBox.style.display = 'none';
                previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    }

    // التعامل مع زر "إزالة الصورة"
    function removeImage() {
        // مسح قيمة حقل الإدخال
        fileInput.value = '';

        // إخفاء المعاينة وإظهار منطقة الرفع
        previewContainer.style.display = 'none';
        uploadBox.style.display = 'flex';
        imagePreview.src = '#';
    }

    // ربط الدالة بحدث التغيير في حقل الإدخال
    fileInput.addEventListener('change', handleFileSelect);

    // إضافة وظيفة السحب والإفلات
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
        handleFileSelect(); // معالجة الملفات المسقطة
    }, false);

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
</script>
@endsection