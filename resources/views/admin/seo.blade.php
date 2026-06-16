@extends('admin.layout')

{{--
    Cài đặt SEO (meta title/description/keywords, Open Graph) cho Trang chủ
    và Trang tin tức - tính năng mới, không có trang tương ứng trong Admin6
    gốc. Dữ liệu đọc/ghi qua App\Services\SeoSettings (file JSON, không cần
    thêm bảng vào database SQL Server).
--}}

@section('title', 'Cài đặt SEO')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Cài đặt SEO</span></li>
@endsection

@section('content')
    @if($saved)
        <div id="seo-alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Đã lưu cài đặt SEO cho {{ ['home' => 'Trang chủ', 'news' => 'Trang tin tức', 'napthe' => 'Trang nạp thẻ', 'news_detail' => 'Trang chi tiết tin', 'login' => 'Trang đăng nhập', 'register' => 'Trang đăng ký', 'account' => 'Trang tài khoản', 'doi-sdt' => 'Trang đổi SĐT'][$saved] ?? $saved }}.
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="seo-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="tab-home-link" data-toggle="pill" href="#tab-home" role="tab">
                        <i class="fas fa-home mr-1"></i> Trang chủ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-news-link" data-toggle="pill" href="#tab-news" role="tab">
                        <i class="fas fa-newspaper mr-1"></i> Trang tin tức
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-napthe-link" data-toggle="pill" href="#tab-napthe" role="tab">
                        <i class="fas fa-credit-card mr-1"></i> Trang nạp thẻ
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-news_detail-link" data-toggle="pill" href="#tab-news_detail" role="tab">
                        <i class="fas fa-file-alt mr-1"></i> Trang chi tiết tin
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-login-link" data-toggle="pill" href="#tab-login" role="tab">
                        <i class="fas fa-right-to-bracket mr-1"></i> Trang đăng nhập
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-register-link" data-toggle="pill" href="#tab-register" role="tab">
                        <i class="fas fa-user-plus mr-1"></i> Trang đăng ký
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-account-link" data-toggle="pill" href="#tab-account" role="tab">
                        <i class="fas fa-circle-user mr-1"></i> Trang tài khoản
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="tab-doi-sdt-link" data-toggle="pill" href="#tab-doi-sdt" role="tab">
                        <i class="fas fa-mobile-screen-button mr-1"></i> Trang đổi SĐT
                    </a>
                </li>
            </ul>
        </div>
        <div class="card-body">
            <div class="tab-content" id="seo-tabs-content">
                {{-- Trang chủ --}}
                <div class="tab-pane fade show active" id="tab-home" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'home', 'data' => $settings['home']])
                </div>

                {{-- Trang tin tức --}}
                <div class="tab-pane fade" id="tab-news" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'news', 'data' => $settings['news']])
                </div>

                {{-- Trang nạp thẻ --}}
                <div class="tab-pane fade" id="tab-napthe" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'napthe', 'data' => $settings['napthe']])
                </div>

                {{-- Trang chi tiết tin --}}
                <div class="tab-pane fade" id="tab-news_detail" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'news_detail', 'data' => $settings['news_detail']])
                </div>

                {{-- Trang đăng nhập --}}
                <div class="tab-pane fade" id="tab-login" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'login', 'data' => $settings['login']])
                </div>

                {{-- Trang đăng ký --}}
                <div class="tab-pane fade" id="tab-register" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'register', 'data' => $settings['register']])
                </div>

                {{-- Trang tài khoản --}}
                <div class="tab-pane fade" id="tab-account" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'account', 'data' => $settings['account']])
                </div>

                {{-- Trang đổi SĐT --}}
                <div class="tab-pane fade" id="tab-doi-sdt" role="tabpanel">
                    @include('admin.seo-form', ['page' => 'doi-sdt', 'data' => $settings['doi-sdt']])
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        // Mở đúng tab vừa lưu (nếu có)
        @if($saved === 'news')
            $(function () {
                $('#tab-news-link').tab('show');
            });
        @endif

        @if($saved === 'napthe')
            $(function () {
                $('#tab-napthe-link').tab('show');
            });
        @endif

        @if($saved === 'news_detail')
            $(function () {
                $('#tab-news_detail-link').tab('show');
            });
        @endif

        @if($saved === 'login')
            $(function () {
                $('#tab-login-link').tab('show');
            });
        @endif

        @if($saved === 'register')
            $(function () {
                $('#tab-register-link').tab('show');
            });
        @endif

        @if($saved === 'account')
            $(function () {
                $('#tab-account-link').tab('show');
            });
        @endif

        @if($saved === 'doi-sdt')
            $(function () {
                $('#tab-doi-sdt-link').tab('show');
            });
        @endif

        @if($saved)
            setTimeout(function () {
                var alertEl = document.getElementById('seo-alert');
                if (alertEl && window.jQuery) {
                    jQuery(alertEl).fadeOut(400, function () {
                        jQuery(this).alert('close');
                    });
                } else if (alertEl) {
                    alertEl.style.display = 'none';
                }
            }, 4000);
        @endif
    </script>
@endsection
