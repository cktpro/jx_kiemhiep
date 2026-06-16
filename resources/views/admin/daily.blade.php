@extends('admin.layout')

{{--
    Cài đặt giao diện khu vực Đại lý (favicon, title, tên brand sidebar, nội
    dung footer, SEO) hiển thị ở layouts/daily.blade.php - tính năng mới,
    không có trang tương ứng trong Admin6 gốc. Dữ liệu đọc/ghi qua
    App\Services\DaiLySettings và App\Services\SeoSettings (file JSON, không
    cần thêm bảng vào database SQL Server).
--}}

@section('title', 'Cài đặt trang Đại lý')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Cài đặt trang Đại lý</span></li>
@endsection

@section('content')
    @if($saved)
        <div id="daily-alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Đã lưu cài đặt trang Đại lý.
        </div>
    @endif

    @if($seoSaved === 'daily')
        <div id="daily-seo-alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Đã lưu cài đặt SEO trang Đại lý.
        </div>
    @endif

    {{-- Giao diện chung --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-palette mr-1"></i> Giao diện</h3>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.daily-config.save') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Favicon hiện tại</label>
                    <div class="col-sm-10">
                        <div>
                            <img src="{{ $settings['favicon'] }}" alt="Favicon" style="max-width: 64px; max-height: 64px;" class="img-thumbnail">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="favicon" class="col-sm-2 col-form-label">Đổi favicon</label>
                    <div class="col-sm-10">
                        <input type="file" name="favicon" id="favicon" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,.gif,.svg,.ico">
                        <small class="form-text text-muted">Hỗ trợ JPG, PNG, WEBP, GIF, SVG, ICO. Tối đa 5MB. Bỏ trống nếu không đổi favicon.</small>
                    </div>
                </div>

                <div class="form-group">
                    <label for="title">Tên trang (Title)</label>
                    <input type="text" id="title" name="title" class="form-control"
                        value="{{ old('title', $settings['title']) }}" maxlength="100">
                    <small class="form-text text-muted">
                        Hiển thị trên thẻ &lt;title&gt; (ví dụ: "{{ $settings['title'] }} :: {{ $settings['footer_text'] }}").
                    </small>
                </div>

                <div class="form-group">
                    <label for="brand_text">Tên hiển thị sidebar (Brand)</label>
                    <input type="text" id="brand_text" name="brand_text" class="form-control"
                        value="{{ old('brand_text', $settings['brand_text']) }}" maxlength="100">
                    <small class="form-text text-muted">
                        Hiển thị ở góc trên bên trái menu của trang Đại lý.
                    </small>
                </div>

                <div class="form-group">
                    <label for="footer_text">Nội dung footer</label>
                    <input type="text" id="footer_text" name="footer_text" class="form-control"
                        value="{{ old('footer_text', $settings['footer_text']) }}" maxlength="100">
                    <small class="form-text text-muted">
                        Hiển thị ở cuối mọi trang trong khu vực Đại lý.
                    </small>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu cài đặt
                </button>
            </form>
        </div>
    </div>

    {{-- SEO --}}
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-search mr-1"></i> SEO</h3>
        </div>
        <div class="card-body">
            @include('admin.seo-form', ['page' => 'daily', 'data' => $seo])
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        @if($saved)
            setTimeout(function () {
                var alertEl = document.getElementById('daily-alert');
                if (alertEl && window.jQuery) {
                    jQuery(alertEl).fadeOut(400, function () {
                        jQuery(this).alert('close');
                    });
                } else if (alertEl) {
                    alertEl.style.display = 'none';
                }
            }, 4000);
        @endif

        @if($seoSaved === 'daily')
            setTimeout(function () {
                var alertEl = document.getElementById('daily-seo-alert');
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
