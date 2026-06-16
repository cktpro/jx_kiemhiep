@extends('admin.layout')

{{--
    Quản lý tài khoản Đại lý (bảng DaiLyKNB): thêm, sửa, xoá, kích hoạt
    (tính năng mới, không có trong Admin6 gốc).
--}}

@section('title', 'Quản lý Đại lý')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Quản lý Đại lý</span></li>
@endsection

@section('content')
    @if($saved)
        <div id="dai-ly-alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            Đã lưu thông tin đại lý.
        </div>
    @endif

    @if($topupSuccess)
        <div id="dai-ly-topup-alert" class="alert alert-success alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ $topupSuccess }}
        </div>
    @endif

    @if($topupError)
        <div id="dai-ly-topup-error-alert" class="alert alert-danger alert-dismissible fade show">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
            {{ $topupError }}
        </div>
    @endif

    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-user-tie mr-1"></i> Quản lý Đại lý</h3>
            <div class="card-tools">
                <a href="{{ route('admin.dai-ly.form') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Thêm đại lý
                </a>
            </div>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.dai-ly.index') }}" class="form-inline mb-3">
                <input type="text" name="q" value="{{ $keyword }}" class="form-control mr-2" placeholder="Tìm theo tên đăng nhập, họ tên, SĐT...">
                <button type="submit" class="btn btn-outline-secondary">
                    <i class="fas fa-search mr-1"></i> Tìm
                </button>
                @if($keyword !== '')
                    <a href="{{ route('admin.dai-ly.index') }}" class="btn btn-link">Xoá tìm kiếm</a>
                @endif
            </form>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th style="width:50px">#</th>
                        <th>Tên đăng nhập</th>
                        <th>Họ và tên</th>
                        <th>SĐT / Zalo</th>
                        <th class="text-right">Số dư KNB</th>
                        <th class="text-right">Chiết khấu (%)</th>
                        <th class="text-center">Quyền</th>
                        <th class="text-center">Trạng thái</th>
                        <th style="width:160px" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($daiLyList as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->TenDangNhap }}</td>
                            <td>{{ $item->HoVaTen }}</td>
                            <td>
                                {{ $item->Phone }}
                                @if($item->Zalo)
                                    <br><small class="text-muted">Zalo: {{ $item->Zalo }}</small>
                                @endif
                            </td>
                            <td class="text-right">{{ number_format((int) $item->iYuanBao, 0, ',', '.') }}</td>
                            <td class="text-right">{{ (int) $item->ChietKhau }}</td>
                            <td class="text-center">
                                @if((int) $item->IsAdmin !== 0)
                                    <span class="badge badge-info">Đại lý tổng</span>
                                @else
                                    <span class="badge badge-secondary">Đại lý thường</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form action="{{ route('admin.dai-ly.toggle', ['id' => $item->ID, 'q' => $keyword]) }}" method="POST" class="d-inline">
                                    @csrf
                                    @if($item->KichHoat)
                                        <button type="submit" class="btn btn-sm btn-success" title="Đang kích hoạt - bấm để khoá">
                                            <i class="fas fa-check-circle mr-1"></i> Hoạt động
                                        </button>
                                    @else
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Đang khoá - bấm để kích hoạt">
                                            <i class="fas fa-ban mr-1"></i> Đã khoá
                                        </button>
                                    @endif
                                </form>
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-success" title="Nạp tiền" data-toggle="modal" data-target="#napModal{{ $item->ID }}">
                                    <i class="fas fa-coins"></i>
                                </button>
                                <a href="{{ route('admin.dai-ly.form', ['id' => $item->ID]) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form action="{{ route('admin.dai-ly.delete', ['id' => $item->ID]) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xoá đại lý [{{ $item->TenDangNhap }}] không?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xoá">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>

                                {{-- Modal nạp KNB cho đại lý (tính năng mới, không có trong code gốc) --}}
                                <div class="modal fade" id="napModal{{ $item->ID }}" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <form action="{{ route('admin.dai-ly.topup', ['id' => $item->ID, 'q' => $keyword]) }}" method="POST">
                                                @csrf
                                                <div class="modal-header">
                                                    <h5 class="modal-title">Nạp KNB cho đại lý [{{ $item->TenDangNhap }}]</h5>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times;</span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="form-group">
                                                        <label>Số dư KNB hiện tại</label>
                                                        <input type="text" class="form-control" value="{{ number_format((int) $item->iYuanBao, 0, ',', '.') }}" disabled>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="so_knb_{{ $item->ID }}">Số KNB cần nạp</label>
                                                        <input type="number" id="so_knb_{{ $item->ID }}" name="so_knb" class="form-control" min="1" step="1" required autofocus>
                                                        <small class="form-text text-muted">
                                                            Số KNB sẽ được cộng trực tiếp vào số dư của đại lý, và được ghi vào lịch sử nạp (Card_History).
                                                        </small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Đóng</button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fas fa-coins mr-1"></i> Nạp tiền
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">Không có đại lý nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted">
            Hiển thị {{ count($daiLyList) }} đại lý
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function jxFadeOutAlert(id) {
            var alertEl = document.getElementById(id);
            if (alertEl && window.jQuery) {
                jQuery(alertEl).fadeOut(400, function () {
                    jQuery(this).alert('close');
                });
            } else if (alertEl) {
                alertEl.style.display = 'none';
            }
        }

        @if($saved)
            setTimeout(function () { jxFadeOutAlert('dai-ly-alert'); }, 4000);
        @endif

        @if($topupSuccess)
            setTimeout(function () { jxFadeOutAlert('dai-ly-topup-alert'); }, 5000);
        @endif

        @if($topupError)
            setTimeout(function () { jxFadeOutAlert('dai-ly-topup-error-alert'); }, 5000);
        @endif
    </script>
@endsection
