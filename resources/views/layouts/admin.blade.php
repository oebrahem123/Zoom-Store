<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="rtl">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>تسجيل الدخول | Skydash Admin</title>

  <!-- plugins:css -->
  <link rel="stylesheet" href="{{ asset('assets/admin/vendors/feather/feather.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/vendors/ti-icons/css/themify-icons.css') }}">
  <link rel="stylesheet" href="{{ asset('assets/admin/vendors/css/vendor.bundle.base.css') }}">
  <!-- inject:css -->
  <link rel="stylesheet" href="{{ asset('assets/admin/css/vertical-layout-light/style.css') }}">
  <link rel="shortcut icon" href="{{ asset('assets/admin/images/omars.png') }}" />

  <style>

    body {
      direction: rtl;
      text-align: right;
    }
    .auth-form-light {
      position: relative;
    }
    .brand-logo {
      position: absolute;
      left: 20px; /* اللوجو على الشمال */
      top: 30px;
    }
    .auth-form-light h4{
        margin-top: 45px
    }
    .auth-form-light h4,
    .auth-form-light h6 {
      text-align: right; /* الكلام على اليمين */
    }
  </style>
</head>
<body>

<div class="container-scroller">
    @yield('content')

</div>

  <!-- JS -->
  <script src="{{ asset('assets/admin/vendors/js/vendor.bundle.base.js') }}"></script>
  <script src="{{ asset('assets/admin/js/off-canvas.js') }}"></script>
  <script src="{{ asset('assets/admin/js/hoverable-collapse.js') }}"></script>
  <script src="{{ asset('assets/admin/js/template.js') }}"></script>
  <script src="{{ asset('assets/admin/js/settings.js') }}"></script>
  <script src="{{ asset('assets/admin/js/todolist.js') }}"></script>
</body>

</html>
