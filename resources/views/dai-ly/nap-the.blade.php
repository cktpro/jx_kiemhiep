@extends('layouts.daily')

{{-- port từ DaiLyNapThe.aspx + .aspx.cs (class DaiLyNapThe1) -> /dai-ly-nap-the --}}

@section('title', 'Quản lý nạp')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Quản lý nạp</span></li>
@endsection

@section('content')
    {{-- Thông tin tài khoản đại lý --}}
    <div class="row">
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-info"><i class="fas fa-user-tie"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Đại lý</span>
                    <span class="info-box-number">{{ $tenTaiKhoan }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-secondary"><i class="fas fa-phone"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Số điện thoại</span>
                    <span class="info-box-number">{{ $sodienthoai }}</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">KNB hiện có</span>
                    <span class="info-box-number">{{ number_format($iYuanbao) }} KNB</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-success"><i class="fas fa-money-bill-wave"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Tổng tiền nạp</span>
                    <span class="info-box-number">{{ number_format($history['totalNap']) }} VNĐ</span>
                </div>
            </div>
        </div>
        <div class="col-lg-4 col-6">
            <div class="info-box">
                <span class="info-box-icon bg-primary"><i class="fas fa-exchange-alt"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">Đã chuyển KIM</span>
                    <span class="info-box-number">{{ number_format($history['totalDaNap']) }} VNĐ</span>
                </div>
            </div>
        </div>
    </div>

    @if ($isAdmin != 0)
        <div class="mb-3">
            <a href="/tong-dai-ly" class="btn btn-outline-primary">
                <i class="fas fa-sitemap mr-1"></i> Quản lý Đại lý Tổng
            </a>
        </div>
    @endif

    @if ($message)
        <div id="topMess" class="alert {{ str_contains($message, 'không') || str_contains($message, 'lỗi') || str_contains($message, 'tồn tại') ? 'alert-danger' : 'alert-success' }}">
            {!! $message !!}
        </div>
    @endif

    <div class="row">
        <div class="col-md-6">
            {{-- Form tìm / đổi số điện thoại của tài khoản game --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-mobile-alt mr-1"></i> Tra cứu &amp; cập nhật số điện thoại</h3>
                </div>
                <div class="card-body">
                    <div id="sdtMess" class="mb-2"></div>
                    <div class="form-group">
                        <label>Tên tài khoản</label>
                        <input type="text" id="sdt_acc_name" class="form-control" placeholder="Tên tài khoản" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại mới</label>
                        <input type="text" id="sdt_phone" class="form-control" placeholder="Số điện thoại mới" maxlength="12">
                    </div>
                    <button type="button" id="btnTimSdt" class="btn btn-outline-secondary">Tra số hiện tại</button>
                    <button type="button" id="btnDoiSdt" class="btn btn-success">Cập nhật SĐT</button>
                </div>
            </div>

            {{-- Form xem / đổi mật khẩu cấp 2 (gộp 2 chức năng vào 1 div, tính năng mới, không có trong code gốc) --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key mr-1"></i> Mật khẩu cấp 2</h3>
                </div>
                <div class="card-body">
                    <div id="passMess" class="mb-2"></div>
                    <div class="form-group">
                        <label>Tên tài khoản</label>
                        <input type="text" id="pass_acc_name" class="form-control" placeholder="Tên tài khoản" maxlength="16">
                    </div>
                    <button type="button" id="btnXemPass" class="btn btn-outline-secondary">Xem mật khẩu</button>

                    <hr>

                    <div id="changePassMess" class="mb-2"></div>
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="text" id="change_pass_new_pass" class="form-control" placeholder="Mật khẩu mới (6 - 16 ký tự)" maxlength="16">
                    </div>
                    <button type="button" id="btnDoiMatKhauTK" class="btn btn-success">
                        <i class="fas fa-key mr-1"></i> Đổi mật khẩu
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            {{-- Form đăng ký nạp KNB cho tài khoản game --}}
            <div class="card card-success card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-paper-plane mr-1"></i> Nạp KNB cho tài khoản</h3>
                </div>
                <div class="card-body">
                    <div id="napMess" class="mb-2"></div>
                    <div class="form-group">
                        <label>Tên tài khoản</label>
                        <input type="text" id="nap_acc_name" class="form-control" placeholder="Tên tài khoản" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label>Mệnh giá</label>
                        <select id="nap_menh_gia" class="form-control">
                            <option value="50000">50.000 VNĐ</option>
                            <option value="100000">100.000 VNĐ</option>
                            <option value="200000">200.000 VNĐ</option>
                            <option value="300000">300.000 VNĐ</option>
                            <option value="400000">400.000 VNĐ</option>
                            <option value="500000">500.000 VNĐ</option>
                            <option value="1000000">1.000.000 VNĐ</option>
                            <option value="2000000">2.000.000 VNĐ</option>
                            <option value="3000000">3.000.000 VNĐ</option>
                            <option value="5000000">5.000.000 VNĐ</option>
                            <option value="10000000">10.000.000 VNĐ</option>
                        </select>
                    </div>
                    <button type="button" id="btnDangKyNap" class="btn btn-success">
                        <i class="fas fa-paper-plane mr-1"></i> Đăng ký
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Lịch sử nạp --}}
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history mr-1"></i> Lịch sử nạp</h3>
        </div>
        <div class="card-body">
            @if ($isAdmin != 0)
                <form method="GET" action="/dai-ly-nap-the" class="form-row align-items-end mb-3">
                    <div class="form-group col-md-4 mb-0">
                        <label>Từ ngày</label>
                        <input type="text" name="today" class="form-control" value="{{ $today }}" placeholder="DD/MM/YYYY">
                        <small class="form-text text-muted">Định dạng: DD/MM/YYYY (ví dụ: 14/06/2026)</small>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <label>Đến ngày</label>
                        <input type="text" name="end_date" class="form-control" value="{{ $endDate }}" placeholder="DD/MM/YYYY">
                        <small class="form-text text-muted">Định dạng: DD/MM/YYYY (ví dụ: 14/06/2026)</small>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-outline-secondary btn-block">
                            <i class="fas fa-filter mr-1"></i> Lọc theo ngày
                        </button>
                        <small class="form-text text-muted">&nbsp;</small>
                    </div>
                </form>
            @endif
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Tài khoản</th>
                        <th>Số tiền</th>
                        <th>KNB</th>
                        <th>KNB KM</th>
                        <th>Thời gian</th>
                        <th>Trạng thái</th>
                        <th>Nội dung</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($history['items'] as $item)
                        <tr>
                            <td>{{ $item->ID }}</td>
                            <td>{{ $item->AccountGammer }}</td>
                            <td>{{ number_format((int) $item->SoTien) }}</td>
                            <td>{{ $item->SoKNB }}</td>
                            <td>{{ $item->SoKNBKM }}</td>
                            <td>{{ optional($item->DateNap)->format('d/m/Y H:i') }}</td>
                            <td>
                                @if ((int) $item->TrangThai === 4)
                                    {{-- (tính năng mới, không có trong code gốc) TrangThai = 4: đã hủy --}}
                                    <span class="badge badge-secondary">Đã hủy</span>
                                @elseif ((int) $item->TrangThai > 0)
                                    <span class="badge badge-success">Đã xử lý</span>
                                @else
                                    <span class="badge badge-warning">Chờ xử lý</span>
                                @endif
                            </td>
                            <td>{{ $item->NoiDung }}</td>
                            <td>
                                @if ((int) $item->TrangThai === 0)
                                    <a href="/dai-ly-nap-the?id={{ $item->ID }}" class="btn btn-sm btn-success">Xác nhận chuyển KIM</a>
                                    {{-- (tính năng mới, không có trong code gốc) Hủy khoản nạp đang chờ --}}
                                    <a href="/dai-ly-nap-the?cancel_id={{ $item->ID }}" class="btn btn-sm btn-danger" onclick="return confirm('Bạn có chắc muốn hủy khoản nạp này?')">Hủy</a>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Không có dữ liệu</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer d-flex justify-content-center">
            {{-- (tính năng mới, không có trong code gốc) Phân trang lịch sử nạp --}}
            {{ $history['items']->links('partials.pagination') }}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#btnTimSdt').on('click', function () {
                var $btn = $(this).prop('disabled', true);

                $.post('{{ url('/dai-ly-nap-the/tim-sdt') }}', {
                    acc_name: $('#sdt_acc_name').val()
                }).done(function (result) {
                    flashMessage($('#sdtMess'), 'mb-2', result.success, result.message || ('Số điện thoại hiện tại: ' + (result.phone || '')));

                    if (result.success && result.phone) {
                        $('#sdt_phone').val(result.phone);
                    }
                }).fail(function () {
                    flashMessage($('#sdtMess'), 'mb-2', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });

            $('#btnDoiSdt').on('click', function () {
                var $btn = $(this).prop('disabled', true);

                $.post('{{ url('/dai-ly-nap-the/doi-sdt') }}', {
                    acc_name: $('#sdt_acc_name').val(),
                    phone: $('#sdt_phone').val()
                }).done(function (result) {
                    flashMessage($('#sdtMess'), 'mb-2', result.success, result.message);
                }).fail(function () {
                    flashMessage($('#sdtMess'), 'mb-2', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });

            $('#btnXemPass').on('click', function () {
                var $btn = $(this).prop('disabled', true);

                $.post('{{ url('/dai-ly-nap-the/xem-pass') }}', {
                    acc_name: $('#pass_acc_name').val()
                }).done(function (result) {
                    flashMessage($('#passMess'), 'mb-2', result.success, result.message);
                }).fail(function () {
                    flashMessage($('#passMess'), 'mb-2', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });

            $('#btnDangKyNap').on('click', function () {
                var $btn = $(this).prop('disabled', true);
                var $sel = $('#nap_menh_gia');

                $.post('{{ url('/dai-ly-nap-the/dang-ky-nap') }}', {
                    acc_name: $('#nap_acc_name').val(),
                    menh_gia: $sel.val(),
                    menh_gia_text: $sel.find('option:selected').text()
                }).done(function (result) {
                    flashMessage($('#napMess'), 'mb-2', result.success, result.message);

                    if (result.success) {
                        $('#nap_acc_name').val('');
                    }
                }).fail(function () {
                    flashMessage($('#napMess'), 'mb-2', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });

            $('#btnDoiMatKhauTK').on('click', function () {
                var $btn = $(this).prop('disabled', true);

                $.post('{{ url('/dai-ly-nap-the/doi-mat-khau-tk') }}', {
                    acc_name: $('#pass_acc_name').val(),
                    new_pass: $('#change_pass_new_pass').val()
                }).done(function (result) {
                    flashMessage($('#changePassMess'), 'mb-2', result.success, result.message);

                    if (result.success) {
                        $('#change_pass_new_pass').val('');
                    }
                }).fail(function () {
                    flashMessage($('#changePassMess'), 'mb-2', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });
        });
    </script>
@endsection
