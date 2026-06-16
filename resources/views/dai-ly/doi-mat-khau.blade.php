@extends('layouts.daily')

{{-- Thông tin tài khoản đại lý - tính năng mới (không có trong code gốc) -> /dai-ly-doi-mat-khau --}}
{{-- Gồm 2 phần: đổi mật khẩu đăng nhập + cập nhật thông tin liên hệ (Họ và tên, SĐT, Zalo, Facebook) --}}

@section('title', 'Thông tin tài khoản')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Thông tin tài khoản</span></li>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-6">
            {{-- Cập nhật thông tin liên hệ --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-id-card mr-1"></i> Thông tin đại lý</h3>
                </div>
                <div class="card-body">
                    <div id="infoMess" class="mb-2"></div>

                    <div class="form-group">
                        <label>Họ và tên</label>
                        <input type="text" id="ho_va_ten" class="form-control" placeholder="Họ và tên" maxlength="250" value="{{ $daiLy->HoVaTen }}">
                    </div>
                    <div class="form-group">
                        <label>Số điện thoại</label>
                        <input type="text" id="phone" class="form-control" placeholder="Số điện thoại" maxlength="50" value="{{ $daiLy->Phone }}">
                    </div>
                    <div class="form-group">
                        <label>Zalo</label>
                        <input type="text" id="zalo" class="form-control" placeholder="Số Zalo" maxlength="50" value="{{ $daiLy->Zalo }}">
                    </div>
                    <div class="form-group">
                        <label>Facebook</label>
                        <input type="text" id="facebook" class="form-control" placeholder="Link Facebook" maxlength="250" value="{{ $daiLy->Facebook }}">
                    </div>

                    <button type="button" id="btnCapNhatInfo" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Cập nhật thông tin
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            {{-- Đổi mật khẩu đăng nhập --}}
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-key mr-1"></i> Đổi mật khẩu đăng nhập</h3>
                </div>
                <div class="card-body">
                    <div id="passMess" class="mb-2"></div>

                    <div class="form-group">
                        <label>Mật khẩu hiện tại</label>
                        <input type="password" id="old_pass" class="form-control" placeholder="Mật khẩu hiện tại" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label>Mật khẩu mới</label>
                        <input type="password" id="new_pass" class="form-control" placeholder="Mật khẩu mới (6 - 16 ký tự)" maxlength="16">
                    </div>
                    <div class="form-group">
                        <label>Xác nhận mật khẩu mới</label>
                        <input type="password" id="confirm_pass" class="form-control" placeholder="Nhập lại mật khẩu mới" maxlength="16">
                    </div>

                    <button type="button" id="btnDoiMatKhau" class="btn btn-success">
                        <i class="fas fa-save mr-1"></i> Đổi mật khẩu
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#btnCapNhatInfo').on('click', function () {
                var $btn = $(this).prop('disabled', true);

                $.post('{{ url('/dai-ly-doi-mat-khau/thong-tin') }}', {
                    ho_va_ten: $('#ho_va_ten').val(),
                    phone: $('#phone').val(),
                    zalo: $('#zalo').val(),
                    facebook: $('#facebook').val()
                }).done(function (result) {
                    flashMessage($('#infoMess'), 'mb-2', result.success, result.message);
                }).fail(function () {
                    flashMessage($('#infoMess'), 'mb-2', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });

            $('#btnDoiMatKhau').on('click', function () {
                var $btn = $(this).prop('disabled', true);

                $.post('{{ url('/dai-ly-doi-mat-khau') }}', {
                    old_pass: $('#old_pass').val(),
                    new_pass: $('#new_pass').val(),
                    confirm_pass: $('#confirm_pass').val()
                }).done(function (result) {
                    flashMessage($('#passMess'), 'mb-2', result.success, result.message);

                    if (result.success) {
                        $('#old_pass, #new_pass, #confirm_pass').val('');
                    }
                }).fail(function () {
                    flashMessage($('#passMess'), 'mb-2', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                }).always(function () {
                    $btn.prop('disabled', false);
                });
            });
        });
    </script>
@endsection
