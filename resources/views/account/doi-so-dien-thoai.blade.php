@extends('layouts.account')

{{-- port từ DoiSoDienThoaiV2.aspx (route "doisdt2" -> /cap-nhat-so-dien-thoai) --}}

@section('seo_page', 'doi-sdt')

@section('title', 'Đổi số điện thoại - Võ Lâm 1 Mobi')

@section('content')
    <h3>Đổi Số Điện Thoại</h3>

    <div id="phoneError" class="text-danger mb-2"></div>
    <div id="phoneStep1" class="mb-2"></div>

    <div class="col-12 mb-2">
        <label style="font-size: smaller">Tài khoản</label>
        <input type="text" class="form-control" value="{{ $account->cAccName }}" readonly>
    </div>

    <div class="col-12 mb-2">
        <label style="font-size: smaller">SĐT hiện tại</label>
        <input type="text" class="form-control" value="{{ $account->cPhone }}" readonly>
    </div>

    <div class="col-12 mb-2">
        <label style="font-size: smaller" for="new_phone">SĐT mới</label>
        <input type="text" id="new_phone" name="new_phone" class="form-control" placeholder="Số điện thoại mới (10-12 số)" maxlength="12">
    </div>

    <div class="col-md-12 col-lg-12 col-xl-12 margin_bottom_20">
        <button type="button" id="btnChangePhone" class="btn_login">Lấy hướng dẫn đổi SĐT</button>
    </div>

    <div class="col-12 margin_top_20 mb-2" style="text-align: center">
        <a href="/tai-khoan" class="btn_dkm">Về trang tài khoản</a>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#btnChangePhone').on('click', function () {
                var newPhone = $('#new_phone').val().trim();

                $('#phoneError').text('');
                $('#phoneStep1').html('');

                if (newPhone.length < 10 || newPhone.length > 12) {
                    $('#phoneError').text('Số điện thoại mới không đúng định dạng');
                    return;
                }

                $(this).prop('disabled', true);

                $.ajax({
                    type: 'POST',
                    url: '/cap-nhat-so-dien-thoai',
                    data: { new_phone: newPhone },
                    success: function (result) {
                        if (result.success) {
                            $('#phoneStep1').html(result.html);
                        } else {
                            $('#phoneError').text(result.message);
                        }
                    },
                    error: function () {
                        $('#phoneError').text('Có lỗi xảy ra, vui lòng thử lại!');
                    },
                    complete: function () {
                        $('#btnChangePhone').prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
