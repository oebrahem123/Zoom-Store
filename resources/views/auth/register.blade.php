@extends('layouts.admin')
<style>
    .terms-container {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
    }

    .terms-text {
        color: #737F8B;
        order: 1;
    }

    .terms-checkbox {
        order: 2;
        margin: 0;
    }

    .text-muted {
        padding-right: 30px;
    }

    .mb-4,
    .my-4 {
        margin-bottom: 1.5rem !important;
        align-items: start;
        display: flex;
    }

    .custom-checkbox {
        display: inline-flex;
        align-items: center;
        cursor: pointer;
    }

    .custom-checkbox input {
        display: none;
    }

    .custom-checkbox .checkmark {
        width: 20px;
        height: 20px;
        border: 2px solid #f28123;
        border-radius: 4px;
        background: #eee;
        position: relative;
        transition: 0.2s;
    }

    .custom-checkbox input:checked+.checkmark {
        background: #f28123;
    }

    .custom-checkbox .checkmark::after {
        content: "";
        position: absolute;
        left: 5px;
        top: 1px;
        width: 6px;
        height: 12px;
        border: solid white;
        border-width: 0 2px 2px 0;
        transform: rotate(45deg);
        opacity: 0;
        transition: 0.2s;
    }

    .custom-checkbox input:checked+.checkmark::after {
        opacity: 1;
    }

    .custom-checkbox .m2 {
        margin-right: 10px;
        color: #333;
        font-size: 15px;
    }
</style>

@section('content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                        <div class="brand-logo text-center">
                            <img src="{{ asset('assets/frontend/images/logo/logo-black.png') }}" alt="logo">
                        </div>
                        <h4 class="text-right">إنشاء حساب جديد </h4>
                        <h6 class="font-weight-light text-right">التسجيل سهل. يستغرق بضع خطوات فقط</h6>

                        <form method="POST" action="{{ route('register') }}" class="pt-3">
                            @csrf
                            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">

                            <div class="form-group">
                                <input type="text"
                                    class="form-control form-control-lg @error('name') is-invalid @enderror" id="name"
                                    name="name" value="{{ old('name') }}" required autocomplete="name" autofocus
                                    placeholder="الاسم الكامل">
                                @error('name')
                                <span class="invalid-feedback text-right" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required autocomplete="email"
                                    placeholder="البريد الإلكتروني">
                                @error('email')
                                <span class="invalid-feedback text-right" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                    id="password" name="password" required autocomplete="new-password"
                                    placeholder="كلمة المرور">
                                @error('password')
                                <span class="invalid-feedback text-right" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="form-group">
                                <input type="password" class="form-control form-control-lg" id="password-confirm"
                                    name="password_confirmation" required autocomplete="new-password"
                                    placeholder="تأكيد كلمة المرور">
                            </div>

                            <div class="mb-4">
                                <label class="custom-checkbox">
                                    <input type="checkbox">
                                    <span class="checkmark"></span>
                                    <span class="m2"> أوافق على الشروط والأحكام</span>
                                </label>
                            </div>

                            <div class="mt-3">
                                <button type="submit"
                                    class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                    إنشاء حساب
                                </button>
                            </div>

                            <div class="text-center mt-4 font-weight-light">
                                لديك حساب بالفعل؟ <a
                                    href="{{ route('login', ['redirect_to' => request('redirect_to')]) }}"
                                    class="text-primary">تسجيل الدخول</a>
                            </div>
                        </form>

                        <div class="mt-4">

                            <div class="text-center mb-3">
                                <h6 class="text-muted font-weight-bold">أو سجل الدخول باستخدام</h6>
                            </div>

                            <div class="row">

                                <div class="col-6 pr-1">

                                    <a href="{{ route('social.redirect', 'google') }}?redirect_to={{ request('redirect_to') }}"
                                        class="btn btn-light btn-block border d-flex align-items-center justify-content-center py-2">

                                        <img src="{{ asset('assets/frontend/images/icons/google1.png') }}" width="22"
                                            class="ml-2" alt="Google">

                                        <span class="font-weight-bold text-dark">Google</span>

                                    </a>

                                </div>

                                <div class="col-6 pl-1">

                                    <a href="{{ route('social.redirect', 'facebook') }}?redirect_to={{ request('redirect_to') }}"
                                        class="btn btn-primary btn-block d-flex align-items-center justify-content-center py-2">

                                        <img src="{{ asset('assets/frontend/images/icons/facebook.png') }}" width="22"
                                            class="ml-2" alt="Facebook">

                                        <span class="font-weight-bold">Facebook</span>

                                    </a>

                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection