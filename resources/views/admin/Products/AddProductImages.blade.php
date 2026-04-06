@extends('admin.layout')

@section('content')

<div class="container mt-5">

    {{-- ✅ عرض الصورة الأصلية للمنتج --}}
    <div class="text-center mb-4">
        <h4 class="mb-3">المنتج: {{ $product->name }}</h4>

        @if ($product->imagepath && file_exists(public_path($product->imagepath)))
            <img src="{{ asset($product->imagepath) }}" alt="صورة المنتج"
                 style="width:200px; height:200px; border-radius:15px; object-fit:cover; box-shadow:0 0 10px #ccc;">
        @else
            <div style="width:200px; height:200px; border-radius:15px; background:#f0f0f0; display:flex; align-items:center; justify-content:center; margin:auto; color:#999;">
                لا توجد صورة
            </div>
        @endif
    </div>


{{-- رفع الصورة --}}
   <div class="d-flex flex-wrap justify-content-center">
    <form action="{{ route('storeProductImage') }}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="row mt-3 mb-3">
            <input type="hidden" name="product_id" id="product_id" value="{{ $product->id }}">

            <div class="col-8 pt-3">
                <input type="file" style="border-radius:50px" class="form-control @error('photo') is-invalid @enderror"
                       name="photo" id="photo" accept="image/*">
                @error('photo')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="col-4 mt-3 mb-3">
                <button type="submit" style="border-radius:50px" class="btn btn-primary w-150">رفع الصورة</button>
            </div>
        </div>
    </form>
</div>

















    <div class="d-flex flex-wrap justify-content-center mb-4 mt-4">
    @foreach ($productImages as $item)
        <div class="card shadow-sm border-0 m-2" style="width: 250px;">
            <img src="{{ asset($item->imagepath) }}" class="card-img-top rounded" style="height: 250px; object-fit: cover;">
            <form action="{{ route('removeproductphoto', $item->id) }}" method="POST" class="p-2">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm w-100"
                        onclick="return confirm('هل أنت متأكد من حذف هذه الصورة؟');">
                    <i class="fas fa-trash"></i> حذف
                </button>

            </form>
        </div>
    @endforeach
</div>




















































    <!--start js code uploud photo--->



    <!--End js code uploud photo--->


    <!--End js code-->
@endsection
