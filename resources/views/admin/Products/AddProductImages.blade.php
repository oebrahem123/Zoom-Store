@extends('admin.layout')

@section('content')

<div class="container mt-5">

    {{-- عرض المنتج --}}
    <div class="text-center mb-4">
        <h4 class="mb-3">المنتج: {{ $product->name }}</h4>

        @if ($product->imagepath && file_exists(public_path($product->imagepath)))
        <img src="{{ asset($product->imagepath) }}"
            style="width:200px;height:200px;border-radius:15px;object-fit:cover;">
        @endif
    </div>

    {{-- 🔥 الفورم الصحيح --}}
    <div class="d-flex justify-content-center">
        <form action="{{ route('storeProductImage') }}" method="POST" enctype="multipart/form-data" class="w-50">
            @csrf

            <input type="hidden" name="product_id" value="{{ $product->id }}">

            {{-- اختيار اللون --}}
            <div class="mb-3">
                <label>اختار اللون</label>
                <select name="color" class="form-control">
                    <option value="">بدون لون (صورة أساسية)</option>

                    @foreach($product->variants->pluck('color')->filter()->unique() as $color)
                    <option value="{{ strtolower(trim($color)) }}">
                        {{ $color }}
                    </option>
                    @endforeach
                </select>
            </div>

            {{-- رفع الصورة --}}
            <div class="mb-3">
                <input type="file" name="photo" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary w-100">
                رفع الصورة
            </button>

        </form>
    </div>

    {{-- عرض الصور --}}
    <div class="d-flex flex-wrap justify-content-center mt-4">
        @foreach ($productImages as $item)
        <div class="card m-2" style="width: 220px;">
            <img src="{{ asset($item->imagepath) }}" style="height:200px;object-fit:cover;">

            <div class="text-center p-2">
                <small>
                    اللون:
                    {{ $item->color ?? 'default' }}
                </small>
            </div>

            <form action="{{ route('removeproductphoto', $item->id) }}" method="POST">
                @csrf
                @method('DELETE')

                <button class="btn btn-danger btn-sm w-100">
                    حذف
                </button>
            </form>
        </div>
        @endforeach
    </div>

</div>

@endsection