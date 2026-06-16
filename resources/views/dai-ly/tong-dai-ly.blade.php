@extends('layouts.daily')

{{-- port từ TongDaiLy.aspx + .aspx.cs -> /tong-dai-ly --}}

@section('title', 'Đại lý Tổng')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Đại lý Tổng</span></li>
@endsection

@section('content')
    @if (! $isTongDaiLy)
        <div class="alert alert-danger">{{ $message }}</div>
        <a href="/dai-ly-nap-the" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left mr-1"></i> Về trang quản lý nạp
        </a>
    @else
        @if ($napMessage)
            <div class="alert alert-success">{!! $napMessage !!}</div>
        @endif

        <div id="napDLMess" class="mb-3"></div>

        <div class="row">
            <div class="col-lg-4 col-6">
                <div class="info-box">
                    <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
                    <div class="info-box-content">
                        <span class="info-box-text">Số dư KNB Tổng</span>
                        <span class="info-box-number">{{ $kimNguyenBaoCon }}</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-primary card-outline">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-paper-plane mr-1"></i> Chuyển KIM cho Đại lý con</h3>
            </div>
            <div class="card-body">
                <div class="form-row align-items-end">
                    <div class="form-group col-md-5 mb-0">
                        <label>Đại lý con</label>
                        <select id="dl_dai_ly" class="form-control">
                            @foreach ($daiLyList as $daiLy)
                                <option value="{{ $daiLy->HoVaTen }}">{{ $daiLy->HoVaTen }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-4 mb-0">
                        <label>Mệnh giá</label>
                        <select id="dl_menh_gia" class="form-control">
                            <option value="50">50.000 VNĐ</option>
                            <option value="100">100.000 VNĐ</option>
                            <option value="500">500.000 VNĐ</option>
                            <option value="1000">1.000.000 VNĐ</option>
                            <option value="2000">2.000.000 VNĐ</option>
                            <option value="3000">3.000.000 VNĐ</option>
                            <option value="5000">5.000.000 VNĐ</option>
                            <option value="10000">10.000.000 VNĐ</option>
                            <option value="20000">20.000.000 VNĐ</option>
                            <option value="50000">50.000.000 VNĐ</option>
                        </select>
                    </div>
                    <div class="form-group col-md-3 mb-0">
                        <button type="button" id="btnNapDaiLy" class="btn btn-success btn-block">
                            <i class="fas fa-paper-plane mr-1"></i> Chuyển KIM
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-outline card-secondary">
            <div class="card-header">
                <h3 class="card-title"><i class="fas fa-history mr-1"></i> Lịch sử chuyển KIM</h3>
            </div>
            <div class="card-body">
                <p class="mb-0">Tổng đã chuyển: <b>{{ number_format($totalNap) }} VNĐ</b></p>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Đại lý nhận</th>
                            <th>KNB</th>
                            <th>Số tiền</th>
                            <th>KNB trước</th>
                            <th>KNB sau</th>
                            <th>Thời gian</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($history as $item)
                            <tr>
                                <td>{{ $item->iid }}</td>
                                <td>{{ $item->cUserName }}</td>
                                <td><span class="badge badge-success">{{ $item->iFlag }} KNB</span></td>
                                <td>{{ number_format((int) $item->Money) }}</td>
                                <td>{{ $item->KNBTruoc }}</td>
                                <td>{{ $item->KNBSau }}</td>
                                <td>{{ optional($item->dDate)->format('d/m/Y H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted py-4">Không có dữ liệu</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif
@endsection

@section('scripts')
    @if ($isTongDaiLy)
        <script>
            $(function () {
                $('#btnNapDaiLy').on('click', function () {
                    var $btn = $(this).prop('disabled', true);

                    $.post('{{ url('/tong-dai-ly/nap') }}', {
                        dai_ly: $('#dl_dai_ly').val(),
                        menh_gia: $('#dl_menh_gia').val()
                    }).done(function (result) {
                        flashMessage($('#napDLMess'), 'mb-3', result.success, result.message);

                        if (result.success) {
                            location.reload();
                        }
                    }).fail(function () {
                        flashMessage($('#napDLMess'), 'mb-3', false, 'Có lỗi xảy ra, vui lòng thử lại!');
                    }).always(function () {
                        $btn.prop('disabled', false);
                    });
                });
            });
        </script>
    @endif
@endsection
