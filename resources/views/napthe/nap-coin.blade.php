@extends('layouts.app')

{{-- port từ NapTheV2.aspx (route "napthe2" -> /nap-coin) --}}

@section('seo_page', 'napthe')

@section('head')
<style>
/* ── Card nạp KNB ──────────────────────────────────────────────── */
.napcoin-wrap {
    padding: 32px 0 48px;
}

.napcoin-card {
    background: #fff;
    border-radius: 20px;
    box-shadow: 0 12px 48px rgba(0,0,0,.18);
    overflow: hidden;
}

/* Header đỏ */
.napcoin-header {
    background: linear-gradient(135deg, #d53030, #8f1000);
    padding: 28px 32px 24px;
    text-align: center;
    color: #fff;
}

.napcoin-header .nc-icon {
    width: 64px;
    height: 64px;
    background: rgba(255,255,255,.18);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    margin: 0 auto 14px;
}

.napcoin-header h2 {
    font-size: 1.6rem;
    font-weight: 800;
    letter-spacing: 1px;
    margin: 0;
    text-transform: uppercase;
}

.napcoin-header p {
    margin: 6px 0 0;
    font-size: .9rem;
    opacity: .85;
}

/* Tỉ giá */
.napcoin-rate {
    background: linear-gradient(90deg, #fff8e1, #fff3cd);
    border-left: 4px solid #f5a623;
    margin: 0 28px;
    border-radius: 10px;
    padding: 14px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    margin-top: -1px;
}

.napcoin-rate i {
    font-size: 1.4rem;
    color: #e08000;
    flex-shrink: 0;
}

.napcoin-rate .rate-label {
    font-size: .78rem;
    color: #888;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
}

.napcoin-rate .rate-value {
    font-size: 1.1rem;
    font-weight: 800;
    color: #8f4b00;
}

/* Body */
.napcoin-body {
    padding: 24px 28px 28px;
}

/* Steps */
.nc-steps {
    list-style: none;
    padding: 0;
    margin: 0 0 20px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.nc-step {
    display: flex;
    align-items: flex-start;
    gap: 14px;
}

.nc-step-num {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: linear-gradient(135deg, #d53030, #8f1000);
    color: #fff;
    font-size: .8rem;
    font-weight: 800;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 1px;
}

.nc-step-text {
    font-size: .92rem;
    color: #444;
    line-height: 1.6;
}

/* Support */
.nc-support {
    background: #f5f5f5;
    border-radius: 10px;
    padding: 12px 16px;
    font-size: .88rem;
    color: #666;
    margin-bottom: 24px;
}

.nc-support a {
    color: #8f1000;
    font-weight: 700;
    text-decoration: none;
}

.nc-support a:hover { text-decoration: underline; }

/* Buttons */
.nc-actions {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.nc-btn-primary {
    flex: 1;
    min-width: 140px;
    padding: 13px 20px;
    background: linear-gradient(135deg, #d53030, #8f1000);
    border: none;
    border-radius: 10px;
    color: #fff !important;
    font-weight: 700;
    font-size: .95rem;
    text-align: center;
    text-decoration: none !important;
    box-shadow: 0 6px 18px rgba(143,16,0,.3);
    transition: transform .15s, box-shadow .15s;
    display: inline-block;
}

.nc-btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(143,16,0,.4);
    color: #fff !important;
}

.nc-btn-secondary {
    flex: 1;
    min-width: 140px;
    padding: 13px 20px;
    background: transparent;
    border: 2px solid #8f1000;
    border-radius: 10px;
    color: #8f1000 !important;
    font-weight: 700;
    font-size: .95rem;
    text-align: center;
    text-decoration: none !important;
    transition: background .2s, color .2s;
    display: inline-block;
}

.nc-btn-secondary:hover {
    background: #8f1000;
    color: #fff !important;
}
</style>
@endsection

@section('content')
<div class="napcoin-wrap">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-12 col-sm-10 col-md-8 col-lg-6 col-xl-5">

                <div class="napcoin-card">

                    {{-- Header --}}
                    <div class="napcoin-header">
                        <div class="nc-icon">
                            <i class="fa-solid fa-coins"></i>
                        </div>
                        <h2>Nạp KNB</h2>
                        <p>Võ Lâm 1 Sơn Hà Xã Tắc Mobile</p>
                    </div>

                    {{-- Tỉ giá --}}
                    <div style="padding: 18px 28px 0">
                        <div class="napcoin-rate">
                            <i class="fa-solid fa-arrow-right-arrow-left"></i>
                            <div>
                                <div class="rate-label">Tỉ giá quy đổi</div>
                                <div class="rate-value">1 KNB &nbsp;=&nbsp; 1.000 VNĐ</div>
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="napcoin-body">

                        <ul class="nc-steps">
                            <li class="nc-step">
                                <div class="nc-step-num">1</div>
                                <div class="nc-step-text">
                                    Đăng ký nạp thẻ qua Đại lý bên dưới hoặc liên hệ fanpage để được hỗ trợ.
                                </div>
                            </li>
                            <li class="nc-step">
                                <div class="nc-step-num">2</div>
                                <div class="nc-step-text">
                                    Gửi biên lai / hóa đơn cho Admin. Trong vòng <strong>60 phút</strong> Admin sẽ nạp KNB vào tài khoản.
                                </div>
                            </li>
                            <li class="nc-step">
                                <div class="nc-step-num">3</div>
                                <div class="nc-step-text">
                                    Thoát khỏi game và đăng nhập lại, vào gặp <strong>NPC Tiền Trang</strong> để rút KNB.
                                </div>
                            </li>
                        </ul>

                        <div class="nc-support">
                            <i class="fa-solid fa-circle-info me-1" style="color:#e08000"></i>
                            Có vấn đề phát sinh? Liên hệ hỗ trợ qua
                            <a href="{{ site_setting('link_facebook') }}" target="_blank">Fanpage</a>
                            hoặc
                            <a href="{{ site_setting('link_zalo') }}" target="_blank">Zalo</a>.
                        </div>

                        {{-- ── Form nạp ──────────────────────────────────── --}}
                        <div id="napTheMess" class="mb-3" style="display:none"></div>

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-weight:600">Tên tài khoản</label>
                            <input type="text" id="acc_name" class="form-control" maxlength="16" placeholder="Nhập tên tài khoản game">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-weight:600">Mệnh giá nạp</label>
                            <select id="menh_gia" class="form-select">
                                <option value="50000">50.000 VNĐ</option>
                                <option value="100000">100.000 VNĐ</option>
                                <option value="200000">200.000 VNĐ</option>
                                <option value="300000">300.000 VNĐ</option>
                                <option value="400000">400.000 VNĐ</option>
                                <option value="500000">500.000 VNĐ</option>
                                <option value="1000000">1.000.000 VNĐ</option>
                                <option value="2000000">2.000.000 VNĐ</option>
                                <option value="3000000">3.000.000 VNĐ</option>
                                <option value="4000000">4.000.000 VNĐ</option>
                                <option value="5000000">5.000.000 VNĐ</option>
                                <option value="10000000">10.000.000 VNĐ</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-600" style="font-weight:600">Đại lý</label>
                            <select id="dai_ly" class="form-select">
                                @foreach ($daiLyList as $index => $daiLy)
                                    <option value="{{ $daiLy->HoVaTen }}" @if($index === $selectedIndex) selected @endif>{{ $daiLy->HoVaTen }}</option>
                                @endforeach
                            </select>
                        </div>

                        <p class="text-danger" style="font-size:.85rem">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Nhập đúng tên tài khoản và chọn chính xác đại lý. Nếu sai sẽ không nhận được KNB.
                        </p>

                        <div class="nc-actions">
                            <button type="button" id="btnNapThe" class="nc-btn-primary">
                                <i class="fa-solid fa-credit-card me-1"></i> Xác nhận nạp
                            </button>
                            <a href="{{ $linkDownload }}" class="nc-btn-secondary">
                                <i class="fa-solid fa-download me-1"></i> Tải game
                            </a>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.getElementById('btnNapThe').addEventListener('click', function () {
    var btn  = this;
    var sel  = document.getElementById('menh_gia');
    var mess = document.getElementById('napTheMess');

    btn.disabled = true;
    mess.style.display = 'none';
    mess.className = 'mb-3';

    var params = new URLSearchParams({
        acc_name:      document.getElementById('acc_name').value,
        dai_ly:        document.getElementById('dai_ly').value,
        menh_gia:      sel.value,
        menh_gia_text: sel.options[sel.selectedIndex].text,
        _token:        document.querySelector('meta[name="csrf-token"]').content
    });

    fetch('/nap-coin', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: params.toString()
    })
    .then(function (r) { return r.json(); })
    .then(function (result) {
        mess.className = 'mb-3 alert ' + (result.success ? 'alert-success' : 'alert-danger');
        mess.innerHTML = result.message;
        mess.style.display = '';
        if (result.success) document.getElementById('acc_name').value = '';
    })
    .catch(function () {
        mess.className = 'mb-3 alert alert-danger';
        mess.textContent = 'Có lỗi xảy ra, vui lòng thử lại!';
        mess.style.display = '';
    })
    .finally(function () { btn.disabled = false; });
});
</script>
@endsection
