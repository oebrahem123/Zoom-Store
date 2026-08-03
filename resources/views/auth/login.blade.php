@extends('layouts.admin')
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
                        <h4 class="text-right">!مرحباً بك</h4>
                        <h6 class="font-weight-light text-right">سجل الدخول للمتابعة</h6>

                        <form method="POST" action="{{ route('login') }}" class="pt-3">
                            @csrf
                            @if (request('redirect_to'))
                            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
                            @endif

                            <div class="form-group">
                                <input type="email"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror" id="email"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus
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
                                    id="password" name="password" required autocomplete="current-password"
                                    placeholder="كلمة المرور">
                                @error('password')
                                <span class="invalid-feedback text-right" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <button type="submit"
                                    class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                    تسجيل الدخول
                                </button>
                            </div>

                            <div class="my-2 d-flex justify-content-between align-items-center">
                                @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="auth-link text-black">
                                    نسيت كلمة المرور؟
                                </a>
                                @endif
                                <div class="form-check ">
                                    <label class="form-check-label text-muted">
                                        <input type="checkbox" class="form-check-input" name="remember" id="remember" {{
                                            old('remember') ? 'checked' : '' }}>
                                        تذكرني
                                    </label>
                                </div>
                            </div>

                            <div class="text-center mt-4 font-weight-light">
                                ليس لديك حساب؟ <a
                                    href="{{ route('register', ['redirect_to' => request('redirect_to')]) }}"
                                    class="text-primary">إنشاء حساب</a>
                            </div>
                        </form>

                        <div class="mt-4">

                            <div class="text-center mb-3">
                                <h6 class="text-muted font-weight-bold">أو سجل الدخول باستخدام</h6>
                            </div>

                            <div class="row">

                                <div class="col-6 pr-1">

                                    <a href="{{ route('social.redirect', ['provider' => 'google', 'redirect_to' => request('redirect_to')]) }}"
                                        class="btn btn-light btn-block border d-flex align-items-center justify-content-center py-2">

                                        <img src="{{ asset('assets/frontend/images/icons/google1.png') }}" width="22"
                                            class="ml-2" alt="Google">

                                        <span class="font-weight-bold text-dark">Google</span>

                                    </a>

                                </div>

                                <div class="col-6 pl-1">

                                    <a href="{{ route('social.redirect', ['provider' => 'facebook', 'redirect_to' => request('redirect_to')]) }}"
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