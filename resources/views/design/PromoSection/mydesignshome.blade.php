@auth
@if($designs->count() > 0)
<section class="bg0 p-t-6 p-b-0">
    <div class="container my-5" dir="rtl">

        <div class="row align-items-center position-relative mb-4">
            <div class="col-md-4 text-start d-none d-md-block">
                <a href="{{ route('designs.my') }}"
                    class="custom-btn text-decoration-none d-inline-flex align-items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        class="bi bi-arrow-right transition-arrow" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M1 8a.5.5 0 0 1 .5-.5h11.793l-3.147-3.146a.5.5 0 0 1 .708-.708l4 4a.5.5 0 0 1 0 .708l-4 4a.5.5 0 0 1-.708-.708L13.293 8.5H1.5A.5.5 0 0 1 1 8" />
                    </svg>
                    <span>عرض جميع التصاميم</span>
                </a>
            </div>

            <div class="text-center p-b-40">
                <h2 class="section-title p-b-7"> تصاميمي الأخيرة </h2>
                <p> استعرض تصاميمك السابقة أو تابع التصميم من حيث توقفت </p>
            </div>

            <div class="col-md-4 d-none d-md-block"></div>
        </div>

        <div class="row">
            @foreach($designs->take(4) as $design)
            @php
            $preview = $design->preview_image ? asset($design->preview_image) :
            asset('assets/frontend/images/placeholder-design.png');
            $isOrdered = \App\Models\orderdetails::where('design_id', $design->id)->exists();
            @endphp
            <div class="col-sm-6 col-md-4 col-lg-3 p-b-35">

                <div class="block2" dir="rtl">
                    <div class="block2-pic hov-img0">
                        <img src="{{ $preview }}" alt="{{ $design->product?->name }}">

                        <a href="{{ route('design.edit', $design->id) }}"
                            class="block2-btn flex-c-m stext-103 cl2 size-102 bg0 bor2 hov-btn1">
                            تعديل المنتج
                        </a>
                    </div>

                    <div class="block2-txt p-t-14">

                        <a href="{{ route('design.edit',$design->id) }}"
                            class="stext-104 cl4 hov-cl1 trans-04 js-name-b2 p-b-6">

                            {{ $design->product?->name ?? 'تصميم' }}

                        </a>

                        <span class="stext-105 cl3">
                            {{ $design->created_at->diffForHumans() }}
                        </span>

                        <div class="m-t-5">

                            <span style="font-size:11px;padding:2px 8px;border-radius:10px;
            {{ $isOrdered ? 'background:#e8f5e9;color:#2e7d32;' : 'background:#e3f2fd;color:#1565c0;' }}">

                                {{ $isOrdered ? 'تم طلبه' : '✔ محفوظ' }}

                            </span>

                        </div>

                    </div>
                </div>

            </div>
            @endforeach
        </div>

        {{-- تغيير الشرط من >= 6 إلى > 4 --}}
        @if($designs->count() > 4)

        <div class="row d-block d-md-none text-center m-t-20">
            <div class="col-12">
                <a href="{{ route('designs.my') }}"
                    class="custom-btn text-decoration-none d-inline-flex align-items-center gap-2 justify-content-center p-3 border rounded">
                    <span>عرض جميع التصاميم</span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="currentColor"
                        class="bi bi-arrow-left transition-arrow" viewBox="0 0 16 16">
                        <path fill-rule="evenodd"
                            d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8" />
                    </svg>
                </a>
            </div>
        </div>
        @endif


    </div>
</section>
@endif
@endauth