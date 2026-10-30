@extends('admin.layout')

{{--
    Cài đặt chung cho khu vực quản trị: "Tên trang quản trị" cùng các cấu
    hình trước đây nằm trong config/site.php (link mạng xã hội, link tải
    game, SĐT nhận OTP, độ dài tài khoản/mật khẩu, footer...). Tính năng mới,
    không có trang tương ứng trong Admin6 gốc. Dữ liệu đọc/ghi qua
    App\Services\AdminSettings (file JSON, không cần thêm bảng vào database
    SQL Server).
--}}

@section('title', 'Cài đặt chung')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Cài đặt chung</span></li>
@endsection

@section('content')
    @if($saved)
        <div id="general-alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Đã lưu cài đặt chung.
        </div>
    @endif

    <form method="POST" action="{{ route('admin.general.save') }}" enctype="multipart/form-data">
        @csrf

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cog mr-1"></i> Trang quản trị</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="admin_title">Tên trang quản trị</label>
                    <input type="text" id="admin_title" name="admin_title" class="form-control"
                        value="{{ old('admin_title', $settings['admin_title']) }}" maxlength="100">
                    <small class="form-text text-muted">
                        Hiển thị trên thẻ &lt;title&gt; và logo ở góc trên bên trái của trang quản trị.
                    </small>
                </div>

                <div class="form-group">
                    <label for="admin_footer_text">Nội dung footer trang quản trị</label>
                    <input type="text" id="admin_footer_text" name="admin_footer_text" class="form-control"
                        value="{{ old('admin_footer_text', $settings['admin_footer_text']) }}" maxlength="100">
                    <small class="form-text text-muted">
                        Hiển thị ở góc dưới bên phải mọi trang quản trị (cạnh chữ "Trang quản trị").
                    </small>
                </div>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-link mr-1"></i> Link đăng nhập / đăng ký</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="link_login">Link trang đăng nhập</label>
                    <input type="text" id="link_login" name="link_login" class="form-control"
                        value="{{ old('link_login', $settings['link_login']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Mặc định: <code>/dang-nhap</code>. Hiển thị ở: menu điều hướng, nút "Đăng Nhập Ngay"
                        ở trang đăng ký / quên mật khẩu, và các ảnh trên trang chủ.
                    </small>
                </div>
                <div class="form-group">
                    <label for="link_register">Link trang đăng ký</label>
                    <input type="text" id="link_register" name="link_register" class="form-control"
                        value="{{ old('link_register', $settings['link_register']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Mặc định: <code>/dang-ky</code>. Hiển thị ở: menu điều hướng, nút "Đăng Ký Ngay"
                        ở trang đăng nhập / quên mật khẩu.
                    </small>
                </div>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-share-alt mr-1"></i> Mạng xã hội / Cộng đồng</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="link_facebook">Link Fanpage Facebook</label>
                    <input type="text" id="link_facebook" name="link_facebook" class="form-control"
                        value="{{ old('link_facebook', $settings['link_facebook']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: menu "Cộng đồng" (header), icon Fanpage trong sidebar nổi,
                        link "Fanpage" ở footer trang chủ, trang nạp coin, đăng nhập/đăng ký, trang tài khoản
                        và trang Đại lý.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_zalo">Link nhóm Zalo</label>
                    <input type="text" id="link_zalo" name="link_zalo" class="form-control"
                        value="{{ old('link_zalo', $settings['link_zalo']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: menu "Hỗ trợ" (header), icon "Nhóm Zalo" trong sidebar nổi,
                        và link "Hỗ trợ" / "Nhóm Zalo" ở footer trang chủ, nút Zalo nổi góc phải màn hình.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_messenger">Link Messenger</label>
                    <input type="text" id="link_messenger" name="link_messenger" class="form-control"
                        value="{{ old('link_messenger', $settings['link_messenger']) }}" maxlength="250"
                        placeholder="https://m.me/ten-fanpage">
                    <small class="form-text text-muted">
                        Hiển thị ở: nút Messenger nổi góc phải màn hình (máy tính + điện thoại).
                        Để trống = tự dùng m.me/&lt;tên page&gt; lấy từ link Fanpage Facebook.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_tiktok">Link TikTok</label>
                    <input type="text" id="link_tiktok" name="link_tiktok" class="form-control"
                        value="{{ old('link_tiktok', $settings['link_tiktok']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Đang lưu để dùng cho các bản cập nhật sau, hiện chưa hiển thị ở trang nào.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_youtube">Link Youtube</label>
                    <input type="text" id="link_youtube" name="link_youtube" class="form-control"
                        value="{{ old('link_youtube', $settings['link_youtube']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: icon Youtube trong sidebar nổi (góc phải trang chủ).
                    </small>
                </div>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-download mr-1"></i> Link tải game / hướng dẫn</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="link_tai_game">Link hướng dẫn tải game</label>
                    <input type="text" id="link_tai_game" name="link_tai_game" class="form-control"
                        value="{{ old('link_tai_game', $settings['link_tai_game']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: link "Tải game" trong footer trang chủ.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_download_googleplay">Link tải game (Google Play)</label>
                    <input type="text" id="link_download_googleplay" name="link_download_googleplay" class="form-control"
                        value="{{ old('link_download_googleplay', $settings['link_download_googleplay']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: icon "Tải Google Play" trong sidebar nổi.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_download_android">Link tải game (Android / APK)</label>
                    <input type="text" id="link_download_android" name="link_download_android" class="form-control"
                        value="{{ old('link_download_android', $settings['link_download_android']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: icon "Tải APK" trong sidebar nổi, link tải game trên Trang chủ khi truy cập
                        từ điện thoại Android, và trang nạp coin/đăng nhập/đăng ký khi trình duyệt là Android.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_download_ios">Link tải game (iOS / TestFlight)</label>
                    <input type="text" id="link_download_ios" name="link_download_ios" class="form-control"
                        value="{{ old('link_download_ios', $settings['link_download_ios']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: icon "Tải iOS" trong sidebar nổi, link tải game trên Trang chủ khi truy cập
                        từ iPhone/iPad, và trang nạp coin/đăng nhập/đăng ký khi trình duyệt là iOS.
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_download_default">Link tải game (mặc định)</label>
                    <input type="text" id="link_download_default" name="link_download_default" class="form-control"
                        value="{{ old('link_download_default', $settings['link_download_default']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: link tải game trên Trang chủ và trang nạp coin/đăng nhập/đăng ký khi truy
                        cập từ thiết bị không phải Android/iOS (ví dụ máy tính).
                    </small>
                </div>

                <div class="form-group">
                    <label for="link_download_emulator">Link tải game (Giả lập PC)</label>
                    <input type="text" id="link_download_emulator" name="link_download_emulator" class="form-control"
                        value="{{ old('link_download_emulator', $settings['link_download_emulator']) }}" maxlength="250"
                        placeholder="/download/ten-file-gia-lap.apk">
                    <small class="form-text text-muted">
                        Hiển thị ở: mục "Giả lập (PC)" trên trang <a href="{{ route('tai-game') }}" target="_blank">/tai-game</a>
                        (bản APK cài vào LDPlayer, BlueStacks...). Để trống = trang tải game hiện "Đang cập nhật".
                    </small>
                </div>
            </div>
        </div>

        {{-- Upload file (APK, ảnh, zip...) vào public/download/ rồi copy link.
             Upload qua AJAX, chia khúc 5MB (xem App\Services\FileUploadService),
             nên không phụ thuộc nút "Lưu cài đặt" bên dưới. --}}
        @php
            $fmtSize = function (int $bytes): string {
                if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2).' GB';
                if ($bytes >= 1048576) return number_format($bytes / 1048576, 1).' MB';
                if ($bytes >= 1024) return number_format($bytes / 1024, 0).' KB';
                return $bytes.' B';
            };
        @endphp
        <div class="card card-primary card-outline" id="file-upload-card">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-cloud-upload-alt mr-1"></i> Upload file &amp; lấy link</h3>
            </div>
            <div class="card-body">
                <div class="form-group mb-2">
                    <label for="fu-input">Chọn file để upload</label>
                    <div class="d-flex flex-wrap" style="gap:8px">
                        <input type="file" id="fu-input" class="form-control-file" style="max-width:420px"
                            accept=".{{ implode(',.', \App\Services\FileUploadService::ALLOWED_EXTS) }}">
                        <button type="button" id="fu-start" class="btn btn-primary btn-sm">
                            <i class="fas fa-upload mr-1"></i> Upload
                        </button>
                        <button type="button" id="fu-cancel" class="btn btn-default btn-sm d-none">Huỷ</button>
                    </div>
                    <small class="form-text text-muted">
                        Cho phép: {{ implode(', ', \App\Services\FileUploadService::ALLOWED_EXTS) }}.
                        Tối đa {{ \App\Services\FileUploadService::MAX_SIZE / 1024 / 1024 }} MB/file.
                        File lưu vào <code>/{{ \App\Services\FileUploadService::PUBLIC_DIR }}/</code>; trùng tên sẽ tự thêm số (-1, -2...), không ghi đè file cũ.
                    </small>
                </div>

                <div id="fu-progress-wrap" class="d-none mb-3">
                    <div class="progress" style="height:20px">
                        <div id="fu-progress" class="progress-bar progress-bar-striped progress-bar-animated"
                            role="progressbar" style="width:0%">0%</div>
                    </div>
                    <small id="fu-status" class="text-muted"></small>
                </div>

                <div id="fu-error" class="alert alert-danger d-none mb-3"></div>

                <div id="fu-result" class="alert alert-success d-none mb-3">
                    <div class="mb-1"><i class="fas fa-check-circle mr-1"></i> Upload xong: <strong id="fu-result-name"></strong></div>
                    <div class="input-group input-group-sm">
                        <input type="text" id="fu-result-link" class="form-control bg-white" readonly>
                        <div class="input-group-append">
                            <button type="button" class="btn btn-dark fu-copy" data-target="fu-result-link">
                                <i class="fas fa-copy mr-1"></i> Copy link
                            </button>
                        </div>
                    </div>
                </div>

                <label class="mb-1">File đã upload</label>
                <div class="table-responsive">
                    <table class="table table-sm table-bordered mb-0">
                        <thead class="thead-light">
                            <tr>
                                <th>Tên file</th>
                                <th style="width:90px">Dung lượng</th>
                                <th style="width:130px">Ngày upload</th>
                                <th style="min-width:280px">Link</th>
                                <th style="width:50px"></th>
                            </tr>
                        </thead>
                        <tbody id="fu-tbody">
                            @forelse($uploadedFiles as $i => $f)
                                <tr data-name="{{ $f['name'] }}">
                                    <td class="text-break">{{ $f['name'] }}</td>
                                    <td>{{ $fmtSize($f['size']) }}</td>
                                    <td>{{ date('d/m/Y H:i', $f['mtime']) }}</td>
                                    <td>
                                        <div class="input-group input-group-sm">
                                            <input type="text" id="fu-link-{{ $i }}" class="form-control" value="{{ $f['url'] }}" readonly>
                                            <div class="input-group-append">
                                                <button type="button" class="btn btn-outline-secondary fu-copy" data-target="fu-link-{{ $i }}" title="Copy link">
                                                    <i class="fas fa-copy"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-xs btn-danger fu-delete" title="Xoá file"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="fu-empty"><td colspan="5" class="text-center text-muted">Chưa có file nào.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- (tính năng mới, không có trong code gốc) Ảnh nền trang chủ / đăng
             nhập / đăng ký - chọn ảnh để upload hoặc nhập URL ảnh. --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-image mr-1"></i> Ảnh nền (Background)</h3>
            </div>
            <div class="card-body">
                @foreach ([
                    'bg_desktop' => 'Desktop (tất cả trang)',
                    'bg_mobile'  => 'Mobile (tất cả trang)',
                ] as $bgKey => $bgLabel)
                    <div class="form-group">
                        <label for="{{ $bgKey }}">Background {{ $bgLabel }}</label>

                        @if($settings[$bgKey])
                            <div class="mb-2">
                                <img src="{{ $settings[$bgKey] }}" alt="Background {{ $bgLabel }}" style="max-height:80px;max-width:100%;border:1px solid #dee2e6;border-radius:4px;">
                            </div>
                        @endif

                        <div class="form-row">
                            <div class="form-group col-md-6 mb-2">
                                <input type="file" class="form-control-file" id="{{ $bgKey }}_file" name="{{ $bgKey }}_file" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
                            </div>
                            <div class="form-group col-md-6 mb-2">
                                <input type="text" id="{{ $bgKey }}" name="{{ $bgKey }}" class="form-control"
                                    value="{{ old($bgKey, $settings[$bgKey]) }}" maxlength="250" placeholder="Hoặc nhập URL ảnh (https://...)">
                            </div>
                        </div>
                        <small class="form-text text-muted">
                            Chọn ảnh để upload (ưu tiên), hoặc nhập <strong>đường dẫn tương đối</strong> (ví dụ: <code>/img/ten-anh.webp</code>) — <strong>không</strong> nhập URL có <code>http://</code>. Để trống cả hai để dùng ảnh nền mặc định có sẵn.
                        </small>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Banner góc phải khung tin tức trang chủ (.news-frame__banner) --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-newspaper mr-1"></i> Banner Tin Tức (Trang chủ)</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="banner_news">Ảnh banner bên phải khung tin tức</label>

                    @if($settings['banner_news'])
                        <div class="mb-2">
                            <img src="{{ $settings['banner_news'] }}" alt="Banner tin tức" style="max-height:80px;max-width:100%;border:1px solid #dee2e6;border-radius:4px;">
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group col-md-6 mb-2">
                            <input type="file" class="form-control-file" id="banner_news_file" name="banner_news_file" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <input type="text" id="banner_news" name="banner_news" class="form-control"
                                value="{{ old('banner_news', $settings['banner_news']) }}" maxlength="250" placeholder="Hoặc nhập URL ảnh (https://...)">
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Dùng đường dẫn tương đối (ví dụ: <code>/img/bgHero_shxt.webp</code>) — <strong>không</strong> nhập URL đầy đủ có <code>http://</code> vì sẽ bị lỗi khi đổi domain / server. Để trống để dùng ảnh mặc định.
                    </small>
                </div>
            </div>
        </div>

        {{-- (tính năng mới, không có trong code gốc) Favicon áp dụng cho toàn
             bộ trang (trang chủ, đăng nhập, đăng ký, tài khoản, admin, đại
             lý...) - chọn ảnh để upload hoặc nhập URL ảnh. --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-star mr-1"></i> Favicon</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="favicon">Favicon (áp dụng cho toàn bộ trang)</label>

                    @if($settings['favicon'])
                        <div class="mb-2">
                            <img src="{{ $settings['favicon'] }}" alt="Favicon" style="max-height:48px;max-width:48px;border:1px solid #dee2e6;border-radius:4px;">
                        </div>
                    @endif

                    <div class="form-row">
                        <div class="form-group col-md-6 mb-2">
                            <input type="file" class="form-control-file" id="favicon_file" name="favicon_file" accept=".jpg,.jpeg,.png,.webp,.gif,.svg,.ico">
                        </div>
                        <div class="form-group col-md-6 mb-2">
                            <input type="text" id="favicon" name="favicon" class="form-control"
                                value="{{ old('favicon', $settings['favicon']) }}" maxlength="250" placeholder="Hoặc nhập URL ảnh (https://...)">
                        </div>
                    </div>
                    <small class="form-text text-muted">
                        Chọn ảnh để upload (ưu tiên), hoặc nhập URL ảnh. Hỗ trợ JPG, PNG, WEBP, GIF, SVG, ICO. Để trống
                        cả hai để mỗi khu vực dùng favicon mặc định riêng (trang đại lý có thể có favicon riêng tại
                        Cài đặt trang Đại lý, sẽ ưu tiên nếu được thiết lập).
                    </small>
                </div>
            </div>
        </div>

        {{-- Menu điều hướng trang chủ (dùng chung cho dropdown burger + nav mobile) --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-bars mr-1"></i> Menu điều hướng (Header)</h3>
            </div>
            <div class="card-body p-0">
                <input type="hidden" id="nav_items_input" name="nav_items"
                    value="{{ old('nav_items', $settings['nav_items'] ?? '[]') }}">
                <table class="table table-sm table-bordered mb-0" id="nav-table">
                    <thead class="thead-light">
                        <tr>
                            <th style="width:60px">Thứ tự</th>
                            <th>Nhãn</th>
                            <th>Icon (FA class)</th>
                            <th>URL <small class="text-muted">(hoặc <code>setting:key</code>)</small></th>
                            <th>Active path</th>
                            <th style="width:70px;text-align:center">Tab mới</th>
                            <th style="width:40px"></th>
                        </tr>
                    </thead>
                    <tbody id="nav-tbody"></tbody>
                </table>
            </div>
            <div class="card-footer">
                <button type="button" class="btn btn-sm btn-secondary" onclick="addNavItem()">
                    <i class="fas fa-plus mr-1"></i> Thêm mục
                </button>
                <small class="text-muted ml-2">
                    <code>setting:link_facebook</code> = lấy giá trị link Facebook từ cài đặt trên.
                    Active path: nhập path URL để tô sáng mục đang xem (ví dụ <code>nap-coin</code>, <code>/</code>).
                </small>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-sliders-h mr-1"></i> Cấu hình khác</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label for="phone_otp">Số điện thoại nhận OTP (đổi SĐT / quên mật khẩu)</label>
                    <input type="text" id="phone_otp" name="phone_otp" class="form-control"
                        value="{{ old('phone_otp', $settings['phone_otp']) }}" maxlength="50">
                    <small class="form-text text-muted">
                        Hiển thị ở: trang "Quên mật khẩu" và mục "Đổi số điện thoại" trong trang tài khoản
                        (số điện thoại để khách nhắn tin xác nhận).
                    </small>
                </div>

                <div class="form-group">
                    <label for="footer1">Nội dung footer chung (SEO, trang Đại lý...)</label>
                    <input type="text" id="footer1" name="footer1" class="form-control"
                        value="{{ old('footer1', $settings['footer1']) }}" maxlength="250">
                    <small class="form-text text-muted">
                        Hiển thị ở: tiêu đề/mô tả SEO (thẻ &lt;title&gt;, meta description, Open Graph) của trang
                        Nạp thẻ và trang Đại lý, và footer trang Đại lý.
                    </small>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="max_acc_len">Độ dài tối đa tên tài khoản</label>
                        <input type="number" id="max_acc_len" name="max_acc_len" class="form-control"
                            value="{{ old('max_acc_len', $settings['max_acc_len']) }}" min="1" max="100">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="min_acc_len">Độ dài tối thiểu tên tài khoản</label>
                        <input type="number" id="min_acc_len" name="min_acc_len" class="form-control"
                            value="{{ old('min_acc_len', $settings['min_acc_len']) }}" min="1" max="100">
                    </div>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu cài đặt
                </button>
            </div>
        </div>
    </form>
@endsection

@section('scripts')
    <script>
        @if($saved)
            setTimeout(function () {
                var alertEl = document.getElementById('general-alert');
                if (alertEl && window.jQuery) {
                    jQuery(alertEl).fadeOut(400, function () { jQuery(this).alert('close'); });
                } else if (alertEl) {
                    alertEl.style.display = 'none';
                }
            }, 4000);
        @endif

        /* ── Upload file & copy link ─────────────────────────────────────── */
        (function () {
            var CHUNK_SIZE = {{ \App\Services\FileUploadService::CHUNK_SIZE }};
            var MAX_SIZE = {{ \App\Services\FileUploadService::MAX_SIZE }};
            var CHUNK_URL = @json(route('admin.files.chunk'));
            var DELETE_URL = @json(route('admin.files.delete'));
            var TOKEN = @json(csrf_token());

            var input = document.getElementById('fu-input');
            var startBtn = document.getElementById('fu-start');
            var cancelBtn = document.getElementById('fu-cancel');
            var progWrap = document.getElementById('fu-progress-wrap');
            var prog = document.getElementById('fu-progress');
            var statusEl = document.getElementById('fu-status');
            var errorEl = document.getElementById('fu-error');
            var resultEl = document.getElementById('fu-result');
            var tbody = document.getElementById('fu-tbody');
            var uploading = false, cancelled = false, rowSeq = 0;

            function fmtSize(b) {
                if (b >= 1073741824) return (b / 1073741824).toFixed(2) + ' GB';
                if (b >= 1048576) return (b / 1048576).toFixed(1) + ' MB';
                if (b >= 1024) return Math.round(b / 1024) + ' KB';
                return b + ' B';
            }

            function esc(s) {
                return String(s).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
            }

            function showError(msg) {
                errorEl.textContent = msg;
                errorEl.classList.remove('d-none');
            }

            function setProgress(pct, text) {
                prog.style.width = pct + '%';
                prog.textContent = pct + '%';
                statusEl.textContent = text || '';
            }

            function randomId() {
                var a = new Uint8Array(16);
                (window.crypto || window.msCrypto).getRandomValues(a);
                return Array.prototype.map.call(a, function (x) { return ('0' + x.toString(16)).slice(-2); }).join('');
            }

            function sendChunk(fd) {
                return fetch(CHUNK_URL, {
                    method: 'POST', body: fd, credentials: 'same-origin',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                }).then(function (res) {
                    if (res.status === 419) throw { fatal: true, message: 'Phiên đăng nhập đã hết hạn, hãy tải lại trang (F5) rồi upload lại.' };
                    return res.json().catch(function () { return null; }).then(function (data) {
                        if (res.ok && data && data.ok) return data;
                        if (data && data.message) throw { fatal: res.status === 422, message: data.message };
                        throw { fatal: false, message: 'Lỗi server (' + res.status + ').' };
                    });
                });
            }

            // Thử lại tối đa 3 lần khi lỗi mạng / lỗi server tạm thời.
            function sendWithRetry(fd, attempt) {
                return sendChunk(fd).catch(function (err) {
                    if (err && err.fatal) throw err;
                    if (attempt >= 3 || cancelled) throw err;
                    return new Promise(function (r) { setTimeout(r, 1500 * attempt); }).then(function () {
                        return sendWithRetry(fd, attempt + 1);
                    });
                });
            }

            function addRow(file) {
                var empty = tbody.querySelector('.fu-empty');
                if (empty) empty.remove();
                var id = 'fu-new-' + (rowSeq++);
                var d = new Date();
                var pad = function (n) { return ('0' + n).slice(-2); };
                var tr = document.createElement('tr');
                tr.setAttribute('data-name', file.name);
                tr.innerHTML =
                    '<td class="text-break">' + esc(file.name) + ' <span class="badge badge-success">mới</span></td>' +
                    '<td>' + fmtSize(file.size) + '</td>' +
                    '<td>' + pad(d.getDate()) + '/' + pad(d.getMonth() + 1) + '/' + d.getFullYear() + ' ' + pad(d.getHours()) + ':' + pad(d.getMinutes()) + '</td>' +
                    '<td><div class="input-group input-group-sm"><input type="text" id="' + id + '" class="form-control" value="' + esc(file.url) + '" readonly>' +
                    '<div class="input-group-append"><button type="button" class="btn btn-outline-secondary fu-copy" data-target="' + id + '" title="Copy link"><i class="fas fa-copy"></i></button></div></div></td>' +
                    '<td class="text-center"><button type="button" class="btn btn-xs btn-danger fu-delete" title="Xoá file"><i class="fas fa-trash"></i></button></td>';
                tbody.insertBefore(tr, tbody.firstChild);
            }

            function finish() {
                uploading = false;
                startBtn.disabled = false;
                input.disabled = false;
                cancelBtn.classList.add('d-none');
                prog.classList.remove('progress-bar-animated');
            }

            startBtn.addEventListener('click', function () {
                var file = input.files && input.files[0];
                errorEl.classList.add('d-none');
                resultEl.classList.add('d-none');

                if (!file) { showError('Vui lòng chọn file trước.'); return; }
                if (file.size <= 0) { showError('File rỗng.'); return; }
                if (file.size > MAX_SIZE) { showError('File vượt quá giới hạn ' + fmtSize(MAX_SIZE) + '.'); return; }

                var total = Math.ceil(file.size / CHUNK_SIZE);
                var uploadId = randomId();
                var started = Date.now();

                uploading = true; cancelled = false;
                startBtn.disabled = true; input.disabled = true;
                cancelBtn.classList.remove('d-none');
                progWrap.classList.remove('d-none');
                prog.classList.add('progress-bar-animated');
                prog.classList.remove('bg-danger');
                setProgress(0, 'Đang upload ' + file.name + ' (' + fmtSize(file.size) + ')...');

                var index = 0;
                (function next() {
                    if (cancelled) { finish(); setProgress(0, 'Đã huỷ upload.'); return; }

                    var fd = new FormData();
                    fd.append('_token', TOKEN);
                    fd.append('upload_id', uploadId);
                    fd.append('chunk_index', index);
                    fd.append('total_chunks', total);
                    fd.append('file_name', file.name);
                    fd.append('total_size', file.size);
                    fd.append('chunk', file.slice(index * CHUNK_SIZE, Math.min(file.size, (index + 1) * CHUNK_SIZE)), 'chunk');

                    sendWithRetry(fd, 1).then(function (data) {
                        index++;
                        var sent = Math.min(file.size, index * CHUNK_SIZE);
                        var secs = Math.max(1, (Date.now() - started) / 1000);
                        setProgress(Math.floor(sent * 100 / file.size),
                            fmtSize(sent) + ' / ' + fmtSize(file.size) + ' - ' + fmtSize(sent / secs) + '/s');

                        if (data.done) {
                            finish();
                            setProgress(100, 'Hoàn tất.');
                            document.getElementById('fu-result-name').textContent = data.file.name;
                            document.getElementById('fu-result-link').value = data.file.url;
                            resultEl.classList.remove('d-none');
                            addRow(data.file);
                            input.value = '';
                            return;
                        }
                        next();
                    }).catch(function (err) {
                        finish();
                        prog.classList.add('bg-danger');
                        showError((err && err.message) ? err.message : 'Upload thất bại, vui lòng thử lại.');
                    });
                })();
            });

            cancelBtn.addEventListener('click', function () { cancelled = true; });

            window.addEventListener('beforeunload', function (e) {
                if (uploading) { e.preventDefault(); e.returnValue = ''; }
            });

            function copyText(text, btn) {
                var done = function () {
                    var old = btn.innerHTML;
                    btn.innerHTML = '<i class="fas fa-check"></i>' + (btn.textContent.trim() ? ' Đã copy' : '');
                    btn.classList.add('btn-success');
                    setTimeout(function () { btn.innerHTML = old; btn.classList.remove('btn-success'); }, 1500);
                };
                if (navigator.clipboard && window.isSecureContext) {
                    navigator.clipboard.writeText(text).then(done, function () { fallback(); });
                } else {
                    fallback();
                }
                function fallback() {
                    var ta = document.createElement('textarea');
                    ta.value = text; ta.style.position = 'fixed'; ta.style.opacity = '0';
                    document.body.appendChild(ta); ta.select();
                    try { document.execCommand('copy'); done(); } catch (e) { window.prompt('Copy link:', text); }
                    ta.remove();
                }
            }

            document.getElementById('file-upload-card').addEventListener('click', function (e) {
                var copyBtn = e.target.closest('.fu-copy');
                if (copyBtn) {
                    var el = document.getElementById(copyBtn.getAttribute('data-target'));
                    if (el) copyText(el.value, copyBtn);
                    return;
                }

                var delBtn = e.target.closest('.fu-delete');
                if (delBtn) {
                    var tr = delBtn.closest('tr');
                    var name = tr.getAttribute('data-name');
                    if (!window.confirm('Xoá file "' + name + '"?\nLink tới file này sẽ không tải được nữa.')) return;

                    var fd = new FormData();
                    fd.append('_token', TOKEN);
                    fd.append('name', name);
                    delBtn.disabled = true;
                    fetch(DELETE_URL, {
                        method: 'POST', body: fd, credentials: 'same-origin',
                        headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                    }).then(function (res) {
                        return res.json().catch(function () { return { ok: false, message: 'Lỗi server (' + res.status + ').' }; });
                    }).then(function (data) {
                        if (data.ok) { tr.remove(); return; }
                        delBtn.disabled = false;
                        window.alert(data.message || 'Không xoá được file.');
                    }).catch(function () {
                        delBtn.disabled = false;
                        window.alert('Không xoá được file (lỗi mạng).');
                    });
                }
            });
        })();

        /* ── Nav items editor ────────────────────────────────────────────── */
        var navItems = (function () {
            try { return JSON.parse(document.getElementById('nav_items_input').value) || []; }
            catch (e) { return []; }
        })();

        function esc(str) {
            return String(str ?? '')
                .replace(/&/g,'&amp;').replace(/</g,'&lt;')
                .replace(/>/g,'&gt;').replace(/"/g,'&quot;');
        }

        function syncNav() {
            document.getElementById('nav_items_input').value = JSON.stringify(navItems);
        }

        function renderNav() {
            var tbody = document.getElementById('nav-tbody');
            tbody.innerHTML = '';
            navItems.forEach(function (item, idx) {
                var tr = document.createElement('tr');
                tr.innerHTML =
                    '<td style="white-space:nowrap">' +
                        '<button type="button" class="btn btn-xs btn-default px-1" onclick="moveNav(' + idx + ',-1)"' + (idx === 0 ? ' disabled' : '') + '>▲</button>' +
                        '<button type="button" class="btn btn-xs btn-default px-1" onclick="moveNav(' + idx + ',1)"' + (idx === navItems.length - 1 ? ' disabled' : '') + '>▼</button>' +
                    '</td>' +
                    '<td><input class="form-control form-control-sm" value="' + esc(item.label) + '" oninput="navItems[' + idx + '].label=this.value;syncNav()"></td>' +
                    '<td><input class="form-control form-control-sm" value="' + esc(item.icon) + '" placeholder="fa-solid fa-house" oninput="navItems[' + idx + '].icon=this.value;syncNav()"></td>' +
                    '<td><input class="form-control form-control-sm" value="' + esc(item.url) + '" placeholder="/path hoặc setting:link_facebook" oninput="navItems[' + idx + '].url=this.value;syncNav()"></td>' +
                    '<td><input class="form-control form-control-sm" value="' + esc(item.url_match) + '" placeholder="nap-coin" oninput="navItems[' + idx + '].url_match=this.value;syncNav()"></td>' +
                    '<td style="text-align:center"><input type="checkbox"' + (item.target === '_blank' ? ' checked' : '') + ' onchange="navItems[' + idx + '].target=this.checked?\'_blank\':\'\';syncNav()"></td>' +
                    '<td><button type="button" class="btn btn-xs btn-danger" onclick="deleteNav(' + idx + ')">✕</button></td>';
                tbody.appendChild(tr);
            });
            syncNav();
        }

        function moveNav(idx, dir) {
            var ni = idx + dir;
            if (ni < 0 || ni >= navItems.length) return;
            var tmp = navItems[idx]; navItems[idx] = navItems[ni]; navItems[ni] = tmp;
            renderNav();
        }

        function deleteNav(idx) {
            navItems.splice(idx, 1);
            renderNav();
        }

        function addNavItem() {
            navItems.push({ label: '', icon: 'fa-solid fa-link', url: '/', url_match: '', target: '' });
            renderNav();
        }

        renderNav();
    </script>
@endsection
