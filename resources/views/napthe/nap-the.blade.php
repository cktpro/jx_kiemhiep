@extends('layouts.app')

{{-- port từ NapThe.aspx (route "napthe1" -> /nap-the, "napthe" -> /nap-the/{id}/{sv}) --}}

{{-- Tiêu đề + meta SEO lấy từ cài đặt "Trang nạp thẻ" tại /admin/seo (App\Services\SeoSettings) --}}
@section('seo_page', 'napthe')

@section('content')
    <div class="container py-3">
        <div class="row justify-content-md-center">
            <div class="col-md-12 col-lg-8 col-xl-6">
                <h3>Nạp Thẻ</h3>
                <h5>Tỉ lệ: 10.000 VNĐ = 10 KNB</h5>
                <h5>Chuyển khoản KM: 20% từ 400k (30% vào ngày vàng)</h5>

                <div id="napTheMess" class="mb-2"></div>

                <div class="mb-2">
                    <label class="form-label">Tên tài khoản</label>
                    <input type="text" id="acc_name" class="form-control" maxlength="16">
                </div>

                <div class="mb-2">
                    <label class="form-label">Mệnh giá nạp</label>
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

                <div class="mb-2">
                    <label class="form-label">Đại lý</label>
                    <select id="dai_ly" class="form-select" @if($lockDaiLy) disabled @endif>
                        @foreach ($daiLyList as $index => $daiLy)
                            <option value="{{ $daiLy->HoVaTen }}" @if($index === $selectedIndex) selected @endif>{{ $daiLy->HoVaTen }}</option>
                        @endforeach
                    </select>
                </div>

                <p class="text-danger">Lưu ý: nhập đúng tên tài khoản, chọn chính xác đại lý. Nếu sai sẽ không nhận được KNB.</p>

                <button type="button" id="btnNapThe" class="btn btn-success">Xác nhận</button>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#btnNapThe').on('click', function () {
                var $btn = $(this).prop('disabled', true);
                var $sel = $('#menh_gia');

                $.ajax({
                    type: 'POST',
                    url: '{{ url('/nap-the') }}',
                    data: {
                        acc_name: $('#acc_name').val(),
                        dai_ly: $('#dai_ly').val(),
                        menh_gia: $sel.val(),
                        menh_gia_text: $sel.find('option:selected').text()
                    },
                    success: function (result) {
                        $('#napTheMess')
                            .attr('class', 'mb-2 alert ' + (result.success ? 'alert-success' : 'alert-danger'))
                            .html(result.message);

                        if (result.success) {
                            $('#acc_name').val('');
                        }
                    },
                    error: function () {
                        $('#napTheMess').attr('class', 'mb-2 alert alert-danger').text('Có lỗi xảy ra, vui lòng thử lại!');
                    },
                    complete: function () {
                        $btn.prop('disabled', false);
                    }
                });
            });
        });
    </script>
@endsection
