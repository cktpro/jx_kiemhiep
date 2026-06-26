<!DOCTYPE html>
{{--
    Layout chung - port từ WebDaiChienTongKim/Site.Master + Site.Master.cs

    Cách dùng trong các view con:

        @extends('layouts.app')

        @section('title', 'Tiêu đề trang')

        @section('head')
            (thêm css/meta riêng cho trang nếu cần)
        @endsection

        @section('content')
            (nội dung trang - tương đương ContentPlaceHolder1)
        @endsection

        @section('scripts')
            (script riêng cho trang - tương đương ContentPlaceHolder "script")
        @endsection
--}}
<html lang="vi">
<head>
    @php
        // Trang gọi @section('seo_page', 'news') để dùng cài đặt SEO riêng,
        // mặc định là 'home' - xem App\Services\SeoSettings, chỉnh ở /admin/seo.
        $seo = seo_setting(trim($__env->yieldContent('seo_page', 'home')));
    @endphp

    @yield('head')

    @php $bgDesktop = site_setting('bg_desktop'); $bgMobile = site_setting('bg_mobile'); @endphp
    @if($bgDesktop || $bgMobile)
    <style>
        @if($bgDesktop)
        .bg-new { background: url('{{ $bgDesktop }}') center top / 100% auto no-repeat #ede8d0 !important; }
        @endif
        @if($bgMobile)
        @@media (max-width: 768px) {
            .bg-new { background: url('{{ $bgMobile }}') center top / 100% auto no-repeat #ede8d0 !important; }
        }
        @endif
    </style>
    @endif

    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">

    <link rel="icon" type="image/png" sizes="32x32" href="{{ site_setting('favicon') ?: '/img/logo.webp' }}">

    <meta name="author" content="{{ $seo['meta_title'] }}">
    <meta name="keywords" content="{{ $seo['meta_keywords'] }}">

    <title>@yield('title', $seo['meta_title'])</title>

    @hasSection('meta_description')
        @yield('meta_description')
    @else
        <meta name="description" content="{{ $seo['meta_description'] }}">
    @endif

    <!-- Open Graph -->
    @hasSection('og_meta')
        @yield('og_meta')
    @else
        <meta property="og:title" content="{{ $seo['og_title'] }}">
        <meta property="og:description" content="{{ $seo['og_description'] }}">
        <meta property="og:image" content="{{ $seo['og_image'] }}">
    @endif
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="JX Kiểm Hiệp 1 Mobile">

    <!-- Google -->
    @sectionMissing('og_meta')
        <meta itemprop="name" content="{{ $seo['og_title'] }}">
        <meta itemprop="description" content="{{ $seo['og_description'] }}">
    @endif

    <meta name="robots" content="index,follow">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>

    <link rel="stylesheet" type="text/css" href="/home_files/styles.css?v=1.0.9">

    @stack('head-extra')
</head>
<body class="bg-new">
    @include('layouts._topnav')

    <div>
        @yield('content')
    </div>

    @php $footer = footer_settings(); @endphp
    <footer class="footer">
        <div class="container">
            <div class="d-flex flex-column flex-md-row justify-content-center align-items-start align-items-md-center  gap-3">
                <div class="r-border-mobile pb-3 pb-md-0"> <img width="300" height="200" src="{{ $footer['logo'] }}" alt="{{ $footer['logo_alt'] }}">  </div>
                <div class="r-border-mobile d-flex flex-column align-items-start pb-3 pb-md-0 gap-1">
                    @foreach($footer['info_lines'] as $line)
                        <div class="d-flex justify-content-start gap-3"><span>{{ $line['label'] }} </span><span>{{ $line['value'] }}</span></div>
                    @endforeach
                </div>
                <div class="d-flex flex-column align-items-start gap-1">
                    @foreach($footer['links'] as $link)
                        <a href="{{ $link['url'] }}" class="d-flex gap-2 text-white align-items-center "><i class="{{ $link['icon'] }} r-icon-footer"></i><span>{{ $link['label'] }}</span></a>
                    @endforeach
                </div>
            </div>
        </div>
    </footer>

    </div>

    {{--
        Sidebar nổi (floating aside) - port từ "jx_kiemhiep/landinggame/index.html"
        (#aside trong .floating). Hiển thị ở góc phải, có thể đóng/mở bằng nút
        mũi tên. Ảnh nền + sprite icon nằm tại "public/landinggame/images/".
        Ẩn trên mobile (xem .jx-aside trong styles.css).
    --}}
    <ul id="jxAside" class="jx-aside active">
        <li>
            <a href="#" id="jxAsideToggle" class="jx-aside__item jx-aside__item--toggle" title="Đóng / Mở"></a>
        </li>
        <li>
            <a href="{{ route('napthe.coin') }}" class="jx-aside__item jx-aside__item--topup" title="Nạp Coin"></a>
        </li>
        <li>
            <a href="{{ site_setting('link_download_ios') }}" target="_blank" class="jx-aside__item jx-aside__item--downappstore" title="Tải iOS"></a>
        </li>
        <li>
            <a href="{{ route('tai-game') }}" class="jx-aside__item jx-aside__item--downggplay" title="Tải Google Play"></a>
        </li>
        <li>
            <a href="{{ site_setting('link_download_android') }}" target="_blank" class="jx-aside__item jx-aside__item--downapk" title="Tải APK"></a>
        </li>
        <li>
            <a href="{{ site_setting('link_youtube') }}" target="_blank" class="jx-aside__item jx-aside__item--youtube" title="Youtube"></a>
            <a href="{{ site_setting('link_facebook') }}" target="_blank" class="jx-aside__item jx-aside__item--fanpage" title="Fanpage"></a>
            <a href="{{ site_setting('link_zalo') }}" target="_blank" class="jx-aside__item jx-aside__item--group" title="Nhóm Zalo"></a>
        </li>
        <li>
            <a href="{{ route('tai-game') }}" class="jx-aside__item jx-aside__item--code" title="Hướng dẫn / Giftcode"></a>
        </li>
    </ul>

    @yield('scripts')

    <script>
        // Toggle sidebar nổi (".jx-aside") - port từ landinggame (#asideToggle).
        document.getElementById('jxAsideToggle')?.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('jxAside')?.classList.toggle('active');
        });

        // Toggle menu dropdown mobile (".jx-topnav-dropdown").
        document.getElementById('jxTopnavBurger')?.addEventListener('click', function (e) {
            e.preventDefault();
            document.getElementById('jxTopnavWrap')?.classList.toggle('active');
        });
    </script>
</body>
</html>
