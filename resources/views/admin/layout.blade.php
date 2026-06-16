<!DOCTYPE html>
{{--
    Layout cho khu vực quản trị /admin6 - port từ Admin6/AdminSite1.Master,
    sử dụng AdminLTE 3.2 (Bootstrap 4.6 + FontAwesome 5) qua CDN thay cho
    CoreUI / thư mục dist gốc.
--}}
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, shrink-to-fit=no">
    @php $adminSettings = admin_settings(); @endphp
    <title>{{ $adminSettings['admin_title'] }} :: @yield('title', 'Trang chủ')</title>

    {{-- (tính năng mới, không có trong code gốc) Favicon - lấy từ cài đặt
         chung /admin/cai-dat (favicon), nếu chưa cấu hình thì dùng mặc định. --}}
    <link rel="icon" type="image/png" sizes="32x32" href="{{ site_setting('favicon') ?: '/img/logo-mobile-r.jpg' }}">

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
            </ul>
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link">Xin chào, <strong>{{ session('admin_user') }}</strong></span>
                </li>
                <li class="nav-item">
                    <form action="{{ route('admin.logout') }}" method="POST" class="d-inline">
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
            <a href="{{ route('admin.home') }}" class="brand-link">
                <span class="brand-text font-weight-light ml-3">{{ $adminSettings['admin_title'] }}</span>
            </a>

            <div class="sidebar">
                <nav class="mt-2">
                    @php
                        // (tính năng mới, không có trong code gốc) iRole == 1 là admin
                        // toàn quyền, khác 1 chỉ thấy Trang chủ/Quản lý tin/Quản lý slide
                        // - xem middleware EnsureAdminRole (admin.role).
                        $isFullAdmin = (int) session('admin_role', 0) === 1;

                        // Tương đương LoadQLNap() trong AdminHome.aspx.cs / AdminPageHome.aspx.cs
                        $canNapThe = $isFullAdmin && in_array(session('admin_user'), ['phongkieu84', 'nguyenlm'], true);
                    @endphp
                    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                        <li class="nav-item">
                            <a href="{{ route('admin.home') }}" class="nav-link @if(request()->routeIs('admin.home')) active @endif">
                                <i class="nav-icon fas fa-tachometer-alt"></i>
                                <p>Trang chủ</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('admin.news.index') }}" class="nav-link @if(request()->routeIs('admin.news.*')) active @endif">
                                <i class="nav-icon fas fa-newspaper"></i>
                                <p>Quản lý tin</p>
                            </a>
                        </li>
                        @if($canNapThe)
                            <li class="nav-item">
                                <a href="{{ route('admin.napthe') }}" class="nav-link @if(request()->routeIs('admin.napthe')) active @endif">
                                    <i class="nav-icon fas fa-credit-card"></i>
                                    <p>Quản lý nạp thẻ</p>
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a href="{{ route('admin.slides.index') }}" class="nav-link @if(request()->routeIs('admin.slides.*')) active @endif">
                                <i class="nav-icon fas fa-images"></i>
                                <p>Quản lý slide</p>
                            </a>
                        </li>
                        @if($isFullAdmin)
                            <li class="nav-item">
                                <a href="{{ route('admin.seo') }}" class="nav-link @if(request()->routeIs('admin.seo')) active @endif">
                                    <i class="nav-icon fas fa-search"></i>
                                    <p>Cài đặt SEO</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.footer') }}" class="nav-link @if(request()->routeIs('admin.footer')) active @endif">
                                    <i class="nav-icon fas fa-shoe-prints"></i>
                                    <p>Cài đặt Footer</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.general') }}" class="nav-link @if(request()->routeIs('admin.general')) active @endif">
                                    <i class="nav-icon fas fa-cog"></i>
                                    <p>Cài đặt chung</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.daily-config') }}" class="nav-link @if(request()->routeIs('admin.daily-config')) active @endif">
                                    <i class="nav-icon fas fa-id-card"></i>
                                    <p>Cài đặt trang Đại lý</p>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route('admin.dai-ly.index') }}" class="nav-link @if(request()->routeIs('admin.dai-ly.*')) active @endif">
                                    <i class="nav-icon fas fa-user-tie"></i>
                                    <p>Quản lý Đại lý</p>
                                </a>
                            </li>
                        @endif
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
                            <h1 class="m-0">@yield('title', 'Trang chủ')</h1>
                        </div>
                        <div class="col-sm-6">
                            <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="{{ route('admin.home') }}">Admin</a></li>
                                @yield('breadcrumb')
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <section class="content">
                <div class="container-fluid">
                    {{-- (tính năng mới, không có trong code gốc) Thông báo khi tài
                         khoản không đủ quyền (iRole khác 1) cố truy cập chức năng
                         bị hạn chế - xem middleware EnsureAdminRole. --}}
                    @if(session('admin_role_error'))
                        <div class="alert alert-warning alert-dismissible fade show">
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            {{ session('admin_role_error') }}
                        </div>
                    @endif

                    @yield('content')
                </div>
            </section>
        </div>

        <footer class="main-footer">
            <div class="float-right d-none d-sm-inline">{{ $adminSettings['admin_footer_text'] }}</div>
            <strong>Trang quản trị</strong>
        </footer>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/admin-lte@3.2/dist/js/adminlte.min.js"></script>
    @yield('scripts')
</body>
</html>
