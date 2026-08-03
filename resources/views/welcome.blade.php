@extends('layouts.master')
@section('content')
{{-- عرض صفحه التصميمات --}}
@include('design.promosection.promo-section')

{{-- عرض اقسام الموقع --}}
<!-- Banner -->
<div class="sec-banner bg0 p-t-50">
    <div class="containe">

        <!-- العنوان -->
        <div class="container text-center p-b-30">
            <h2 class="section-title">
                أقسام الموقع
            </h2>
            <p>متعة التسوق عبر فرعنا</p>
        </div>

        @php
        $homeCategories = $categories->take(4);

        $main = $homeCategories->get(0);
        $top = $homeCategories->get(3);
        $bottom1 = $homeCategories->get(2);
        $bottom2 = $homeCategories->get(1);
        @endphp

        <div class="container my-5 " dir="rtl">
            <div class="row g-3 align-items-stretch">

                {{-- LEFT BIG --}}
                @if($main)
                <div class="col-lg-6">
                    <div class="block1 wrap-pic-w h-100">

                        <img src="{{ asset($main->imagepath) }}" class="w-100 h-100 object-fit-cover">

                        <a href="{{ route('prods', $main->id) }}" class="block1-txt ab-t-l s-full flex-col-l-sb p-5">

                            <div class="block1-txt-child1 d-flex flex-column">
                                <span class="block1-name ltext-102 p-b-8">
                                    {{ $main->name }}
                                </span>

                                <span class="block1-info stext-102">
                                    {{ $main->description }}
                                </span>
                            </div>

                            <div class="block1-link stext-101">
                                ابدأ التسوق
                            </div>

                        </a>
                    </div>
                </div>
                @endif


                {{-- RIGHT --}}
                <div class="col-lg-6 d-flex flex-column">

                    {{-- TOP --}}
                    @if($top)
                    <div class="mb-3" style="flex:1.3;">
                        <div class="block1 wrap-pic-w h-100">

                            <img src="{{ asset($top->imagepath) }}" class="w-100 h-100 object-fit-cover">

                            <a href="{{ route('prods', $top->id) }}" class="block1-txt ab-t-l s-full flex-col-l-sb p-4">

                                <div class="block1-txt-child1 d-flex flex-column">
                                    <span class="block1-name ltext-102 p-b-8">
                                        {{ $top->name }}
                                    </span>

                                    <span class="block1-info stext-102 white white">
                                        {{ $top->description}}
                                    </span>
                                </div>

                                <div class="block1-link stext-101">
                                    ابدأ التسوق
                                </div>

                            </a>

                        </div>
                    </div>
                    @endif


                    {{-- BOTTOM --}}
                    <div class="row g-3 flex-fill">

                        @foreach([$bottom1, $bottom2] as $item)
                        @if($item)
                        <div class="col-md-6">
                            <div class="block1 wrap-pic-w h-100">

                                <img src="{{ asset($item->imagepath) }}" class="w-100 h-100 object-fit-cover">

                                <a href="{{ route('prods', $item->id) }}"
                                    class="block1-txt ab-t-l s-full flex-col-l-sb p-3">

                                    <div>
                                        <span class="block1-name stext-102">
                                            {{ $item->name }}
                                        </span>
                                        <br>
                                        <span class="block1-info stext-102">
                                            {{ $item->description }}
                                        </span>
                                    </div>


                                    <div class="block1-link stext-101">
                                        تسوق الآن
                                    </div>

                                </a>

                            </div>
                        </div>
                        @endif
                        @endforeach

                    </div>

                </div>

            </div>
        </div>
    </div>
</div>








{{-- أحدث المنتجات المضافة --}}
@include('partials.product')

{{-- تصميمات الخاصه بالعاميل --}}
@include('design.promosection.mydesignshome')

{{-- المنتجات الأكثر مبيعًا --}}
@include('products.Best-selling.Best-selling')


{{-- اراء العملاء --}}
@include('partials.reviews')


{{-- اتصل بنا --}}
@include('layouts.partials.contact-us')


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const showMoreBtn = document.getElementById('showMoreCategoriesBtn');
        const moreCategories = document.getElementById('moreCategories');

        if (showMoreBtn && moreCategories) {
            showMoreBtn.addEventListener('click', function() {
                if (moreCategories.style.display === 'none') {
                    moreCategories.style.display = 'block';
                    showMoreBtn.innerHTML = 'إظهار أقل <i class="fa fa-chevron-up m-l-5"></i>';
                } else {
                    moreCategories.style.display = 'none';
                    showMoreBtn.innerHTML = 'المزيد من الأقسام <i class="fa fa-chevron-down m-l-5"></i>';
                }
            });
        }
    });
</script>
@endsection
