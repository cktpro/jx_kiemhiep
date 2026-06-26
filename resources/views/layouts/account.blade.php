<!DOCTYPE html>
{{--
    Layout cho nhóm trang Tài khoản (Thông tin / Đổi mật khẩu / Đổi SĐT)
    - Navbar copy y hệt layouts/app.blade.php (trang chủ).
    - Card nội dung dùng chung CSS id_login_v2.css (glassmorphism) với trang
      đăng nhập/đăng ký.
--}}
<html lang="vi">
<head>
    @php
        $seo = seo_setting(trim($__env->yieldContent('seo_page', 'account')));
    @endphp

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <link rel="stylesheet" type="text/css" href="/home_files/styles.css?v=1.0.9">
    <link rel="stylesheet" href="/files/id_login_v2.css">
    @php $bgMobileAcc = site_setting('bg_mobile'); @endphp
    @if($bgMobileAcc)
    <style>
        @media (max-width: 768px) {
            body { --auth-bg-image: url('{{ $bgMobileAcc }}'); }
        }
    </style>
    @endif
    @yield('head')
</head>
@php $bgDesktopAcc = site_setting('bg_desktop'); @endphp
<body
    @if($bgDesktopAcc)
        style="--auth-bg-image: url('{{ $bgDesktopAcc }}');"
    @endif
>

@include('layouts._topnav')

{{-- ── Modal thông báo ──────────────────────────────────────────────────────── --}}
<div class="modal custom-modal" tabindex="-1" role="dialog" id="modalMsg">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><span class="modal-title-content">Thông báo</span></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body"></div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-dismiss="modal">Đóng</button>
            </div>
        </div>
    </div>
</div>

{{-- ── Content card ─────────────────────────────────────────────────────────── --}}
<div class="container">
        <div class="row justify-content-md-center main_login">
            <div class="col-md-12 col-lg-8 col-xl-6 id_login">
                @yield('content')
            </div>
        </div>
</div>

{{-- ── Footer — clone từ layouts/app.blade.php ─────────────────────────────── --}}
@php $footer = footer_settings(); @endphp
<footer class="footer">
    <div class="container">
        <div class="d-flex flex-column flex-md-row justify-content-center align-items-start align-items-md-center gap-3">
            <div class="r-border-mobile pb-3 pb-md-0">
                <img width="300" height="200" src="{{ $footer['logo'] }}" alt="{{ $footer['logo_alt'] }}">
            </div>
            <div class="r-border-mobile d-flex flex-column align-items-start pb-3 pb-md-0 gap-1">
                @foreach($footer['info_lines'] as $line)
                    <div class="d-flex justify-content-start gap-3">
                        <span>{{ $line['label'] }}</span><span>{{ $line['value'] }}</span>
                    </div>
                @endforeach
            </div>
            <div class="d-flex flex-column align-items-start gap-1">
                @foreach($footer['links'] as $link)
                    <a href="{{ $link['url'] }}" class="d-flex gap-2 text-white align-items-center">
                        <i class="{{ $link['icon'] }} r-icon-footer"></i><span>{{ $link['label'] }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
<script>
    function ShowConfirm(title, msg, type, redirectUrl) {
        if (type === 2 && redirectUrl) { window.location.href = redirectUrl; return; }
        $('#modalMsg .modal-title-content').html(title);
        $('#modalMsg .modal-body').html(msg);
        $('#modalMsg').modal('show');
    }
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });
    document.getElementById('jxTopnavBurger')?.addEventListener('click', function (e) {
        e.preventDefault();
        document.getElementById('jxTopnavWrap')?.classList.toggle('active');
    });
</script>
@yield('scripts')
</body>
</html>
