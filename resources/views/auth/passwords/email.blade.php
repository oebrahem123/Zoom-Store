@extends('layouts.admin')

@section('content')
<div class="container-scroller">
    <div class="container-fluid page-body-wrapper full-page-wrapper">
        <div class="content-wrapper d-flex align-items-center auth px-0">
            <div class="row w-100 mx-0">
                <div class="col-lg-4 mx-auto">
                    <div class="auth-form-light text-left py-5 px-4 px-sm-5">
                        <div class="brand-logo text-center">
                            <img src="{{ asset('assets/admin/images/logo1.png') }}" alt="logo">
                        </div>
                        <h4 class="text-right">استعادة كلمة المرور</h4>
                        <h6 class="font-weight-light text-right">أدخل بريدك الإلكتروني لإرسال رابط الاستعادة</h6>

                        <form method="POST" action="{{ route('password.email') }}" class="pt-3">
                            @csrf

                            @if (session('status'))
                                <div class="alert alert-success text-right" role="alert">
                                    {{ session('status') }}
                                </div>
                            @endif

                            <div class="form-group">
                                <input type="email" class="form-control form-control-lg @error('email') is-invalid @enderror"
                                       id="email" name="email" value="{{ old('email') }}"
                                       required autocomplete="email" autofocus
                                       placeholder="البريد الإلكتروني">
                                @error('email')
                                    <span class="invalid-feedback text-right" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>

                            <div class="mt-3">
                                <button type="submit" class="btn btn-block btn-primary btn-lg font-weight-medium auth-form-btn">
                                    إرسال رابط استعادة كلمة المرور
                                </button>
                            </div>

                            <div class="text-center mt-4 font-weight-light">
                                <a href="{{ route('login') }}" class="text-primary">العودة لتسجيل الدخول</a>
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
