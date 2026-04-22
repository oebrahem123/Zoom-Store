<div class="container my-5">
    <div class="row g-3 align-items-stretch">

        {{-- LEFT BIG CATEGORY --}}
        @if($categories->count() > 0)
        @php $main = $categories[0]; @endphp

        <div class="col-lg-6 d-flex" dir="rtl">
            <a href="{{ route('prods', $main->id) }}"
                class="product-card main-card p-5 d-flex flex-column justify-content-between w-100 text-decoration-none">

                <div>
                    <h2>{{ $main->name }}</h2>
                    <p class="mt-3 text-muted">
                        {{ $main->description ?? 'تسوق الآن' }}
                    </p>
                </div>

                <div class="text-center">
                    <img src="{{ asset($main->imagepath) }}" class="main-img">
                </div>

                <span class="shop-now">▶ ابدأ التسوق</span>

            </a>
        </div>
        @endif

        {{-- RIGHT SIDE --}}
        <div class="col-lg-6 d-flex flex-column" dir="rtl">

            {{-- TOP CATEGORY --}}
            @if($categories->count() > 1)
            @php
            $top = $categories->where('name', 'كابات Zoom')->first();
            @endphp

            <div class="mb-3" style="flex:1.3;">
                <a href="{{ route('prods', $top->id) }}"
                    class="product-card smart-watch-card p-4 d-flex justify-content-between align-items-center h-100 text-decoration-none">

                    <div>
                        <h3>{{ $top->name }}</h3>
                        <p class="mt-2">{{ $top->description ?? '' }}</p>
                    </div>


                    <img src="{{ asset($top->imagepath) }}" class="side-img">

                </a>
            </div>
            @endif

            {{-- BOTTOM TWO --}}
            <div class="row g-3 flex-fill" dir="rtl">

                @foreach($categories->slice(2,2) as $item)
                <div class="col-md-6 d-flex">
                    <a href="{{ route('prods', $item->id) }}"
                        class="product-card sub-card p-4 d-flex flex-column justify-content-between w-100 text-decoration-none">

                        <div>
                            <span class="price-red">تسوق الآن</span>
                        </div>

                        <img src="{{ asset($item->imagepath) }}" class="mini-img my-3">

                        <p class="product-name">{{ $item->name }}</p>

                    </a>
                </div>
                @endforeach

            </div>

        </div>

    </div>
</div>

@if($categories->count() > 4)

<div class="text-center my-4">
    <button id="showMoreBtn" class="btn btn-outline-dark">
        المزيد من الأقسام ↓
    </button>
</div>

<div id="moreCategories" class="container" style="display:none;">
    <div class="row g-3">

        @foreach($categories->slice(4) as $item)
        <div class="col-md-3">
            <a href="{{ route('prods', $item->id) }}" class="product-card p-3 text-center d-block text-decoration-none">

                <img src="{{ asset($item->imagepath) }}" class="mini-img mb-2">

                <p class="product-name">{{ $item->name }}</p>
            </a>
        </div>
        @endforeach

    </div>
</div>

@endif

{{-- ************************** --}}
<div class="container my-5">
    <div class="row g-3 align-items-stretch">

        <!-- LEFT BIG CARD -->
        <div class="col-lg-6 d-flex">
            <div class="product-card main-card p-5 d-flex flex-column justify-content-between w-100">

                <div>
                    <h2>Bold Chronograph</h2>
                    <h2>Rose Dial Ladies</h2>
                    <h2>Watch</h2>

                    <p class="price-green mt-4">$450.00</p>
                </div>

                <div class="text-center">
                    <img src="{{ asset('assets/frontend/images/main-watch.jpg') }}" class="main-img">
                </div>

                <a href="#" class="shop-now">▶ SHOP NOW</a>

            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="col-lg-6 d-flex flex-column">

            <!-- TOP ORANGE -->
            <div class="mb-3" style="flex: 1.3;">
                <div class="product-card smart-watch-card p-4 d-flex justify-content-between align-items-center h-100">

                    <div>
                        <h3>Red Motion</h3>
                        <h3>Stainless Steel</h3>
                        <h3>Smartwatch</h3>

                        <p class="price-white mt-3">$99.99</p>
                    </div>

                    <div class="play-btn">&#9655;</div>

                    <img src="{{ asset('assets/frontend/images/banner/G.png') }}" class="side-img">

                </div>
            </div>

            <!-- BOTTOM -->
            <div class="row g-3 flex-fill">

                <div class="col-md-6 d-flex">
                    <div class="product-card sub-card p-4 d-flex flex-column justify-content-between w-100">

                        <div>
                            <span class="price-red">$19.90</span>
                        </div>

                        <img src="https://via.placeholder.com/150" class="mini-img my-3">

                        <p class="product-name">Leather Shoulder Bag</p>

                    </div>
                </div>

                <div class="col-md-6 d-flex">
                    <div class="product-card sub-card p-4 d-flex flex-column justify-content-between w-100">

                        <div>
                            <span class="price-red">$199.00</span>
                        </div>

                        <img src="https://via.placeholder.com/150" class="mini-img my-3">

                        <p class="product-name">Adidas Red Sneaker</p>

                    </div>
                </div>

            </div>

        </div>

    </div>
</div>
{{-- ***************************** --}}





{{-- الجذء القديم الى فى صفحه الهوم للأقسام --}}

<div class="sec-banner bg0">
    <div class="flex-w flex-c-m">
        @foreach ($categories->take(3) as $item)
        <div class="size-202 m-lr-auto respon4" dir="rtl">
            <!-- Block1 -->
            <div class="block1 wrap-pic-w om-o">
                <img class="im" src="{{ asset($item->imagepath) }}" alt="{{ $item->name }}">
                <a href="{{ route('prods', $item->id) }}"
                    class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                    <div class="block1-txt-child1 flex-col-l">
                        <span class="block1-name ltext-102 trans-04 p-b-8">
                            {{ $item->name }}
                        </span>
                        <span class="block1-info stext-102 trans-04">
                            {{ $item->description ?? 'تسوق الآن' }}
                        </span>
                    </div>
                    <div class="block1-txt-child2 p-b-4 trans-05">
                        <div class="block1-link stext-101 cl0 trans-09">
                            ابدأ التسوق
                        </div>
                    </div>
                </a>
            </div>
        </div>
        @endforeach
    </div>


    <div class="flex-c-m m-t-30 m-b-30">
        <button id="showMoreCategoriesBtn" class="btn-showmore">
            المزيد من الأقسام
            <i class="fa fa-chevron-right m-l-5"></i>
        </button>
    </div>


    <div id="moreCategories" style="display: none;">
        <div class="flex-w flex-c-m m-t-30">
            @foreach ($categories->skip(3) as $item)
            <div class="size-202 m-lr-auto respon4" dir="rtl">

                <div class="block1 wrap-pic-w om-o">
                    <img class="im" src="{{ asset($item->imagepath) }}" alt="{{ $item->name }}">
                    <a href="{{ route('prods', $item->id) }}"
                        class="block1-txt ab-t-l s-full flex-col-l-sb p-lr-38 p-tb-34 trans-03 respon3">
                        <div class="block1-txt-child1 flex-col-l">
                            <span class="block1-name ltext-102 trans-04 p-b-8">
                                {{ $item->name }}
                            </span>
                            <span class="block1-info stext-102 trans-04">
                                {{ $item->description ?? 'تسوق الآن' }}
                            </span>
                        </div>
                        <div class="block1-txt-child2 p-b-4 trans-05">
                            <div class="block1-link stext-101 cl0 trans-09">
                                ابدأ التسوق
                            </div>
                        </div>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
    </div>
</div>



{{-- cart --}}
@extends('layouts.master')

@section('content')


<form class="bg0 p-t-75 p-b-85">
    <div class="container">
        <div class="row">

            <!-- المنتجات -->
            <div class="col-lg-10 col-xl-7 m-lr-auto m-b-50">
                <div class="m-l-25 m-r--38 m-lr-0-xl">

                    <div class="wrap-table-shopping-cart" dir="rtl">
                        <table class="table-shopping-cart">
                            <tbody>

                                <tr class="table_head">
                                    <th class="column-1">المنتج</th>
                                    <th class="column-6"></th>
                                    <th class="column-3"> المقاس </th>
                                    <th class="column-4"> اللون </th>
                                    <th class="column-2">السعر</th>
                                    <th class="column-4">الكمية</th>
                                    <th class="column-2 text-center">الإجمالي</th>
                                </tr>

                                @forelse($cartProducts as $item)
                                <tr class="table_row {{ !$item->isAvailable ? 'opacity-50 bg-light' : '' }}">

                                    <td class="column-1">
                                        <div class="how-itemcart1 delete-item" data-id="{{ $item->id }}">

                                            <img src="{{ asset($item->display_image) }}" alt="">

                                            <form id="delete-{{ $item->id }}"
                                                action="{{ route('cart.delete', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                        </div>
                                    </td>

                                    <td class="column-6 texx">
                                        <span class="{{ !$item->isAvailable ? 'text-decoration-line-through' : '' }}"
                                            style="display:inline-block;">
                                            {{ $item->display_name }}
                                        </span>
                                        @if(!$item->isAvailable)
                                        <span class="d-block"
                                            style="font-size:12px;margin-top:6px;color:{{ $item->availabilityStatus === 'out_of_stock' ? '#dc3545' : '#6c757d' }};">
                                            {{ $item->availabilityMessage }}
                                        </span>
                                        @endif
                                    </td>
                                    <td class="column-3 text-center">{{ $item->size ?? '—' }}</td>
                                    <td class="column-4 text-center">{{ $item->color ?? '—' }}</td>

                                    <td class="column-2 text-center">
                                        {{ number_format($item->display_price, 2) }} ج.م
                                    </td>

                                    <!-- الكمية -->
                                    <td class="column-4 text-center">
                                        {{ $item->quantity }}
                                    </td>

                                    <!-- الإجمالي -->
                                    <td class="column-2 text-center">
                                        {{ number_format($item->display_price * $item->quantity, 2) }} ج.م
                                    </td>

                                </tr>

                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5">
                                        سلة المشتريات فارغة حالياً
                                    </td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>



                </div>
            </div>

            <!-- summary -->
            <div class="col-sm-10 col-lg-7 col-xl-5 m-lr-auto m-b-50">

                <div class="bor10 p-lr-40 p-t-30 p-b-40 m-l-63 m-r-40 m-lr-0-xl p-lr-15-sm" dir="rtl">

                    <h4 class="mtext-109 black cl2 p-b-30">
                        ملخص الطلب
                    </h4>

                    <!-- subtotal -->
                    <div class="flex-w flex-t bor12 p-b-13">
                        <div class="size-208">
                            <span class="stext-110 black cl2">
                                المجموع:
                            </span>
                        </div>

                        <div class="size-209">
                            <span class="mtext-110 black cl2">
                                {{ number_format($cartProducts->sum(fn($i) => $i->display_price * $i->quantity), 2) }}
                                ج
                            </span>
                        </div>
                    </div>

                    <!-- shipping -->
                    <div class="flex-w flex-t bor12 p-t-15 p-b-30">
                        <div class="size-208">
                            <span class="stext-110 black cl2">
                                الشحن:
                            </span>
                        </div>

                        <div class="size-209">
                            <span class="stext-111 cl6">
                                يتم احتساب تكلفة الشحن عند إتمام الطلب
                            </span>
                        </div>
                    </div>

                    <!-- total -->
                    <div class="flex-w flex-t p-t-27 p-b-33">
                        <div class="size-208">
                            <span class="mtext-101 black cl2">
                                الإجمالي:
                            </span>
                        </div>

                        <div class="size-209 p-t-1">
                            <span class="mtext-110 black cl2">
                                {{ number_format($cartProducts->sum(fn($i) => $i->display_price * $i->quantity), 2) }}
                                ج
                            </span>
                        </div>
                    </div>
                    <div class="p-t-18"
                        style=" align-items: center; justify-content: center; position: relative; display: flex;">
                        <a href=" {{ route('Completeorder') }}" class="zoom-btn" dir="ltr">
                            <span class="icon">→</span>
                            <span class="btn-text"> إتمام الطلب </span>
                            <span class="hover-bg"></span>
                        </a>
                    </div>
                    <!-- زر -->
                    {{-- <a href="{{ route('Completeorder') }}"
                        class="flex-c-m stext-101 cl0 size-116 bg3 bor14 hov-btn3 p-lr-15 trans-04">
                        إتمام الطلب
                    </a> --}}

                </div>

            </div>

        </div>
    </div>
</form>
<script>
    document.querySelectorAll('.delete-item').forEach(item => {

item.addEventListener('click', function () {

let id = this.getAttribute('data-id');

swal({
title: "هل أنت متأكد؟",
text: "سيتم حذف المنتج من السلة!",
icon: "warning",
buttons: ["إلغاء", "نعم، احذف"],
dangerMode: true,
})
.then((willDelete) => {

if (willDelete) {

// تنفيذ الحذف
document.getElementById('delete-' + id).submit();

} else {

swal("تم الإلغاء 👍");

}

});

});

});
</script>
@endsection
