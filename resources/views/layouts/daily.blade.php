<!DOCTYPE html>
{{--
    Layout cho nhóm trang Đại lý (DaiLyNapThe.aspx, TongDaiLy.aspx)
    - chuyển sang giao diện AdminLTE 3.2 (Bootstrap 4.6 + FontAwesome 5) qua
      CDN, cùng phong cách với khu vực /admin (xem admin/layout.blade.php).
--}}
<html lang="vi">
<head>
    @php
        // Cài đặt giao diện khu vực Đại lý (favicon, title, brand, footer) +
        // SEO trang "daily", chỉnh được ở /admin/dai-ly-config (tính năng mới,
        // không có trong code gốc).
        $dailySettings = daily_settings();
        $dailySeo = seo_setting('daily');
    @endphp

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ $dailySettings['favicon'] }}">
    <title>@yield('title', $dailySettings['title']) :: {{ $dailySettings['footer_text'] }}</title>

    <meta name="description" content="{{ $dailySeo['meta_description'] }}">
    <meta name="keywords" content="{{ $dailySeo['meta_keywords'] }}">

    <!-- Open Graph -->
    <meta property="og:title" content="{{ $dailySeo['og_title'] }}">
    <meta property="og:description" content="{{ $dailySeo['og_description'] }}">
    <meta property="og:image" content="{{ $dailySeo['og_image'] }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/css/adminlte.min.css">
    @yield('head')
</head>
<body class="hold-transition sidebar-mini layout-fixed">
    <div class="wrapper">

        {{-- Navbar --}}
        <nav class="main-header navbar navbar-expand navbar-white navbar-light">
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="/" class="nav-link">Trang Chủ</a>
                </li>
                <li class="nav-item d-none d-sm-inline-block">
                    <a href="{{ site_setting('link_facebook') }}" target="_blank" class="nav-link">Fanpage</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <form action="{{ route('daily.logout') }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="nav-link border-0 bg-transparent" style="cursor:pointer">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </button>
                    </form>
                </li>
            </ul>
        </nav>

        {{-- Sidebar --}}
        <aside class="main-sidebar sidebar-dark-primary elevation-4">
            <a href="/dai-ly-nap-the" class="brand-link">
                <span class="brand-text font-weight-light ml-3">{{ $dailySettings['brand_text'] }}</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    @php
                        // (tính năng mới, không có trong code gốc) Chỉ Đại lý Tổng
                        // (IsAdmin != 0) mới thấy menu "Đại lý Tổng" - tương tự
                        // điều kiện $isAdmin trong dai-ly/nap-the.blade.php và
                        // DaiLyController::tongDaiLy().
                        $daiLyCurrent = request()->attributes->get('daiLy');
                        $isAdminMenu = $daiLyCurrent ? (int) $daiLyCurrent->IsAdmin : 0;
                    @endphp
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="/dai-ly-nap-the" class="nav-link @if(request()->routeIs('daily.napthe')) active @endif">
                                <i class="nav-icon fas fa-credit-card"></i>
                                <p>Quản lý nạp</p>
                            </a>
                        </li>
                        @if($isAdminMenu != 0)
                            <li class="nav-item">
                                <a href="/tong-dai-ly" class="nav-link @if(request()->routeIs('daily.tongdaily')) active @endif">
                                    <i class="nav-icon fas fa-sitemap"></i>
                                    <p>Đại lý Tổng</p>
                                </a>
                            </li>
                        @endif
                        {{-- (tính năng mới, không có trong code gốc) Tất cả Đại lý
                             (không phân biệt IsAdmin) đều được đổi mật khẩu / cập
                             nhật thông tin tài khoản của chính mình - xem
                             DaiLyController. --}}
                        <li class="nav-item">
                            <a href="{{ route('daily.change-password') }}" class="nav-link @if(request()->routeIs('daily.change-password')) active @endif">
                                <i class="nav-icon fas fa-user-cog"></i>
                                <p>Thông tin tài khoản</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('daily.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link border-0 bg-transparent w-100 text-left">
                                    <i class="nav-icon fas fa-sign-out-alt"></i>
                                    <p>Đăng xuất</p>
                                </button>
                            </form>
                        </li>
                    </ul>
                </nav>
            </div>
        </aside>

        {{-- Content Wrapper --}}
        <div class="content-wrapper">
            <div class="content-header">
                <div class="container-fluid">
                    <div class="row mb-2">
                        <div class="col-sm-6">
                            <h1 class="m-0">@yield('title', 'Đại lý')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><span>Đại lý</span></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <strong>{{ $dailySettings['footer_text'] }}</strong>
        </footer>
    </div>

    {{-- Modal thông báo dùng chung (ShowConfirm) --}}
    <div class="modal fade" tabindex="-1" role="dialog" id="modalMsg">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title modal-title-content">Thông báo</h5>
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

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    <script>
        function ShowConfirm(title, msg, type, redirectUrl) {
            if (type === 2 && redirectUrl) {
                window.location.href = redirectUrl;
                return;
            }

            $('#modalMsg .modal-title-content').html(title);
            $('#modalMsg .modal-body').html(msg);
            $('#modalMsg').modal('show');
        }

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        });

        /**
         * Hiển thị thông báo dạng alert (kết quả AJAX) và tự ẩn sau một
         * khoảng thời gian (tính năng mới, không có trong code gốc).
         *
         * @param {jQuery} $el       Phần tử chứa thông báo (ví dụ #sdtMess, #napMess)
         * @param {string} baseClass Class gốc của phần tử (ví dụ "mb-2", "mb-3")
         * @param {boolean} success  true -> alert-success, false -> alert-danger
         * @param {string} html      Nội dung thông báo
         * @param {number} [duration=5000] Thời gian hiển thị trước khi tự ẩn (ms)
         */
        function flashMessage($el, baseClass, success, html, duration) {
            duration = duration || 5000;

            clearTimeout($el.data('hideTimer'));

            $el.stop(true, true)
                .attr('class', baseClass + ' alert ' + (success ? 'alert-success' : 'alert-danger'))
                .html(html)
                .show();

            var timer = setTimeout(function () {
                $el.fadeOut(400);
            }, duration);

            $el.data('hideTimer', timer);
        }
    </script>

    @yield('scripts')
</body>
</html>
