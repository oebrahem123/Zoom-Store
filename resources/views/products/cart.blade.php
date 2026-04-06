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
                                    <th class="column-2"></th>
                                    <th class="column-3">السعر</th>
                                    <th class="column-4">الكمية</th>
                                    <th class="column-5 text-right">الإجمالي</th>
                                </tr>

                                @forelse($cartProducts as $item)
                                <tr class="table_row">

                                    <td class="column-1">
                                        <div class="how-itemcart1 delete-item" data-id="{{ $item->id }}">

                                            <img src="{{ asset($item->product->imagepath) }}" alt="IMG">

                                            <form id="delete-{{ $item->id }}"
                                                action="{{ route('cart.delete', $item->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                            </form>

                                        </div>
                                    </td>

                                    <td class="column-2">
                                        {{ $item->product->name }}
                                    </td>

                                    <td class="column-3">
                                        {{ number_format($item->product->price, 2) }} ج.م
                                    </td>

                                    <!-- الكمية -->
                                    <td class="column-4 text-center">
                                        {{ $item->quantity }}
                                    </td>

                                    <!-- الإجمالي -->
                                    <td class="column-5">
                                        {{ number_format($item->product->price * $item->quantity, 2) }} ج.م
                                    </td>

                                </tr>

                                @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
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
                                {{ number_format($cartProducts->sum(fn($i) => $i->product->price * $i->quantity), 2) }}
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
                                مجاني
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
                                {{ number_format($cartProducts->sum(fn($i) => $i->product->price * $i->quantity), 2) }}
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