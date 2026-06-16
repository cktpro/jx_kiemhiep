@extends('layouts.account')

{{-- port từ DoiMatKhauV2.aspx (route "doimk2" -> /doi-mat-khau) --}}

@section('title', 'Đổi mật khẩu - Võ Lâm 1 Mobi')

@section('content')
    <h3>Đổi Mật Khẩu</h3>

    <div id="pwMess" class="w-100 mb-2"></div>

    <div class="col-12 mb-2">
        <label style="font-size: smaller">Tài khoản</label>
        <input type="text" class="form-control" value="{{ $account->cAccName }}" readonly>
    </div>

    <div class="col-12 mb-2">
        <label style="font-size: smaller" for="new_pass">Mật khẩu mới</label>
        <input type="password" id="new_pass" name="new_pass" class="form-control" placeholder="Mật khẩu mới (6-16 ký tự)" maxlength="16">
    </div>

    <div class="col-md-12 col-lg-12 col-xl-12 margin_bottom_20">
        <button type="button" id="btnChangePass" class="btn_login">Đổi Mật Khẩu</button>
    </div>

    <div class="col-12 margin_top_20 mb-2" style="text-align: center">
        <a href="/tai-khoan" class="btn_dkm">Về trang tài khoản</a>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#btnChangePass').on('click', function () {
                var newPass = $('#new_pass').val().trim();

                $('#pwMess').attr('class', 'w-100 mb-2');

                if (newPass.length < 6 || newPass.length > 16) {
                    $('#pwMess').addClass('alert alert-danger').text('Mật khẩu mới tối đa 16 ký tự tối thiểu 6 ký tự');
                    return;
                }

                $(this).prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '/doi-mat-khau',
                    data: { new_pass: newPass },
                    success: function (result) {
                        $('#pwMess').addClass(result.success ? 'alert alert-success' : 'alert alert-danger').text(result.message);
                        if (result.success) {
                            $('#new_pass').val('');
                        }
                    },
                    error: function () {
                        $('#pwMess').addClass('alert alert-danger').text('Có lỗi xảy ra, vui lòng thử lại!');
                    },
                    complete: function () {
                        $('#btnChangePass').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
