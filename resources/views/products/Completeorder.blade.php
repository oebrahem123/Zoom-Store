@extends('layouts.master')
@section('content')
<style>
    .row .col-lg-4 {
        height: auto !important;
        align-self: flex-start !important;
        /* يمنع التمدد */
    }

    .row {
        align-items: flex-start !important;
    }
</style>
<div class="checkout-section mt-150 mb-150">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 p-t-50 p-b-50">
                <div class="checkout-accordion-wrap">
                    <div class="accordion" id="accordionExample">
                        <div class="card single-accordion">

                            <div class="card-header" id="headingOne" dir="rtl">
                                <h5 class="mb-0">
                                    <button class="btn btn-link" type="button" data-toggle="collapse"
                                        data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                        عنوان الشحن
                                    </button>
                                </h5>
                            </div>

                            <div id="collapseOne" class="collapse show" aria-labelledby="headingOne"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="billing-address-form">
                                        <form action="/StoreOrder" id="store-order" method="post" name="store-order">
                                            @csrf
                                            <div class="billing-address-form" dir="rtl">

                                                <input class="size-111 bor8 stext-102 cl2 black p-lr-20 m-b-15"
                                                    type="text" name="name" placeholder="الاسم" required>

                                                <input class="size-111 bor8 stext-102 cl2 black p-lr-20 m-b-15"
                                                    type="email" name="email" placeholder="البريد الإلكتروني" required>

                                                <input class="size-111 bor8 stext-102 cl2 black p-lr-20 m-b-15"
                                                    type="text" name="address" placeholder="العنوان" required>

                                                <input class="size-111 bor8 stext-102 cl2 black p-lr-20 m-b-15"
                                                    type="tel" name="phone" placeholder="رقم الهاتف" dir="rtl">

                                                <textarea class="size-110 bor8 stext-102 cl2 black p-lr-20 p-tb-10"
                                                    name="note" placeholder="ملاحظات إضافية"></textarea>

                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                        </div>
                        <div class="card single-accordion">
                            <div class="card-header" id="headingThree">
                                <h5 class="mb-0" dir="rtl">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                        data-target="#collapseThree" aria-expanded="false"
                                        aria-controls="collapseThree">
                                        تفاصيل الطلب
                                    </button>
                                </h5>
                            </div>
                            <div id="collapseThree" class="collapse" aria-labelledby="headingThree"
                                data-parent="#accordionExample">
                                <div class="card-body">
                                    <div class="cart-section" style="margin-bottom: 20px;margin-top:20px;">
                                        <div class="container">
                                            <div class="row">

                                                <div class="col-md-12 col-lg-8">
                                                    <div class="wrap-table-shopping-cart" dir="rtl">
                                                        <table class="table-shopping-cart">

                                                            <tr class="table_head">
                                                                <th class="column-1">المنتج</th>
                                                                <th class="column-6"></th>
                                                                <th class="column-3">المقاس</th>
                                                                <th class="column-4">اللون</th>
                                                                <th class="column-2">السعر</th>
                                                                <th class="column-4">الكمية</th>
                                                                <th class="column-2 text-center">الإجمالي</th>
                                                            </tr>

                                                            @forelse($cartProducts as $item)
                                                            <tr
                                                                class="table_row {{ !$item->isAvailable ? 'opacity-50' : '' }}">

                                                                <td class="column-1">
                                                                    <div class="how-itemcart1 delete-item"
                                                                        data-id="{{ $item->id }}">
                                                                        <img src="{{ asset($item->display_image) }}"
                                                                            alt="">

                                                                        <form id="delete-{{ $item->id }}"
                                                                            action="{{ route('cart.delete', $item->id) }}"
                                                                            method="POST">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                        </form>
                                                                    </div>
                                                                </td>

                                                                <td class="column-6 texx">
                                                                    @if($item->product)
                                                                    <a
                                                                        href="{{ route('product.details', $item->product->id) }}">
                                                                        <span
                                                                            class="{{ !$item->isAvailable ? 'text-decoration-line-through' : '' }}">{{
                                                                            $item->display_name }}</span>
                                                                    </a>
                                                                    @else
                                                                    <span
                                                                        class="{{ !$item->isAvailable ? 'text-decoration-line-through' : '' }}">{{
                                                                        $item->display_name }}</span>
                                                                    @endif
                                                                    @if(!$item->isAvailable)
                                                                    <span class="d-block"
                                                                        style="font-size:12px;margin-top:6px;color:{{ $item->availabilityStatus === 'out_of_stock' ? '#dc3545' : '#6c757d' }};">
                                                                        {{ $item->availabilityMessage }}
                                                                    </span>
                                                                    @endif
                                                                </td>

                                                                <td class="text-center">{{ $item->size ?? '—' }}</td>
                                                                <td class="text-center">{{ $item->color ?? '—' }}</td>

                                                                <td class="text-center">
                                                                    {{ number_format($item->display_price, 2) }} ج.م
                                                                </td>

                                                                <td class="text-center">{{ $item->quantity }}</td>

                                                                <td class="text-center">
                                                                    {{ number_format($item->display_price *
                                                                    $item->quantity, 2) }} ج.م
                                                                </td>

                                                            </tr>
                                                            @empty
                                                            <tr>
                                                                <td colspan="7" class="text-center py-5">
                                                                    سلة المشتريات فارغة حالياً
                                                                </td>
                                                            </tr>
                                                            @endforelse

                                                        </table>
                                                    </div>
                                                </div>

                                                <div class=" col-lg-4 bor10 p-t-12" dir="rtl">

                                                    <h4 class="mtext-109 black cl2 p-b-30">
                                                        ملخص الطلب
                                                    </h4>

                                                    <!-- subtotal -->
                                                    <div
                                                        class="flex-w flex-t p-lr-0 bor12 p-b-13 justify-content-between">
                                                        <span class="stext-110 black cl2">
                                                            المجموع:
                                                        </span>

                                                        <span class="mtext-110 black cl2">
                                                            {{ number_format($cartProducts->sum(fn($i) =>
                                                            $i->display_price * $i->quantity), 2) }} ج
                                                        </span>
                                                    </div>

                                                    <!-- shipping -->
                                                    <div
                                                        class="flex-w flex-t bor12 p-t-15 p-b-30 justify-content-between">
                                                        <span class="stext-110 black cl2">
                                                            الشحن:
                                                        </span>

                                                        <span class="stext-111 cl6">

                                                        </span>
                                                    </div>

                                                    <!-- total -->
                                                    <div class="flex-w flex-t p-t-27 p-b-33 justify-content-between">
                                                        <span class="mtext-101 black cl2">
                                                            الإجمالي:
                                                        </span>

                                                        <span class="mtext-110 black cl2">
                                                            {{ number_format($cartProducts->sum(fn($i) =>
                                                            $i->display_price * $i->quantity), 2) }} ج
                                                        </span>
                                                    </div>

                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card single-accordion">

                            <!-- header -->
                            <div class="card-header" id="headingFour">
                                <h5 class="mb-0" dir="rtl">
                                    <button class="btn btn-link collapsed" type="button" data-toggle="collapse"
                                        data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                        إتمام عملية الدفع
                                    </button>
                                </h5>
                            </div>

                            <!-- content -->
                            <div id="collapseFour" class="collapse" aria-labelledby="headingFour"
                                data-parent="#accordionExample">

                                <div class="card-body" dir="rtl">

                                    <p class="stext-102 cl3 p-b-20">
                                        سيتم تحويلك إلى صفحة الدفع الإلكتروني لإتمام العملية بأمان
                                    </p>

                                    <p class="stext-102 cl3 p-b-10">
                                        الدفع يتم عبر بوابة آمنة
                                    </p>

                                    <p class="stext-111 cl6">
                                        🔒 جميع بياناتك مشفرة وآمنة 100%
                                    </p>

                                    <div class="p-t-18">
                                        <button type="submit" form="store-order" class="zoom-btn" dir="ltr">
                                            <span class="icon">→</span>
                                            <span class="btn-text"> الدفع الآمن </span>
                                            <span class="hover-bg"></span>
                                        </button>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>


            {{-- <div class="col-lg-12 mt-2">
                <div class="cart-buttons">
                    <a onclick="event.preventDefault(); document.getElementById('store-order').submit();"
                        class="boxed-btn">Place Order</a>
                </div>
            </div> --}}
        </div>
    </div>
</div>


@endsection