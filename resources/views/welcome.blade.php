<style>
    .star-rating {
        font-size: 0;
        background: var(--background-light);
        border: 2px solid var(--border-color);
        border-radius: 6px;
        display: flex;
        flex-direction: row-reverse;
        height: 40px;
        align-items: center;
        justify-content: flex-end;
        width: 100%;
        padding: 1px 4px 0px 6px;
    }

    .star-rating input {
        display: none;
    }

    .star-rating label {
        font-size: 30px;
        color: #ddd;
        cursor: pointer;
        transition: all 0.3s ease;
        margin: 0 0px;
        padding: 5px;
        border-radius: 50%;
    }

    .star-rating label:hover {
        color: #ffc107 !important;
        transform: scale(1.2);

    }

    .star-rating input:checked+label,
    .star-rating input:checked+label~label {
        color: #ffc107;
        text-shadow: 0 0 10px rgba(255, 193, 7, 0.5);
    }

    section.contact-us .contact-info .icon i {
        float: left;
        margin-right: 15px;
        width: 50px;
        height: 50px;
        display: inline-block;
        text-align: center;
        line-height: 50px;
        border-radius: 50%;
        background: rgb(219, 138, 222);
        background: linear-gradient(-145deg, rgb(242 129 35) 0%, rgba(246, 191, 159, 1) 100%);
        color: #fff;
    }

    section.contact-us #contact input {
        width: 100%;
        height: 40px;
        border-radius: 5px;
        background-color: #f4f7fb;
        outline: none;
        border: none;
        box-shadow: none;
        font-size: 13px;
        font-weight: 500;
        color: #7a7a7a;
        padding: 0px 15px;
        margin-bottom: 30px;
    }

    section.contact-us {
        background-image: url(../images/footer-bg.png);
        background-position: center center;
        background-repeat: no-repeat;
        background-size: cover;
        padding: 0px;
        position: relative;
    }

    #contact-section {
        padding-top: 20px;
    }

    section.contact-us #contact textarea {
        width: 100%;
        min-height: 140px;
        max-height: 180px;
        border-radius: 5px;
        background-color: #f4f7fb;
        outline: none;
        border: none;
        box-shadow: none;
        font-size: 13px;
        font-weight: 500;
        color: #7a7a7a;
        padding: 15px;
        margin-bottom: 30px;
    }

    section.contact-us #contact button {
        float: right;
        font-size: 13px;
        color: #fff;
        background: #F28123;
        border-color: #F28123;
        padding: 12px 30px;
        display: inline-block;
        border-radius: 5px;
        font-weight: 500;
        text-transform: uppercase;
        transition: all .3s;
        transition: all .3s;
        border: none;
        outline: none;
    }

    section.contact-us:after {
        position: absolute;
        content: '';
        /* background-image: url("{{ asset('assets/img/footer-bg.png') }}"); */
        left: 0px;
        top: 60px;
        width: 267px;
        height: 396px;
        z-index: 1;
    }

    .element.style {
        z-index: 3;
        position: absolute;
        height: 100%;
        width: 100%;
        padding: 0px;
        border-width: 0px;
        margin: 0px;
        left: 0px;
        top: 0px;
        touch-action: pan-x pan-y;
    }

    section.contact-us .contact-info {
        background-color: #fff;
        margin-top: -30px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.05);
        border-radius: 15px;
        z-index: 5;
        position: relative;
        padding: 20px;
    }

    section.contact-us .contact-info h4 {
        font-size: 18px;
        font-weight: 900;
        text-transform: uppercase;
    }

    section.contact-us .contact-info span {
        font-size: 15px;
        color: #f5b183;
        ;
        font-weight: 700;
    }

    section.contact-us #contact {
        margin-left: -100px;
        position: relative;
        z-index: 5;
        background-color: #fff;
        margin-top: 40px;
        box-shadow: 0px 0px 15px rgba(0, 0, 0, 0.05);
        border-radius: 15px;
        padding: 40px;
        margin-bottom: 43px;

    }

    section.contact-us .section-heading {
        text-align: right;
        margin-bottom: 40px;
    }

    .section-heading {
        position: relative;
        z-index: 2;
        padding-top: 20px;
        margin-top: 0px;
        text-align: center;
        margin-bottom: 60px;
    }

    .section-heading p {
        margin-top: 30px;
    }

    .fieldset {
        min-width: 0;
        padding: 0;
        margin: 0;
        border: 0;
    }
</style>
@extends('layouts.master')
@section('content')


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




{{-- --}}

{{-- --}}
@include('partials.product')

@include('partials.reviews')


<section class="contact-us" id="contact-section">
    <div class="container">
        <div class="row">
            <div class="col-lg-8">
                <div id="map">

                    <!-- You just need to go to Google Maps for your own map point, and copy the embed code from Share -> Embed a map section -->
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d27347.3483374904!2d31.356059290066355!3d31.04244592162872!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x14f79dcf806fe6c7%3A0x541ea3e24ccc4765!2s29V9%2BP83%2C%20El-Shaheed%20El-Tayar%20Saad%20Mohammed%20Ismail%2C%20Mansoura%20Qism%202%2C%20El%20Mansoura%2C%20Dakahlia%20Governorate%207650177!5e0!3m2!1sen!2seg!4v1762979861875!5m2!1sen!2seg"
                        width="100%" height="420px" frameborder="0"
                        style="border:0; border-radius: 15px; position: relative; z-index: 2;"
                        allowfullscreen=""></iframe>
                    <div class="row">
                        <div class="col-lg-4 offset-lg-1">
                            <div class="contact-info">
                                <div class="icon">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <h4>Phone</h4>
                                <span>010-23-196780</span>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="contact-info">
                                <div class="icon">
                                    <i class="fa fa-phone"></i>
                                </div>
                                <h4>Mobile</h4>
                                <span>090-080-0760</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                <form id="contact" style="direction: rtl;" action="/storeReview" method="post">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="section-heading" style=": rtl;">
                                <h6>أترك رسالتك</h6>
                                <h4> نحن هنا من أجلك </h4>
                                <p>هدفنا راحتك ورضاك. تواصل معنا لأي استفسار حول الطلبات ، المنتجات <br> أو الدعم الفني
                                    وسنرد عليك فورًا.</p>
                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-lg-6">
                                <input type="text" name="name" class="form-control" placeholder="اسمك الكريم"
                                    value="{{ old('name', auth()->check() ? auth()->user()->name : '') }}" required>

                            </div>
                            <div class="form-group col-lg-6">
                                <input type="email" name="email" class="form-control" placeholder="بريدك الإلكتروني"
                                    value="{{ old('email', auth()->check() ? auth()->user()->email : '') }}" required>

                            </div>
                        </div>
                        <div class="form-row">
                            <div class="form-group col-lg-6">
                                <input type="tel" style="direction: rtl;" name="phone" class="form-control"
                                    placeholder="رقم هاتفك"
                                    value="{{ old('phone', auth()->check() ? auth()->user()->phone ?? '' : '') }}">

                            </div>
                            <div class="form-group col-lg-6">
                                <div class="star-rating">
                                    <input type="radio" id="star5" name="subject" value="5-stars" {{
                                        old('subject')=='5-stars' ? 'checked' : '' }} required>
                                    <label for="star5" title="ممتاز - 5 نجوم">&#9733;</label>

                                    <input type="radio" id="star4" name="subject" value="4-stars" {{
                                        old('subject')=='4-stars' ? 'checked' : '' }}>
                                    <label for="star4" title="جيد جداً - 4 نجوم">&#9733;</label>

                                    <input type="radio" id="star3" name="subject" value="3-stars" {{
                                        old('subject')=='3-stars' ? 'checked' : '' }}>
                                    <label for="star3" title="جيد - 3 نجوم">&#9733;</label>

                                    <input type="radio" id="star2" name="subject" value="2-stars" {{
                                        old('subject')=='2-stars' ? 'checked' : '' }}>
                                    <label for="star2" title="مقبول - 2 نجوم">&#9733;</label>

                                    <input type="radio" id="star1" name="subject" value="1-star" {{
                                        old('subject')=='1-star' ? 'checked' : '' }}>
                                    <label for="star1" title="سيء - 1 نجمة">&#9733;</label>

                                </div>

                            </div>
                        </div>


                        <!-- نص الرسالة -->
                        <div class="col-lg-12">
                            <div class="form-row">

                                <textarea name="message" class="form-control" placeholder="اكتب رسالتك هنا..." rows="5"
                                    required>{{ old('message') }}</textarea>

                            </div>
                        </div>
                        <div class="col-lg-12">
                            <fieldset>
                                <button type="submit" style="font-size: 15px" id="form-submit"
                                    class="main-gradient-button"> أترك رسالتك </button>
                            </fieldset>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>


</section>

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