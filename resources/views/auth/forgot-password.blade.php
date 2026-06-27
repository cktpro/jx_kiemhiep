@extends('layouts.auth')

{{--
    port từ QuenMatKhauV2.aspx (route "quenmk2")

    Lưu ý: trang gốc dùng layout riêng (skin jx1m) khác với DangNhapV2/DangKyV2.
    Ở đây dùng chung layout "auth" cho gọn; phần nội dung/luồng nghiệp vụ
    (hướng dẫn soạn SMS đổi mật khẩu) được giữ nguyên 100%.
--}}

@section('title', 'Quên mật khẩu - Võ Lâm 1 Mobi')

@section('content')
    <h3>Quên Mật Khẩu</h3>

    <div class="col-12" id="FormForgot">
        <div id="forgotError" class="text-danger mb-2"></div>
        <div id="forgotStep1" class="mb-2"></div>

        <div class="col-12 mb-2">
            <label style="font-size: smaller" for="acc_name">Tên tài khoản</label>
            <input type="text" id="acc_name" name="acc_name" class="form-control" placeholder="Tên tài khoản" maxlength="16">
        </div>

        <div class="col-12 mb-2">
            <label style="font-size: smaller" for="old_phone">SĐT đã đăng ký</label>
            <input type="text" id="old_phone" name="old_phone" class="form-control" placeholder="SĐT đã đăng ký">
        </div>

        <div class="col-12 mb-2">
            <label style="font-size: smaller" for="new_pass">Mật khẩu mới</label>
            <input type="password" id="new_pass" name="new_pass" class="form-control" placeholder="Mật khẩu mới" maxlength="16">
        </div>

        <div class="col-12 mb-2">
            <label style="font-size: smaller" for="confirm_pass">Xác nhận mật khẩu</label>
            <input type="password" id="confirm_pass" name="confirm_pass" class="form-control" placeholder="Xác nhận mật khẩu" maxlength="16">
        </div>

        <div class="col-md-12 col-lg-12 col-xl-12 margin_bottom_20">
            <button type="button" id="btnGetCode" class="btn_login">Lấy mã xác nhận</button>
        </div>

        <div class="col-12 margin_top_20 mb-2" style="text-align: center">
            <a href="{{ site_setting('link_login') }}" class="btn_dkm">Đăng nhập</a>
            <a href="{{ site_setting('link_register') }}" class="btn_dkm">Đăng ký</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            function getCode() {
                var accName = $('#acc_name').val().trim();
                var oldPhone = $('#old_phone').val().trim();
                var newPass = $('#new_pass').val().trim();
                var confirmPass = $('#confirm_pass').val().trim();

                $('#forgotError').text('');
                $('#forgotStep1').html('');

                if (accName.length < 6 || accName.length > 16) {
                    $('#forgotError').text('Tên tài khoản từ 6 đến 16 ký tự!');
                    return;
                }
                if (newPass.length < 6 || newPass.length > 16) {
                    $('#forgotError').text('Mật khẩu từ 6 đến 16 ký tự!');
                    return;
                }
                if (newPass !== confirmPass) {
                    $('#forgotError').text('Mật khẩu xác nhận không khớp!');
                    return;
                }

                $('#btnGetCode').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '/quen-mat-khau',
                    data: {
                        acc_name: accName,
                        old_phone: oldPhone,
                        new_pass: newPass
                    },
                    success: function (result) {
                        if (result.success) {
                            $('#forgotStep1').html(result.html);
                        } else {
                            $('#forgotError').text(result.message);
                        }
                    },
                    error: function () {
                        $('#forgotError').text('Có lỗi xảy ra, vui lòng thử lại!');
                    },
                    complete: function () {
                        $('#btnGetCode').prop('disabled', false);
                    }
                });
            }

            $('#btnGetCode').on('click', getCode);
        });
    </script>
@endsection
