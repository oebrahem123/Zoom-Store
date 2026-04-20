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
                                ليس لديك حساب؟ <a href="{{ route('register') }}" class="text-primary">إنشاء حساب</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <!-- content-wrapper ends -->
    </div>
    <!-- page-body-wrapper ends -->
</div>
@endsection
