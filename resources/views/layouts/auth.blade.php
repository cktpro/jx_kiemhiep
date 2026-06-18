<!DOCTYPE html>
{{--
    Layout cho nhóm trang Đăng nhập / Đăng ký / Quên mật khẩu
    - port từ phần <head>/<body> chung của DangNhapV2.aspx và DangKyV2.aspx
--}}
<html lang="vi">
<head>
    {{-- (tính năng mới, không có trong code gốc) Cài đặt SEO cho trang đăng
         nhập / đăng ký, copy từ layouts/app.blade.php - chỉnh được qua
         /admin/seo. Trang con dùng section "seo_page" với giá trị "login"
         hoặc "register" để chọn đúng cài đặt (mặc định "login"). --}}
    @php
        $seo = seo_setting(trim($__env->yieldContent('seo_page', 'login')));
    @endphp

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- (tính năng mới, không có trong code gốc) Favicon - lấy từ cài đặt
         chung /admin/cai-dat (favicon), nếu chưa cấu hình thì dùng mặc định
         giống layouts/app.blade.php. --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ site_setting('favicon') ?: '/img/logo.webp' }}">

    <meta name="author" content="{{ $seo['meta_title'] }}">
    <meta name="keywords" content="{{ $seo['meta_keywords'] }}">

    <title>@yield('title', $seo['meta_title'])</title>

    <meta name="description" content="{{ $seo['meta_description'] }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $seo['og_title'] }}">
    <meta property="og:description" content="{{ $seo['og_description'] }}">
    <meta property="og:image" content="{{ $seo['og_image'] }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="JX Kiểm Hiệp 1 Mobile">

    <!-- Google -->
    <meta itemprop="name" content="{{ $seo['og_title'] }}">
    <meta itemprop="description" content="{{ $seo['og_description'] }}">

    <meta name="robots" content="index,follow">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="/files/id_tl.css">
    <link rel="stylesheet" href="/files/id_login.css">
    {{-- (tính năng mới, không có trong code gốc) Nạp CSS trang chủ để dùng
         chung navbar (.jx-topnav-*, .header, .header_inner_lstNavtop...) -
         nạp TRƯỚC id_login_v2.css để file sau có thể override các xung đột
         (body background, chiều cao .header...). --}}
    <link rel="stylesheet" type="text/css" href="/home_files/styles.css?v=1.0.9">
    {{-- (tính năng mới, không có trong code gốc) Giao diện mới cho trang đăng nhập/đăng ký --}}
    <link rel="stylesheet" href="/files/id_login_v2.css">
    @php $bgMobileAuth = site_setting('bg_mobile'); @endphp
    @if($bgMobileAuth)
    <style>
        @media (max-width: 768px) {
            body { --auth-bg-image: url('{{ $bgMobileAuth }}'); }
        }
    </style>
    @endif
    @yield('head')
</head>
@php $bgDesktopAuth = site_setting('bg_desktop'); @endphp
<body
    {{-- Ảnh nền dùng chung cho tất cả trang (chỉnh tại /admin/cai-dat).
         Dùng CSS custom property vì home_files/styles.css có rule
         "body{background:#fff !important}" đè lên background thường. --}}
    @if($bgDesktopAuth)
        style="--auth-bg-image: url('{{ $bgDesktopAuth }}');"
    @endif
>

    @include('layouts._topnav')

    <div class="modal custom-modal" tabindex="-1" role="dialog" id="modalMsg">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <span class="modal-title-content">Thông báo</span>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="row justify-content-md-center main_login">
            <div class="col-md-12 col-lg-8 col-xl-6 id_login">
                @yield('content')
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
    <script>
        // tương đương hàm ShowConfirm() dùng chung trong các trang gốc
        function ShowConfirm(title, msg, type, redirectUrl) {
            if (type === 2 && redirectUrl) {
                window.location.href = redirectUrl;
                return;
            }

            $('#modalMsg .modal-title-content').html(title);
            $('#modalMsg .modal-body').html(msg);
            $('#modalMsg').modal('show');
        }

        // Thêm sẵn CSRF token cho mọi request AJAX (Laravel)
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        // (tính năng mới, không có trong code gốc) Toggle menu dropdown mobile
        // (".jx-topnav-dropdown") - giống trang chủ (layouts/app.blade.php)
        document.getElementById('jxTopnavBurger')?.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('jxTopnavWrap')?.classList.toggle('active');
        });
    </script>

    @yield('scripts')
</body>
</html>
