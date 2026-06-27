@extends('layouts.app')

{{-- port từ Default.aspx + Default.aspx.cs -> / (trang chủ, theme jx1m skin-2020) --}}

{{-- Tiêu đề + meta SEO lấy từ cài đặt "Trang chủ" tại /admin/seo (App\Services\SeoSettings) --}}
@section('seo_page', 'home')

@push('head-extra')
    {{--
        home-v9.css đã được gỡ bỏ: chỉ rule ".block__special-feature" (khối
        "TÍNH NĂNG ĐẶC SẮC" bên dưới) còn được dùng từ file này, đã port sang
        /home_files/styles.css. Phần còn lại của home-v9.css (~7000 dòng)
        thuộc giao diện trang chủ cũ, không còn áp dụng cho markup hiện tại.

        script-v8.js cũng đã được gỡ bỏ: file này (368KB) chỉ là gói nhiều
        plugin/khối JS cũ (popup cookie, fancybox, Swiper core, menu cũ,
        posts-tab cũ, "tuyệt học" cũ, ranking/pagination cũ) cộng 8 lệnh
        "new Swiper(...)" nhắm vào các class/id (".home-banner",
        ".special-feature", ".tuyethoc-news", "#block-ranking", ".swiper-mp",
        ".swiper-mp-tab"...) không còn tồn tại trong markup hiện tại - section
        môn phái mới (".mpx-*") đã có slider JS thuần riêng (xem cuối file).
        Không còn gì cần "lấy ra" từ file này.
    --}}

    {{--
        Hệ thống môn phái (".mpx-*"): viết lại độc lập với home-v9.css/script-v8.js
        (".swiper-mp", ".frame-mp", ".mp-tab"...) vì các class đó dùng kích thước
        cố định (2000x1000 desktop, 1130px height mobile...) gây lỗi đè hình /
        thừa nền trên mobile. Layout mới dùng aspect-ratio + % để co giãn thật
        theo chiều rộng khung, và slider chạy bằng JS thuần (xem cuối file).
    --}}
    <style>
        .mpx-section {
            position: relative;
            margin-top: 0;
            border-radius: 14px;
            overflow: hidden;
            background: linear-gradient(to bottom, #fffdf6 0%, #fbf4dd 100%);
            border: 1px solid #e6d3a3;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            padding-bottom: 16px;
        }

        /* Đồng bộ với khung tin tức (.news-detail-box / .news-sidebar-box):
           gradient kem + viền vàng nhạt, thay cho ảnh nền cũ. */
        .block__special-feature {
            background: linear-gradient(to bottom, #fffdf6 0%, #fbf4dd 100%) !important;
            border: 1px solid #e6d3a3 !important;
            border-radius: 10px !important;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08) !important;
        }


        /* Tiêu đề "HỆ THỐNG MÔN PHÁI" - co giãn theo chiều rộng khung, có đổ bóng nhẹ */
        .mpx-title-wrap {
            display: flex;
            justify-content: center;
            padding: 14px 16px 0;
        }

        /* Banner tiêu đề dạng ribbon (dùng chung cho ".mpx-title-wrap" -
           HỆ THỐNG MÔN PHÁI và phần TÍNH NĂNG ĐẶC SẮC) - co giãn đồng bộ
           theo chiều rộng khung, có đổ bóng nhẹ. */
        .jx-banner-title {
            max-width: 420px;
            width: 100%;
            height: auto;
            filter: drop-shadow(0 4px 10px rgba(0, 0, 0, .25));
        }

        /* Khung nhân vật: tỉ lệ gốc 2000x1000 (desktop) */
        .mpx-stage {
            position: relative;
            width: 100%;
            aspect-ratio: 2000 / 1000;
            overflow: hidden;
            margin-top: 12px;
        }

        .mpx-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            visibility: hidden;
            transform: scale(1.04);
            transition: opacity .7s ease, transform 1.2s ease;
        }

        .mpx-slide.active {
            opacity: 1;
            visibility: visible;
            transform: scale(1);
            z-index: 1;
        }

        /* Nhãn tên môn phái đang hiển thị, góc dưới-trái khung nhân vật */
        .mpx-name {
            position: absolute;
            left: 16px;
            bottom: 16px;
            z-index: 4;
            padding: 6px 18px;
            border-radius: 999px;
            background: linear-gradient(180deg, #ffd877, #c9a227);
            color: #2a1d05;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: .5px;
            text-transform: uppercase;
            box-shadow: 0 4px 10px rgba(0, 0, 0, .35);
            transition: opacity .25s ease;
        }

        /* info-mp gốc: 1049x793 trong khung 2000x1000 => 52.45% x 79.3%.
           Căn giữa khung (nằm phía sau ảnh nhân vật nhờ z-index thấp hơn). */
        .mpx-slide .mpx-info {
            position: absolute;
            left: 12%;
            top: -9.65%;
            width: 52.45%;
            height: 79.3%;
            z-index: 1;
        }

        /* char gốc: 1147x893 (tỉ lệ 1.285:1). Phóng to ~12% để nhân vật tràn full
           chiều cao khung .mpx-stage (top:0 -> bottom:100%), giữ nguyên tỉ lệ và
           căn giữa theo chiều ngang trong khung. */
        .mpx-slide .mpx-char {
            position: absolute;
            left: 17.89%;
            top: 0;
            width: 64.22%;
            height: 100%;
            z-index: 2;
        }

        .mpx-slide .mpx-char-mb {
            display: none;
            width: 100%;
            height: auto;
        }

        .mpx-nav {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 44px;
            height: 44px;
            border-radius: 50%;
            border: 1px solid rgba(201, 162, 39, .6);
            background: rgba(0, 0, 0, .35);
            backdrop-filter: blur(2px);
            color: #ffd877;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            transition: background .2s, transform .2s, box-shadow .2s;
            padding: 0;
        }

        .mpx-nav:hover {
            background: rgba(0, 0, 0, .6);
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 0 12px rgba(201, 162, 39, .6);
        }

        .mpx-prev {
            left: 16px;
        }

        .mpx-next {
            right: 16px;
        }

        .mpx-tabs {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 10px 12px 0;
            padding: 10px 12px;
            border-radius: 12px;
            background: linear-gradient(180deg, rgba(0, 0, 0, .04), rgba(0, 0, 0, .1));
            border: 1px solid rgba(201, 162, 39, .25);
        }

        .mpx-tab-list {
            list-style: none;
            display: flex;
            flex-wrap: nowrap;
            gap: 6px;
            margin: 0;
            padding: 4px 0;
            overflow-x: auto;
            scroll-behavior: smooth;
            flex: 1;
            justify-content: space-evenly;
            scrollbar-width: none;
        }

        /* Ẩn thanh scrollbar ngang của hàng tab (vẫn cuộn được bằng kéo/nút mũi tên) */
        .mpx-tab-list::-webkit-scrollbar {
            display: none;
            width: 0;
            height: 0;
        }

        .mpx-tab-item {
            flex: 0 0 auto;
        }

        .mpx-tab-item button {
            display: block;
            width: 88px;
            aspect-ratio: 130 / 222;
            border: 0;
            background-color: transparent;
            background-repeat: no-repeat;
            background-position: center;
            background-size: contain;
            cursor: pointer;
            padding: 0;
            opacity: .6;
            transition: opacity .2s, transform .2s;
        }

        .mpx-tab-item.active button,
        .mpx-tab-item button:hover {
            opacity: 1;
            transform: translateY(-4px);
        }

        .mpx-tab-item.active button {
            filter: drop-shadow(0 0 6px rgba(255, 216, 119, .85));
        }

        @for ($i = 1; $i <= 10; $i++)
            .mpx-tab-item.tab-{{ $i }} button {
                background-image: url(/Home/products/jx1m/skin-2020/images/desktop/pag-mp-{{ $i }}.png);
            }

            .mpx-tab-item.tab-{{ $i }}.active button,
            .mpx-tab-item.tab-{{ $i }} button:hover {
                background-image: url(/Home/products/jx1m/skin-2020/images/desktop/pag-mp-{{ $i }}-hov.png);
            }
        @endfor

        .mpx-tab-nav {
            flex: 0 0 auto;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: 1px solid rgba(201, 162, 39, .5);
            background: rgba(0, 0, 0, .3);
            color: #ffd877;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            transition: background .2s, box-shadow .2s;
        }

        .mpx-tab-nav:hover {
            background: rgba(0, 0, 0, .55);
            box-shadow: 0 0 10px rgba(201, 162, 39, .5);
        }

        /* Slider ảnh khu "TÍNH NĂNG ĐẶC SẮC": bo góc, đổ bóng, tỉ lệ cố định
           (ảnh luôn lấp đầy khung bằng object-fit: cover, không bị méo/giãn),
           nút điều hướng và chấm chỉ báo đồng bộ tông vàng-đỏ của trang. */
        .feature-slider {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 6px 16px rgba(0, 0, 0, .18);
            border: 1px solid #e6d3a3;
        }

        .feature-slider .carousel-inner {
            border-radius: 12px;
            overflow: hidden;
        }

        .feature-slider .carousel-item {
            aspect-ratio: 16 / 7;
        }

        .feature-slider .carousel-item > img,
        .feature-slider .carousel-item > a > img {
            width: 100%;
            height: 100%;
        }

        /* Nút prev/next dạng vòng tròn vàng-đen, giống .mpx-nav ở khung
           "Hệ thống môn phái" - đồng bộ giao diện 2 khu vực. */
        .feature-slider .carousel-control-prev,
        .feature-slider .carousel-control-next {
            /* !important vì home_files/styles.css có sẵn rule
               ".carousel-control-prev, .carousel-control-next { width: 10% !important }"
               trong @media (min-width: 1024px) - nếu không ép !important,
               nút sẽ bị kéo giãn thành hình bầu dục (biến dạng) trên PC. */
            top: 50% !important;
            width: 44px !important;
            height: 44px !important;
            bottom: auto !important;
            transform: translateY(-50%);
            border-radius: 50%;
            border: 1px solid rgba(201, 162, 39, .6);
            background: rgba(0, 0, 0, .35);
            backdrop-filter: blur(2px);
            color: #ffd877;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            z-index: 5;
            opacity: 1;
            padding: 0;
            transition: background .2s, transform .2s, box-shadow .2s;
        }

        .feature-slider .carousel-control-prev {
            left: 16px;
        }

        .feature-slider .carousel-control-next {
            right: 16px;
        }

        .feature-slider .carousel-control-prev:hover,
        .feature-slider .carousel-control-next:hover {
            background: rgba(0, 0, 0, .6);
            transform: translateY(-50%) scale(1.08);
            box-shadow: 0 0 12px rgba(201, 162, 39, .6);
        }

        .feature-slider .carousel-indicators {
            margin-bottom: 8px;
        }

        .feature-slider .carousel-indicators [data-bs-target] {
            /* !important để đè rule ".carousel-indicators [data-bs-target]"
               (20px, viền đen, nền đỏ) trong home_files/styles.css. */
            width: 9px !important;
            height: 9px !important;
            border: none !important;
            border-radius: 50% !important;
            background-color: rgba(255, 255, 255, .55) !important;
            opacity: 1;
            transition: background-color .2s, transform .2s;
        }

        .feature-slider .carousel-indicators .active {
            background-color: #ffd877 !important;
            transform: scale(1.25);
        }

        @media screen and (max-width: 1024px) {
            /* Mobile: dùng ảnh char-mb đã gộp sẵn nhân vật + info, ẩn lớp desktop */
            .mpx-stage {
                aspect-ratio: 750 / 1130;
            }

            .mpx-slide .mpx-info,
            .mpx-slide .mpx-char {
                display: none;
            }

            .mpx-slide .mpx-char-mb {
                display: block;
            }

            .mpx-nav {
                display: none;
            }

            .jx-banner-title {
                max-width: 280px;
            }

            /* Mobile: ảnh slide thường cao hơn (chụp màn hình dọc), tỉ lệ
               vuông hơn để không bị crop quá nhiều. */
            .feature-slider .carousel-item {
                aspect-ratio: 4 / 3;
            }

            .mpx-name {
                left: 10px;
                bottom: 10px;
                padding: 4px 14px;
                font-size: .85rem;
            }

            .mpx-tab-item button {
                width: 58px;
                aspect-ratio: 130 / 130;
            }

            @for ($i = 1; $i <= 10; $i++)
                .mpx-tab-item.tab-{{ $i }} button {
                    background-image: url(/Home/products/jx1m/skin-2020/images/desktop/pag-mp-{{ $i }}-mb.png);
                }

                .mpx-tab-item.tab-{{ $i }}.active button,
                .mpx-tab-item.tab-{{ $i }} button:hover {
                    background-image: url(/Home/products/jx1m/skin-2020/images/desktop/pag-mp-{{ $i }}-mb-hov.png);
                }
            @endfor
        }
    </style>

    {{--
        Khung tin tức trang chủ (".news-frame"): viết lại độc lập với
        .main_ct/.navtabs/.list-posts cũ, dùng nền khung trang trí của
        khtd.vn + tab dạng pill + danh sách [Tiêu đề | Ngày], đồng bộ màu
        cam-đỏ active với menu header và nút "Xem tất cả bài viết".
    --}}
    <style>
        .news-frame {
            position: relative;
            margin-bottom: 30px;
            border-radius: 18px;
            background-image: url('/assests/images/news-frame.jpg');
            background-repeat: no-repeat;
            background-position: center;
            /* Ảnh local là 1 khung viền màu kem gần như đồng nhất (không có
               hoa văn phức tạp như bản khtd.vn) nên kéo giãn full 100% 100%
               để lấp đầy khung, không chừa khoảng trắng 2 bên/trên dưới. */
            background-size: 100% 100%;
        }

        /* Khối nội dung bên trong khung tin tức. */
        .news-frame__inner {
            padding: 26px 30px;
        }

        .news-frame__banner {
            display: flex;
            align-items: center;
        }

        .news-frame__banner img {
            width: 100%;
            border-radius: 12px;
        }

        .news-frame__panel {
            display: flex;
            flex-direction: column;
            padding-left: 28px;
        }

        .news-frame__tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            list-style: none;
            padding: 0;
            margin: 0 0 12px;
            border-bottom: 0;
        }

        .news-frame__tabs .nav-link {
            padding: 5px 18px;
            border-radius: 20px;
            border: 1px solid #e6d3a3;
            background: #fdf6e3;
            color: #5a3b00;
            font-weight: 700;
            font-size: 0.875rem;
            line-height: 1.6;
        }

        .news-frame__tabs .nav-link.active,
        .news-frame__tabs .nav-link:hover {
            background: linear-gradient(to bottom, #ff9a4d 0%, #ff6a00 55%, #ff3d00 100%);
            color: #fff;
            border-color: #ff6a00;
            text-shadow: 0 1px 1px rgba(0, 0, 0, .25);
        }

        .news-frame__list {
            flex: 1;
            overflow: hidden;
        }

        .news-frame__item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            padding: 6px 0;
            border-bottom: 1px dashed rgba(184, 134, 11, .35);
        }

        .news-frame__item:last-child {
            border-bottom: none;
        }

        .news-frame__item a {
            color: #4a3000;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .news-frame__item a:hover {
            color: #ff6600;
        }

        .news-frame__item time {
            color: #b3742a;
            font-size: 0.8125rem;
            white-space: nowrap;
        }

        .news-frame__more-wrap {
            text-align: right;
            margin-top: 10px;
        }

        .news-frame__more {
            display: inline-block;
            padding: 6px 24px;
            border-radius: 20px;
            background: linear-gradient(to bottom, #ffd36e 0%, #ff9a00 100%);
            color: #6b3a00;
            font-weight: 700;
            font-size: 0.8125rem;
            text-decoration: none;
            box-shadow: 0 2px 5px rgba(0, 0, 0, .2);
        }

        .news-frame__more:hover {
            color: #4a2700;
        }

        @media (max-width: 991px) {
            .news-frame {
                aspect-ratio: auto;
                background-image: none;
                background: linear-gradient(to bottom, #fffdf6 0%, #fbf4dd 100%);
                border: 1px solid #e6d3a3;
                box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            }

            .news-frame__inner {
                padding: 18px;
            }

            .news-frame__panel {
                padding-left: 0;
                margin-top: 18px;
            }
        }
    </style>
@endpush

@section('content')
    <section class="container">
        <div id="loading" style="display: none" class="f-loading cnt"></div>

        <div class="d-flex justify-content-center align-items-center my-5">
            <div class="d-flex justify-content-center align-items-center gap-2 feature-bg">
                <div class="d-none d-md-block">
                    <a href="{{ site_setting('link_login') }}">
                        <img width="300" height="200" src="/img/logo-kiem-hiep.webp"></a>
                </div>
                <div class="d-none d-md-block">
                    <a href="{{ site_setting('link_login') }}">
                        <img class="w-100" src="/home_files/18t.png"></a>
                </div>
                <div class="">
                    <a href="{{ site_setting('link_login') }}">
                        <img class="w-100" src="/home_files/qrcode.png"></a>
                </div>
                <div class="d-flex flex-column gap-1 r-link-down">
                    <a href="{{ $linkIOS }}" class="spr main_dl-btdl main_dl-ios" target="_blank"></a>
                    <a href="{{ $linkAndroid }}" class="spr main_dl-btdl main_dl-gg" target="_blank"></a>
                </div>
            </div>
        </div>

        <div class="f-container">
            <div class="overlay"></div>
            <div id="trailer" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                <div class="modalVideo cnt" id="f-video"></div>
            </div>

            <main class="main fixCen">
                {{--
                    Khung tin tức trang chủ - giao diện theo mẫu khtd.vn:
                    nền khung trang trí (news-frame.webp), banner game bên
                    trái, tab chuyên mục dạng pill + danh sách [Tiêu đề | Ngày]
                    bên phải, nút "Xem Toàn Bộ" -> /tin-tuc/all.
                --}}
                <div class="news-frame">
                    <div class="news-frame__inner">
                        <div class="row g-0 h-100">
                            <div class="col-12 col-lg-5 news-frame__banner">
                                <a href="/">
                                    <img src="{{ site_setting('banner_news') ?: '/img/bgHero_shxt.webp' }}" alt="JX Kiểm Hiệp 1 Mobile"></a>
                            </div>
                            <div class="col-12 col-lg-7 news-frame__panel">
                                <ul class="nav news-frame__tabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="news-tab-tt" data-bs-toggle="tab" data-bs-target="#news-tin-tuc" type="button" role="tab" aria-controls="news-tin-tuc" aria-selected="true">Tin Tức</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="news-tab-sk" data-bs-toggle="tab" data-bs-target="#news-su-kien" type="button" role="tab" aria-controls="news-su-kien" aria-selected="false">Sự Kiện</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="news-tab-cn" data-bs-toggle="tab" data-bs-target="#news-cam-nang" type="button" role="tab" aria-controls="news-cam-nang" aria-selected="false">Cẩm Nang</button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="news-tab-tc" data-bs-toggle="tab" data-bs-target="#news-tat-ca" type="button" role="tab" aria-controls="news-tat-ca" aria-selected="false">Tất Cả</button>
                                    </li>
                                </ul>
                                <div class="tab-content news-frame__list">
                                    <div class="tab-pane fade show active" id="news-tin-tuc" role="tabpanel" aria-labelledby="news-tab-tt">
                                        @include('home.partials.list-tin-frame', ['items' => $tinTucList])
                                    </div>
                                    <div class="tab-pane fade" id="news-su-kien" role="tabpanel" aria-labelledby="news-tab-sk">
                                        @include('home.partials.list-tin-frame', ['items' => $tinSuKien])
                                    </div>
                                    <div class="tab-pane fade" id="news-cam-nang" role="tabpanel" aria-labelledby="news-tab-cn">
                                        @include('home.partials.list-tin-frame', ['items' => $tinHuongDan])
                                    </div>
                                    <div class="tab-pane fade" id="news-tat-ca" role="tabpanel" aria-labelledby="news-tab-tc">
                                        @include('home.partials.list-tin-frame', ['items' => $tinTatCa])
                                    </div>
                                </div>
                                <div class="news-frame__more-wrap">
                                    <a href="/tin-tuc/all" class="news-frame__more">Xem Toàn Bộ <i class="fa-solid fa-angle-right"></i></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- HỆ THỐNG MÔN PHÁI --}}
                <div class="mpx-section mb-3">
                    <div class="mpx-title-wrap">
                        <img class="jx-banner-title" src="/Home/products/jx1m/skin-2020/images/themes/title-3-v2-1x.png" alt="Hệ thống môn phái" />
                    </div>

                    @php
                        $monPhaiSlides = [
                            ['name' => 'Thiếu Lâm', 'info' => 'mp1-info.png', 'char' => '/Home/upload/jx1m/source/News/Mainsite/mp1-char.png', 'mb' => '/Home/upload/jx1m/source/News/Mainsite/mp1-mb.jpg'],
                            ['name' => 'Thiên Vương', 'info' => 'mp2-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp2-char.png', 'mb' => '/Home/upload/jx1m/source/News/Mainsite/mp2-mb.jpg'],
                            ['name' => 'Đường Môn', 'info' => 'mp3-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp10-char.png', 'mb' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp3-mb.jpg'],
                            ['name' => 'Nga Mi', 'info' => 'mp4-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp4-char.png', 'mb' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp4-mb.jpg'],
                            ['name' => 'Cái Bang', 'info' => 'mp5-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp5-char.png', 'mb' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp5-mb.jpg'],
                            ['name' => 'Côn Lôn', 'info' => 'mp6-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp6-char.png', 'mb' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp6-mb.jpg'],
                            ['name' => 'Võ Đang', 'info' => 'mp7-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp7-char.png', 'mb' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp7-mb.jpg'],
                            ['name' => 'Thiên Nhẫn', 'info' => 'mp8-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp8-char.png', 'mb' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp8-mb.jpg'],
                            ['name' => 'Thúy Yên', 'info' => 'mp9-info.png', 'char' => '/Home/upload/jx1m/source/News/Mainsite/mp9-char.png', 'mb' => '/Home/upload/jx1m/source/News/Mainsite/mp9-mb.jpg'],
                            ['name' => 'Ngũ Độc', 'info' => 'mp10-info.png', 'char' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp3-char.png', 'mb' => '/Home/products/jx1m/skin-2020/images/nv-monphai/mp10-mb.jpg'],
                        ];
                    @endphp

                    <div class="mpx-stage" id="mpxStage">
                        @foreach ($monPhaiSlides as $i => $slide)
                            <div class="mpx-slide @if ($i === 0) active @endif" data-index="{{ $i }}" data-name="{{ $slide['name'] }}">
                                <img src="/Home/products/jx1m/skin-2020/images/nv-monphai/{{ $slide['info'] }}" alt="" class="mpx-info" loading="lazy" />
                                <img src="{{ $slide['char'] }}" alt="{{ $slide['name'] }}" class="mpx-char" loading="lazy" />
                                <img src="{{ $slide['mb'] }}" alt="{{ $slide['name'] }}" class="mpx-char-mb" loading="lazy" />
                            </div>
                        @endforeach

                        <div class="mpx-name" id="mpxName">{{ $monPhaiSlides[0]['name'] }}</div>

                        <button type="button" class="mpx-nav mpx-prev" aria-label="Môn phái trước">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <button type="button" class="mpx-nav mpx-next" aria-label="Môn phái kế tiếp">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>

                    <div class="mpx-tabs">
                        <button type="button" class="mpx-tab-nav mpx-tab-prev" aria-label="Cuộn trái">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        </button>
                        <ul class="mpx-tab-list" id="mpxTabList">
                            @foreach ($monPhaiSlides as $i => $slide)
                                <li class="mpx-tab-item tab-{{ $i + 1 }} @if ($i === 0) active @endif" data-index="{{ $i }}">
                                    <button type="button" title="{{ $slide['name'] }}" aria-label="{{ $slide['name'] }}"></button>
                                </li>
                            @endforeach
                        </ul>
                        <button type="button" class="mpx-tab-nav mpx-tab-next" aria-label="Cuộn phải">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </button>
                    </div>
                </div>

                {{-- TÍNH NĂNG ĐẶC SẮC --}}
                <div class="block block__special-feature">
                    <div class="d-flex justify-content-center align-content-center py-1">
                        <img class="jx-banner-title" src="/Home/products/jx1m/skin-2020/images/themes/title-5-v2-1x.png" alt="Tính năng đặc sắc" />
                    </div>
                    <div class="px-md-2 pt-3 pb-4">
                        {{--
                            Danh sách ảnh slide lấy từ SlideSettings (quản lý tại
                            /admin/slides). Nếu chưa cấu hình thì dùng 8 ảnh mặc định
                            trong /img/ (xem App\Services\SlideSettings::defaults()).
                        --}}
                        <div id="carouselExampleIndicators" class="carousel slide feature-slider">
                            <div class="carousel-indicators gap-1">
                                @foreach($featureSlides as $i => $slide)
                                    <button type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide-to="{{ $i }}" @if($i === 0) class="active" aria-current="true" @endif aria-label="Slide {{ $i + 1 }}"></button>
                                @endforeach
                            </div>
                            <div class="carousel-inner">
                                @foreach($featureSlides as $i => $slide)
                                    <div class="carousel-item @if($i === 0) active @endif">
                                        @if(!empty($slide['link']))
                                            <a href="{{ $slide['link'] }}">
                                                <img src="{{ $slide['image'] }}" class="d-block w-100" alt="{{ $slide['alt'] ?? ('Slide '.($i + 1)) }}">
                                            </a>
                                        @else
                                            <img src="{{ $slide['image'] }}" class="d-block w-100" alt="{{ $slide['alt'] ?? ('Slide '.($i + 1)) }}">
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                            <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="prev" aria-label="Slide trước">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                                <span class="visually-hidden">Previous</span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators" data-bs-slide="next" aria-label="Slide kế tiếp">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                                <span class="visually-hidden">Next</span>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <div id="cms" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
            <div class="comming-soon cnt">
                <a href="javascript:void(0);" data-dismiss="modal" class="closeModal">×</a>
                <p class="rs">Coming Soon!</p>
            </div>
        </div>
    </section>
@endsection

@section('scripts')
    <script src="/home_files/index-v3.bundle.js" type="text/javascript"></script>
    <script type="text/javascript" src="/home_files/jquery-3.5.1.min.js"></script>
    <script type="text/javascript" src="/home_files/bootstrap.min.js"></script>
    <script type="text/javascript" src="/home_files/all.js"></script>
    <script type="text/javascript" src="/home_files/owl.carousel2.2.js"></script>
    <script type="text/javascript" src="/home_files/lazysizes.min.js"></script>
    <!-- <script type="text/javascript" src="/home_files/main.js"></script> -->

    {{-- Slider "Hệ thống môn phái" - JS thuần, độc lập với Swiper/script-v8.js --}}
    <script>
        (function () {
            var stage = document.getElementById('mpxStage');
            if (!stage) return;

            var slides = stage.querySelectorAll('.mpx-slide');
            var tabItems = document.querySelectorAll('#mpxTabList .mpx-tab-item');
            var tabList = document.getElementById('mpxTabList');
            var prevBtn = stage.querySelector('.mpx-prev');
            var nextBtn = stage.querySelector('.mpx-next');
            var tabPrevBtn = document.querySelector('.mpx-tab-prev');
            var tabNextBtn = document.querySelector('.mpx-tab-next');
            var nameEl = document.getElementById('mpxName');

            var total = slides.length;
            var current = 0;
            var autoplayId = null;
            var AUTOPLAY_MS = 5000;

            function show(index) {
                index = ((index % total) + total) % total;
                if (index === current) return;

                slides[current].classList.remove('active');
                tabItems[current].classList.remove('active');

                current = index;

                slides[current].classList.add('active');
                tabItems[current].classList.add('active');

                if (nameEl) {
                    nameEl.style.opacity = '0';
                    setTimeout(function () {
                        nameEl.textContent = slides[current].dataset.name || '';
                        nameEl.style.opacity = '1';
                    }, 200);
                }

                if (tabList && tabItems[current]) {
                    tabList.scrollTo({
    left: tabItems[current].offsetLeft - tabList.clientWidth / 2,
    behavior: 'smooth'
});
                }
            }

            function next() { show(current + 1); }
            function prev() { show(current - 1); }

            function stopAutoplay() {
                if (autoplayId) {
                    clearInterval(autoplayId);
                    autoplayId = null;
                }
            }

            function startAutoplay() {
                stopAutoplay();
                autoplayId = setInterval(next, AUTOPLAY_MS);
            }

            if (prevBtn) prevBtn.addEventListener('click', function () { prev(); startAutoplay(); });
            if (nextBtn) nextBtn.addEventListener('click', function () { next(); startAutoplay(); });
            if (tabPrevBtn) tabPrevBtn.addEventListener('click', function () { prev(); startAutoplay(); });
            if (tabNextBtn) tabNextBtn.addEventListener('click', function () { next(); startAutoplay(); });

            tabItems.forEach(function (tab, i) {
                tab.addEventListener('click', function () {
                    show(i);
                    startAutoplay();
                });
            });

            startAutoplay();
        })();
    </script>
@endsection
