@extends('admin.layout')
@section('content')
<style>
    .preview-wrap { text-align: center; margin-top: 15px; }
    .preview-wrap img { max-width: 250px; max-height: 250px; border-radius: 8px; display: none; }
</style>

<div class="contener">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title text-center mb-4">إضافة تصميم جديد</h4>

                <form action="{{ route('admin.designs.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">الصورة</label>
                        <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                            accept="image/*" required onchange="previewImage(this)">
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div class="preview-wrap">
                            <img id="imagePreview" src="#" alt="معاينة">
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg" style="padding: 12px 48px; border-radius: 12px;">
                            <i class="mdi mdi-content-save"></i> حفظ التصميم
                        </button>
                        <a href="{{ route('admin.designs.index') }}" class="btn btn-secondary btn-lg" style="padding: 12px 48px; border-radius: 12px;">إلغاء</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function previewImage(input) {
        const preview = document.getElementById('imagePreview');
        if (input.files && input.files[0]) {
            const reader = new FileReader();
            reader.onload = function(e) { preview.src = e.target.result; preview.style.display = 'block'; };
            reader.readAsDataURL(input.files[0]);
        }
    }
</script>
@endsection
