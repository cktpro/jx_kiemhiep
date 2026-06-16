@extends('admin.layout')

{{--
    Cài đặt nội dung Footer (logo, thông tin sản phẩm, liên kết nhanh) hiển
    thị ở cuối Trang chủ / Trang tin tức - tính năng mới, không có trang
    tương ứng trong Admin6 gốc. Dữ liệu đọc/ghi qua App\Services\FooterSettings
    (file JSON, không cần thêm bảng vào database SQL Server).
--}}

@section('title', 'Cài đặt Footer')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Cài đặt Footer</span></li>
@endsection

@section('content')
    @if($saved)
        <div id="footer-alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Đã lưu cài đặt Footer.
        </div>
    @endif

    <form action="{{ route('admin.footer.save') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- Logo --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-image mr-1"></i> Logo</h3>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <label class="col-sm-2 col-form-label">Logo hiện tại</label>
                    <div class="col-sm-10">
                        <div>
                            <img src="{{ $settings['logo'] }}" alt="{{ $settings['logo_alt'] }}" style="max-width: 240px; max-height: 120px;" class="img-thumbnail">
                        </div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="logo" class="col-sm-2 col-form-label">Đổi logo</label>
                    <div class="col-sm-10">
                        <input type="file" name="logo" id="logo" class="form-control-file" accept=".jpg,.jpeg,.png,.webp,.gif,.svg">
                        <small class="form-text text-muted">Hỗ trợ JPG, PNG, WEBP, GIF, SVG. Tối đa 5MB. Bỏ trống nếu không đổi logo.</small>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="logo_alt" class="col-sm-2 col-form-label">Văn bản thay thế (alt)</label>
                    <div class="col-sm-10">
                        <input type="text" name="logo_alt" id="logo_alt" class="form-control" value="{{ $settings['logo_alt'] }}">
                    </div>
                </div>
            </div>
        </div>

        {{-- Thông tin sản phẩm --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-info-circle mr-1"></i> Thông tin sản phẩm</h3>
            </div>
            <div class="card-body">
                <div id="info-lines">
                    @foreach($settings['info_lines'] as $i => $line)
                        <div class="form-row align-items-end info-line mb-2" data-index="{{ $i }}">
                            <div class="col-sm-5">
                                <label>Nhãn</label>
                                <input type="text" name="info_lines[{{ $i }}][label]" class="form-control" value="{{ $line['label'] }}" placeholder="Ví dụ: Thông tin sản phẩm:">
                            </div>
                            <div class="col-sm-5">
                                <label>Nội dung</label>
                                <input type="text" name="info_lines[{{ $i }}][value]" class="form-control" value="{{ $line['value'] }}" placeholder="Ví dụ: Sơn Hà Xã Tắc">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger btn-block remove-row">
                                    <i class="fas fa-trash"></i> Xoá
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-info-line" class="btn btn-secondary">
                    <i class="fas fa-plus mr-1"></i> Thêm dòng
                </button>
            </div>
        </div>

        {{-- Liên kết nhanh --}}
        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-link mr-1"></i> Liên kết nhanh</h3>
            </div>
            <div class="card-body">
                <div id="links">
                    @foreach($settings['links'] as $i => $link)
                        <div class="form-row align-items-end link-row mb-2" data-index="{{ $i }}">
                            <div class="col-sm-3">
                                <label>Icon (FontAwesome)</label>
                                <input type="text" name="links[{{ $i }}][icon]" class="form-control" value="{{ $link['icon'] }}" placeholder="Ví dụ: fa-solid fa-download">
                            </div>
                            <div class="col-sm-3">
                                <label>Nhãn</label>
                                <input type="text" name="links[{{ $i }}][label]" class="form-control" value="{{ $link['label'] }}" placeholder="Ví dụ: Tải game">
                            </div>
                            <div class="col-sm-4">
                                <label>Đường dẫn</label>
                                <input type="text" name="links[{{ $i }}][url]" class="form-control" value="{{ $link['url'] }}" placeholder="https://...">
                            </div>
                            <div class="col-sm-2">
                                <button type="button" class="btn btn-danger btn-block remove-row">
                                    <i class="fas fa-trash"></i> Xoá
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="add-link" class="btn btn-secondary">
                    <i class="fas fa-plus mr-1"></i> Thêm liên kết
                </button>
                <small class="form-text text-muted">
                    Tên class icon FontAwesome, ví dụ: <code>fa-solid fa-download</code>, <code>fa-solid fa-circle-info</code>,
                    <code>fa-solid fa-user-group</code>, <code>fa-brands fa-facebook</code>.
                </small>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fas fa-save mr-1"></i> Lưu cài đặt
        </button>
    </form>
@endsection

@section('scripts')
    <script>
        (function () {
            // Mẫu HTML cho 1 dòng "Thông tin sản phẩm"
            function infoLineHtml(index) {
                return '<div class="form-row align-items-end info-line mb-2" data-index="' + index + '">'
                    + '<div class="col-sm-5">'
                    + '<label>Nhãn</label>'
                    + '<input type="text" name="info_lines[' + index + '][label]" class="form-control" placeholder="Ví dụ: Thông tin sản phẩm:">'
                    + '</div>'
                    + '<div class="col-sm-5">'
                    + '<label>Nội dung</label>'
                    + '<input type="text" name="info_lines[' + index + '][value]" class="form-control" placeholder="Ví dụ: Sơn Hà Xã Tắc">'
                    + '</div>'
                    + '<div class="col-sm-2">'
                    + '<button type="button" class="btn btn-danger btn-block remove-row"><i class="fas fa-trash"></i> Xoá</button>'
                    + '</div>'
                    + '</div>';
            }

            // Mẫu HTML cho 1 dòng "Liên kết nhanh"
            function linkRowHtml(index) {
                return '<div class="form-row align-items-end link-row mb-2" data-index="' + index + '">'
                    + '<div class="col-sm-3">'
                    + '<label>Icon (FontAwesome)</label>'
                    + '<input type="text" name="links[' + index + '][icon]" class="form-control" placeholder="Ví dụ: fa-solid fa-download">'
                    + '</div>'
                    + '<div class="col-sm-3">'
                    + '<label>Nhãn</label>'
                    + '<input type="text" name="links[' + index + '][label]" class="form-control" placeholder="Ví dụ: Tải game">'
                    + '</div>'
                    + '<div class="col-sm-4">'
                    + '<label>Đường dẫn</label>'
                    + '<input type="text" name="links[' + index + '][url]" class="form-control" placeholder="https://...">'
                    + '</div>'
                    + '<div class="col-sm-2">'
                    + '<button type="button" class="btn btn-danger btn-block remove-row"><i class="fas fa-trash"></i> Xoá</button>'
                    + '</div>'
                    + '</div>';
            }

            function reindex(container, prefix) {
                var rows = container.children;
                for (var i = 0; i < rows.length; i++) {
                    rows[i].setAttribute('data-index', i);
                    var inputs = rows[i].querySelectorAll('input');
                    inputs.forEach(function (input) {
                        input.name = input.name.replace(/\[\d+\]/, '[' + i + ']');
                    });
                }
            }

            var infoContainer = document.getElementById('info-lines');
            var linksContainer = document.getElementById('links');

            document.getElementById('add-info-line').addEventListener('click', function () {
                var div = document.createElement('div');
                div.innerHTML = infoLineHtml(infoContainer.children.length);
                infoContainer.appendChild(div.firstElementChild);
            });

            document.getElementById('add-link').addEventListener('click', function () {
                var div = document.createElement('div');
                div.innerHTML = linkRowHtml(linksContainer.children.length);
                linksContainer.appendChild(div.firstElementChild);
            });

            document.addEventListener('click', function (e) {
                if (e.target.closest('.remove-row')) {
                    var row = e.target.closest('.info-line, .link-row');
                    if (!row) {
                        return;
                    }
                    var container = row.parentElement;
                    var prefix = row.classList.contains('info-line') ? 'info_lines' : 'links';
                    row.remove();
                    reindex(container, prefix);
                }
            });
        })();

        @if($saved)
            setTimeout(function () {
                var alertEl = document.getElementById('footer-alert');
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
