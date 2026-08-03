@extends('layouts.admin')
@section('content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                        <div class="brand-logo">
                            <img src="{{ asset('assets/frontend/images/logo/logo-black.png') }}" alt="logo">
                        </div>
                        <h4>تسجيل دخول المشرف </h4>
                        <h6 class="font-weight-light">سجّل الدخول للوصول إلى لوحة التحكم .</h6>

                        @error('email')
                        <div class="alert alert-danger text-right" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror

                        <form method="POST" action="{{ route('admin.login.submit') }}" class="pt-3">
                            @csrf

                            <div class="form-group">
                                <input type="email" name="email" value="{{ old('email') }}"
                                    class="form-control form-control-lg @error('email') is-invalid @enderror"
                                    placeholder=" بريد إلكتروني " required autocomplete="email" autofocus>
                            </div>

                            <div class="form-group">
                                <input type="password" name="password"
                                    class="form-control form-control-lg @error('password') is-invalid @enderror"
                                    placeholder=" كلمة المرور " required autocomplete="current-password">
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
                                <div class="form-check">
                                    <label class="form-check-label text-muted">
                                        <input type="checkbox" class="form-check-input" name="remember" {{
                                            old('remember') ? 'checked' : '' }}>
                                        أبقِني مسجّل الدخول
                                    </label>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection