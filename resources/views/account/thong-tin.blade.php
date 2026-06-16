@extends('layouts.account')

{{-- port từ ThongTinTaiKhoanV2.aspx (route "taikhoan2" -> /tai-khoan) --}}

@section('seo_page', 'account')

@section('title', 'Sơn Hà Xã Tắc | Trang Tài Khoản')

@section('head')
<style>
    /* ── Account info cards ─────────────────────────────────────── */
    .acc-logo {
        display: block;
        margin: 0 auto 18px;
        max-width: 100px;
        width: 100%;
        height: auto;
    }

    .acc-info-row {
        display: flex;
        align-items: center;
        gap: 14px;
        background: #f8f5f5;
        border-radius: 12px;
        padding: 12px 16px;
        margin-bottom: 12px;
    }

    .acc-info-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        background: linear-gradient(135deg, #d53030, #8f1000);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: .95rem;
    }

    .acc-info-text {
        display: flex;
        flex-direction: column;
        min-width: 0;
    }

    .acc-info-label {
        font-size: .72rem;
        color: #999;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: .6px;
        line-height: 1.2;
    }

    .acc-info-value {
        font-size: 1.05rem;
        color: #1a1a1a;
        font-weight: 700;
        word-break: break-all;
    }

    /* KNB badge highlight */
    .acc-knb .acc-info-value {
        color: #8f1000;
    }

    /* ── Giải kẹt button ────────────────────────────────────────── */
    .btn-unlock {
        width: 100%;
        padding: 11px 20px;
        background: transparent;
        border: 2px solid #8f1000;
        border-radius: 10px;
        color: #8f1000;
        font-size: .95rem;
        font-weight: 700;
        letter-spacing: .3px;
        cursor: pointer;
        transition: background .2s, color .2s;
        margin-top: 4px;
    }

    .btn-unlock:hover {
        background: #8f1000;
        color: #fff;
    }

    .btn-unlock:disabled {
        opacity: .6;
        cursor: not-allowed;
    }

    /* ── Section divider ────────────────────────────────────────── */
    .acc-section-divider {
        border: none;
        border-top: 1px solid #ece8e8;
        margin: 24px 0 20px;
    }

    .acc-section-title {
        font-size: .8rem;
        font-weight: 800;
        color: #8f1000;
        text-transform: uppercase;
        letter-spacing: .8px;
        border-left: 3px solid #8f1000;
        padding-left: 10px;
        margin-bottom: 18px;
    }

    /* ── Inline message ─────────────────────────────────────────── */
    #accMess:not(:empty) {
        border-radius: 8px;
        padding: 9px 14px;
        font-weight: 600;
        font-size: .9rem;
        margin-bottom: 14px;
    }
</style>
@endsection

@section('content')
    <img src="/img/logo.webp" alt="Võ Lâm 1 Mobi" class="acc-logo">

    <h3>Tài Khoản</h3>

    <div id="accMess" class="w-100"></div>

    {{-- Thông tin tài khoản --}}
    <div class="acc-info-row">
        <div class="acc-info-icon"><i class="fa-solid fa-user"></i></div>
        <div class="acc-info-text">
            <span class="acc-info-label">Tài khoản</span>
            <span class="acc-info-value">{{ $account->cAccName }}</span>
        </div>
    </div>

    <div class="acc-info-row acc-knb">
        <div class="acc-info-icon"><i class="fa-solid fa-coins"></i></div>
        <div class="acc-info-text">
            <span class="acc-info-label">KNB (Yuanbao)</span>
            <span class="acc-info-value">{{ number_format($account->iYuanbao) }}</span>
        </div>
    </div>

    <div class="acc-info-row">
        <div class="acc-info-icon"><i class="fa-solid fa-phone"></i></div>
        <div class="acc-info-text">
            <span class="acc-info-label">Số điện thoại</span>
            <span class="acc-info-value">{{ $account->cPhone }}</span>
        </div>
    </div>

    <button type="button" id="btnGiaiKet" class="btn-unlock">
        <i class="fa-solid fa-lock-open me-1"></i> Giải Kẹt Tài Khoản
    </button>

    <hr class="acc-section-divider">

    {{-- Đổi mật khẩu --}}
    <div class="acc-section-title">Đổi Mật Khẩu</div>

    <div class="col-12 mb-3">
        <label class="form-label" for="old_pass">Mật khẩu cũ</label>
        <div class="input-icon-group">
            <i class="fa-solid fa-lock"></i>
            <input type="password" id="old_pass" name="old_pass" class="form-control"
                placeholder="Nhập mật khẩu hiện tại" maxlength="16">
        </div>
    </div>

    <div class="col-12 mb-3">
        <label class="form-label" for="new_pass">Mật khẩu mới</label>
        <div class="input-icon-group">
            <i class="fa-solid fa-key"></i>
            <input type="password" id="new_pass" name="new_pass" class="form-control"
                placeholder="Mật khẩu mới (6–16 ký tự)" maxlength="16">
        </div>
    </div>

    <div class="col-12 mb-3">
        <button type="button" id="btnChangePass" class="btn_login">
            Đổi Mật Khẩu
        </button>
    </div>

    <div class="d-flex gap-3 justify-content-center mt-2 flex-wrap">
        <a href="/nap-coin" class="btn_dkm"
            style="background:linear-gradient(135deg,#d53030,#8f1000);color:#fff!important;border-color:transparent">
            <i class="fa-solid fa-coins me-1"></i> Nạp KNB
        </a>
        <a href="/cap-nhat-so-dien-thoai" class="btn_dkm">
            <i class="fa-solid fa-mobile-screen-button me-1"></i> Đổi số điện thoại
        </a>
    </div>

    <hr class="acc-section-divider">

    <form action="{{ route('logout') }}" method="POST" class="text-center">
        @csrf
        <button type="submit" class="btn_dkm border-0"
            style="background:transparent;cursor:pointer;color:#8f1000">
            <i class="fa-solid fa-right-from-bracket me-1"></i> Đăng xuất
        </button>
    </form>
@endsection

@section('scripts')
    <script>
        $(function () {
            function showMessage(msg, ok) {
                $('#accMess')
                    .attr('class', 'w-100 alert ' + (ok ? 'alert-success' : 'alert-danger'))
                    .text(msg);
            }

            $('#btnGiaiKet').on('click', function () {
                $(this).prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: '/tai-khoan/giai-ket',
                    success: function (result) { showMessage(result.message, result.success); },
                    error: function () { showMessage('Có lỗi xảy ra, vui lòng thử lại!', false); },
                    complete: function () { $('#btnGiaiKet').prop('disabled', false); }
                });
            });

            $('#btnChangePass').on('click', function () {
                var oldPass = $('#old_pass').val().trim();
                var newPass = $('#new_pass').val().trim();

                if (newPass.length < 6 || newPass.length > 16) {
                    showMessage('Mật khẩu mới phải từ 6 đến 16 ký tự', false);
                    return;
                }

                $(this).prop('disabled', true);
                $.ajax({
                    type: 'POST',
                    url: '/tai-khoan/doi-mat-khau',
                    data: { old_pass: oldPass, new_pass: newPass },
                    success: function (result) {
                        showMessage(result.message, result.success);
                        if (result.success) {
                            $('#old_pass').val('');
                            $('#new_pass').val('');
                        }
                    },
                    error: function () { showMessage('Có lỗi xảy ra, vui lòng thử lại!', false); },
                    complete: function () { $('#btnChangePass').prop('disabled', false); }
                });
            });
        });
    </script>
@endsection
