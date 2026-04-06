@extends('layouts.master')
@section('content')


<div class="bg0 m-t-23 p-b-140">
    <div class="container">
        <div class="flex-w flex-sb-m p-b-52" dir="rtl">
            <div class="flex-w flex-l-m filter-tope-group m-tb-10">
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5 how-active1" data-filter="*">
                    كل المنتجات
                </button>
                @foreach ($categories as $cat)
                <button class="stext-106 cl6 hov1 bor3 trans-04 m-r-32 m-tb-5" data-filter="._{{ $cat->id }}">
                    {{ $cat->name }}
                </button>
                @endforeach

            </div>



        </div>

        <div class="row isotope-grid" style="position: relative; height: 1561.1px;">
            @foreach ($products as $item)
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35 isotope-item _{{ $item->category_id }}"
                style="position: absolute; left: 0%; top: 0px;">
                <!-- Block2 -->

                <div class="block2">
                    <div class="block2-pic hov-img0">
                        <img src="{{ asset($item->imagepath) }}" alt="{{ $item->name }}">

                        <a href="/single-product/{{ $item->id }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1 p-lr-15 trans-04">
                            عرض المنتج
                        </a>
                    </div>

                    <div class="block2-txt flex-w flex-t p-t-14">
                        <div class="block2-txt-child1 flex-col-l ">
                            <a href="/single-product/{{ $item->id }}" class="stext-104 cl4 hov-cl1 trans-04 p-b-6">
                                {{ $item->name }}
                            </a>

                            <span class="stext-105 cl3">
                                {{ $item->price }} ج.م
                            </span>
                        </div>

                        <div class="block2-txt-child2 flex-r p-t-3">
                            <a href="/wishlist/add/{{ $item->id }}"
                                class="btn-addwish-b2 dis-block pos-relative js-addwish-b2" tabindex="0">
                                <img class="icon-heart1 dis-block trans-04"
                                    src="{{asset('assets/frontend/images/icons/icon-heart-01.png') }}" alt="ICON">
                                <img class="icon-heart2 dis-block trans-04 ab-t-l"
                                    src="{{asset('assets/frontend/images/icons/icon-heart-02.png') }}" alt="ICON">
                            </a>
                        </div>
                        {{-- <div style="padding:10px;">
                            <a href="/addproducttocart/{{ $item->id }}" class="btn btn-warning w-100">
                                إضافة للسلة
                            </a>
                        </div> --}}
                    </div>
                </div>

            </div>
            @endforeach







        </div>

        <!-- Load more -->
        <div class="flex-c-m flex-w w-full p-t-45">
            <a href="#" class="flex-c-m stext-101 cl5 size-103 bg2 bor1 hov-btn1 p-lr-15 trans-04">
                Load More
            </a>
        </div>
    </div>
</div>
@endsection