@extends('admin.layout')

@section('content')

<div class="row">
    <div class="col-12 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">تعديل القسم</h4>
                <p class="card-description">
                    قم بتحديث بيانات القسم المطلوبة
                </p>

                {{-- Form Start --}}
                <form class="forms-sample" method="POST" enctype="multipart/form-data"
                    action="{{ route('admin.categories.update', $category->id) }}" style="text-align:right" dir="rtl">
                    @csrf
                    @method('PUT')

                    {{-- اسم القسم --}}
                    <div class="form-group">
                        <span class="text-danger">@error('name'){{ $message }}@enderror</span>
                        <label for="name">اسم القسم</label>
                        <input type="text" class="form-control" name="name" id="name"
                            value="{{ old('name', $category->name) }}" placeholder="اسم القسم">
                    </div>

                    {{-- وصف القسم --}}
                    <div class="form-group">
                        <span class="text-danger">@error('description'){{ $message }}@enderror</span>
                        <label for="description">وصف القسم</label>
                        <textarea class="form-control" id="description" name="description" rows="4"
                            placeholder="وصف القسم">{{ old('description', $category->description) }}</textarea>
                    </div>

                    {{-- صورة القسم --}}
                    <div class="file-upload-wrapper my-4">
                        <input type="file" class="d-none" name="image" id="image" accept="image/*"
                            aria-describedby="imageFeedback">
                        <div id="imagePreviewContainer"
                            class="image-preview-custom p-3 d-flex flex-column align-items-center justify-content-center text-center"
                            style="min-height: 250px; border-radius: 0.75rem;
               display: {{ isset($category->imagepath) && $category->imagepath ? 'flex' : 'none' }};">

                            {{-- الصورة الحالية للقسم --}}
                            <img id="imagePreview"
                                src="{{ isset($category->imagepath) && $category->imagepath ? asset($category->imagepath) : '#' }}"
                                alt="صورة القسم" class="img-fluid rounded preview-image-size mb-2">

                            {{-- زر "تعديل الصورة" --}}
                            <label for="image" class="btn btn-orange action-button-style m-0">
                                تعديل الصورة
                            </label>

                        </div>

                        @error('image')
                        <div id="imageFeedback" class="invalid-feedback d-block mt-2">
                            {{ $message }}
                        </div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary mr-2">حفظ التعديلات</button>
                    <a href="{{ route('admin.categories.index') }}" class="btn btn-light">إلغاء</a>
                </form>
            </div>
        </div>
    </div>
</div>


<script>
    const fileInput = document.getElementById('image');
    const previewContainer = document.getElementById('imagePreviewContainer');
    const imagePreview = document.getElementById('imagePreview');

    // دالة التعامل مع اختيار ملف جديد
    function handleFileSelect() {
        const file = fileInput.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                imagePreview.src = e.target.result;
                previewContainer.style.display = 'flex';
            };
            reader.readAsDataURL(file);
        }
    }

    fileInput.addEventListener('change', handleFileSelect);

    // وظيفة السحب والإفلات (Drag & Drop)
    const uploadBox = document.getElementById('imagePreviewContainer');

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

@endsection