@extends('admin.layout')

{{-- port từ Admin6/AdminNapThe.aspx + AdminNapThe.aspx.cs --}}

@section('title', 'Quản lý nạp thẻ')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Quản lý nạp thẻ</span></li>
@endsection

@section('content')
    <div class="info-box mb-4">
        <span class="info-box-icon bg-warning"><i class="fas fa-coins"></i></span>
        <div class="info-box-content">
            <span class="info-box-text">Kim Nguyên Bảo còn lại</span>
            <span class="info-box-number">{{ $kimNguyenBaoCon ?: 'Không xác định' }}</span>
        </div>
    </div>

    <div class="card card-primary card-outline mb-4">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-credit-card mr-1"></i> Nạp thẻ cho đại lý</h3>
        </div>
        <form method="POST" action="{{ route('admin.napthe') }}">
            @csrf
            <div class="card-body">
                @if($message)
                    <div id="napthe-alert" class="alert alert-info alert-dismissible fade show">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        {{ $message }}
                    </div>
                @endif
                <div class="form-row align-items-end">
                    <div class="form-group col-md-5 mb-0">
                        <label>Tài khoản đại lý</label>
                        <select name="ho_va_ten" class="form-control">
                            @forelse($daiLyList as $dl)
                                <option value="{{ $dl->HoVaTen }}">{{ $dl->HoVaTen }}</option>
                            @empty
                                <option value="">Không có đại lý nào</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group col-md-5 mb-0">
                        <label>Mệnh giá nạp</label>
                        <select name="menh_gia" class="form-control">
                            <option value="1000">1.000.000 VNĐ</option>
                            <option value="2000" selected>2.000.000 VNĐ</option>
                            <option value="3000">3.000.000 VNĐ</option>
                            <option value="5000">5.000.000 VNĐ</option>
                            <option value="10000">10.000.000 VNĐ</option>
                        </select>
                    </div>
                    <div class="form-group col-md-2 mb-0">
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-paper-plane mr-1"></i> Nạp Ngay
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="card card-outline card-secondary">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-history mr-1"></i> Lịch sử nạp</h3>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">STT</th>
                        <th>Đại Lý</th>
                        <th>Ngày nạp</th>
                        <th>Mệnh giá nạp</th>
                        <th>Số KNB</th>
                        <th>KNB Trước</th>
                        <th>KNB Sau</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($history as $item)
                        <tr>
                            <td>{{ $item->iid }}</td>
                            <td>{{ $item->cUserName }}</td>
                            <td>{{ optional($item->dDate)->format('d/m/Y H:i:s') }}</td>
                            <td>{{ number_format((int) $item->Money, 0, ',', '.') }} VNĐ</td>
                            <td><span class="badge badge-success">{{ $item->iFlag }} KNB</span></td>
                            <td>{{ $item->KNBTruoc }} KNB</td>
                            <td>{{ $item->KNBSau }} KNB</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Chưa có lịch sử nạp</td>
                        </tr>
                    @endforelse
                </tbody>
                @if($history->count())
                    <tfoot>
                        <tr>
                            <th colspan="3" class="text-right">Tổng:</th>
                            <th>{{ number_format($totalNap, 0, ',', '.') }} VNĐ</th>
                            <th colspan="3"></th>
                        </tr>
                    </tfoot>
                @endif
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    @if($message)
        <script>
            setTimeout(function () {
                var alertEl = document.getElementById('napthe-alert');
                if (alertEl && window.jQuery) {
                    jQuery(alertEl).fadeOut(400, function () {
                        jQuery(this).alert('close');
                    });
                } else if (alertEl) {
                    alertEl.style.display = 'none';
                }
            }, 4000);
        </script>
    @endif
@endsection
