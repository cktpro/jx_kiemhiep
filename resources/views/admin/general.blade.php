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
                        và link "Hỗ trợ" / "Nhóm Zalo" ở footer trang chủ.
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
