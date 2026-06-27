@extends('layouts.auth')

{{-- port từ DangNhapV2.aspx (đăng nhập tài khoản game / đăng nhập đại lý) --}}

@section('title', 'Đăng nhập - Võ Lâm 1 Mobi')

@section('seo_page', 'login')

@section('content')
    {{-- (tính năng mới, không có trong code gốc) Logo + giao diện card mới --}}
    <img src="/img/logo.webp" alt="Võ Lâm 1 Mobi" class="auth-logo">

    <h3>{{ $isDaiLy ? 'Đăng Nhập Đại Lý' : 'Đăng Nhập' }}</h3>

    <div class="col-12" id="FormLogin">
        <div id="loginMess" class="text-danger mb-2"></div>

        <div class="col-12 mb-3">
            <label style="font-size: smaller" for="username">Tên tài khoản</label>
            <div class="input-icon-group">
                <i class="fa-solid fa-user"></i>
                <input type="text" id="username" name="username" class="form-control" placeholder="Tài khoản" maxlength="{{ site_setting('max_acc_len') }}">
            </div>
        </div>

        <div class="col-12 mb-2">
            <label style="font-size: smaller" for="password">Mật khẩu</label>
            <div class="input-icon-group">
                <i class="fa-solid fa-lock"></i>
                <input type="password" id="password" name="password" class="form-control" placeholder="Mật khẩu" maxlength="{{ site_setting('max_acc_len') }}">
            </div>
        </div>

        <div class="col-md-12 col-lg-12 col-xl-12 margin_bottom_20">
            <button type="button" id="btnLogin" class="btn_login">Đăng Nhập Ngay</button>
        </div>

        <div class="col-12 margin_top_20 mb-2" style="text-align: center">
            @unless($isDaiLy)
                <p class="text_dkm"><a href="/quen-mat-khau">Quên mật khẩu?</a></p>
                <a href="{{ site_setting('link_register') }}" class="btn_dkm">Đăng Ký Ngay</a>
            @endunless
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            var submitUrl = @json($isDaiLy ? '/dai-ly' : '/dang-nhap');
            var minLen = @json((int) site_setting('min_acc_len'));
            var maxLen = @json((int) site_setting('max_acc_len'));

            function doLogin() {
                var username = $('#username').val().trim();
                var password = $('#password').val().trim();

                $('#loginMess').text('');

                if (username.length < minLen || username.length > maxLen) {
                    $('#loginMess').text('Tài khoản phải từ ' + minLen + ' đến ' + maxLen + ' ký tự');
                    return;
                }
                if (password.length < minLen || password.length > maxLen) {
                    $('#loginMess').text('Mật khẩu phải từ ' + minLen + ' đến ' + maxLen + ' ký tự');
                    return;
                }

                $('#btnLogin').prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: submitUrl,
                    data: { username: username, password: password },
                    success: function (result) {
                        if (result.success) {
                            ShowConfirm('Thông báo', result.message, 2, result.redirect || '/');
                        } else {
                            $('#loginMess').text(result.message);
                        }
                    },
                    error: function () {
                        $('#loginMess').text('Có lỗi xảy ra, vui lòng thử lại!');
                    },
                    complete: function () {
                        $('#btnLogin').prop('disabled', false);
                    }
                });
            }

            $('#btnLogin').on('click', doLogin);
            $('#username, #password').on('keypress', function (e) {
                if (e.which === 13) { doLogin(); }
            });
        });
    </script>
@endsection
