@extends('layouts.master')
@section('content')

<div class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="text-center p-b-40">
            <h2 class="section-title">المفضلة</h2>
            <p>المنتجات التي أضفتها إلى مفضلتك</p>
        </div>

        @if($wishlistProducts->isEmpty())
        <div class="text-center py-5">
            <i class="zmdi zmdi-favorite-outline" style="font-size: 64px; color: #ccc;"></i>
            <p class="mt-3" style="font-size: 18px; color: #888;">المفضلة فارغة حالياً</p>
            <a href="{{ route('prods') }}" class="zoom-btn mt-3" dir="ltr" style="display: inline-block;">
                <span class="btn-text">تسوق الآن</span>
            </a>
        </div>
        @else
        <div class="row">
            @foreach($wishlistProducts as $product)
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35">
                <div class="block2">
                    <div class="block2-pic hov-img0">
                        <img src="{{ asset($product->imagepath) }}" alt="{{ $product->name }}">
                        <a href="{{ route('product.details', $product->id) }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                            عرض المنتج
                        </a>
                    </div>
                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l ">
                            <a href="{{ route('product.details', $product->id) }}"
                                class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">
                                {{ $product->name }}
                            </a>
                            <span class="stext-105 cl3">
                                {{ $product->price }} ج.م
                            </span>
                        </div>
                        <div class="block2-txt-child2 flex-r p-t-3">
                            <a href="#" class="btn-addwish-b2 dis-block pos-relative js-addwish-b2 js-addedwish-b2"
                                data-product-id="{{ $product->id }}" tabindex="0">
                                <img class="icon-heart1 dis-block trans-04"
                                    src="{{ asset('assets/frontend/images/icons/icon-heart-01.png') }}" alt="ICON">
                                <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                    src="{{ asset('assets/frontend/images/icons/icon-heart-02.png') }}" alt="ICON">
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

{{-- <style>
    .section-title {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 8px;
    }

    .zoom-btn {
        display: inline-flex;
        align-items: center;
        padding: 12px 30px;
        background: var(--secondary-color, #ff6e26);
        color: #fff;
        border-radius: 25px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s;
    }

    .zoom-btn:hover {
        transform: scale(1.05);
        color: #fff;
    }
</style> --}}
@endsection
