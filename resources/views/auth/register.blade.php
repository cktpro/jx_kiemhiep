@extends('layouts.auth')

{{-- port từ DangKyV2.aspx (đăng ký tài khoản game) --}}

@section('title', 'Đăng ký - Võ Lâm 1 Mobi')

@section('seo_page', 'register')

@section('content')
    {{-- (tính năng mới, không có trong code gốc) Logo + giao diện card mới --}}
    <img src="/img/logo.webp" alt="Võ Lâm 1 Mobi" class="auth-logo">

    <h3>Đăng Ký Tài Khoản</h3>

    <div class="col-12" id="FormRegister">
        <div id="regMess" class="text-danger mb-2"></div>

        <div class="col-12 mb-3">
            <label style="font-size: smaller" for="username">Tên tài khoản</label>
            <div class="input-icon-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="username" name="username" class="form-control" placeholder="Tài khoản phải từ 6 đến 16 ký tự" maxlength="16">
            </div>
        </div>

        <div class="col-12 mb-3">
            <label style="font-size: smaller" for="phone">Số điện thoại</label>
            <div class="input-icon-group">
                <i class="fa-solid fa-phone"></i>
                <input type="text" id="phone" name="phone" class="form-control" placeholder="Nhập vào số điện thoại">
            </div>
        </div>

        <div class="col-12 mb-2">
            <label style="font-size: smaller" for="password">Mật khẩu</label>
            <div class="input-icon-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="Mật khẩu phải từ 6 đến 16 ký tự" maxlength="16">
            </div>
        </div>

        <div class="col-md-12 col-lg-12 col-xl-12 margin_bottom_20">
            <button type="button" id="btnRegister" class="btn_login">Đăng Ký Ngay</button>
        </div>

        <div class="col-12 margin_top_20 mb-2" style="text-align: center">
            <p class="text_dkm">Bạn đã có tài khoản?</p>
            <a href="/dang-nhap" class="btn_dkm">Đăng Nhập Ngay</a>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            function doRegister() {
                var username = $('#username').val().trim();
                var phone = $('#phone').val().trim();
                var password = $('#password').val().trim();

                $('#regMess').text('');

                if (phone.length < 10) {
                    $('#regMess').text('Số điện thoại không hợp lệ');
                    return;
                }
                if (username.length < 6 || username.length > 16) {
                    $('#regMess').text('Tên tài khoản phải có độ dài 6 đến 16 ký tự');
                    return;
                }
                if (password.length < 6 || password.length > 16) {
                    $('#regMess').text('Mật khẩu phải có độ dài 6 đến 16 ký tự');
                    return;
                }

                $('#btnRegister').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '/dang-ky',
                    data: { username: username, phone: phone, password: password },
                    success: function (result) {
                        if (result.success) {
                            $('#username').val('');
                            $('#phone').val('');
                            $('#password').val('');
                            $('#regMess').removeClass('text-danger').addClass('text-success').text(result.message);
                        } else {
                            $('#regMess').removeClass('text-success').addClass('text-danger').text(result.message);
                        }
                    },
                    error: function () {
                        $('#regMess').text('Có lỗi xảy ra, vui lòng thử lại!');
                    },
                    complete: function () {
                        $('#btnRegister').prop('disabled', false);
                    }
                });
            }

            $('#btnRegister').on('click', doRegister);
        });
    </script>
@endsection
