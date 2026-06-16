@extends('admin.layout')

{{--
    Form thêm mới / sửa thông tin tài khoản Đại lý (bảng DaiLyKNB)
    (tính năng mới, không có trong Admin6 gốc).
--}}

@section('title', $daiLy ? 'Sửa đại lý' : 'Thêm đại lý')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.dai-ly.index') }}">Quản lý Đại lý</a></li>
    <li class="breadcrumb-item active"><span>{{ $daiLy ? 'Sửa đại lý' : 'Thêm đại lý' }}</span></li>
@endsection

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-{{ $daiLy ? 'pen' : 'plus' }} mr-1"></i> {{ $daiLy ? 'Sửa thông tin đại lý' : 'Thêm đại lý mới' }}
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.dai-ly.save', ['id' => $id]) }}">
            @csrf
            <div class="card-body">
                @if($message)
                    <div class="alert alert-danger">{{ $message }}</div>
                @endif

                <div class="form-row">
                    <div class="form-group col-md-6">
                        <label for="TenDangNhap">Tên đăng nhập <span class="text-danger">*</span></label>
                        <input type="text" id="TenDangNhap" name="TenDangNhap" class="form-control"
                            value="{{ old('TenDangNhap', $daiLy->TenDangNhap ?? '') }}" maxlength="50">
                    </div>
                    <div class="form-group col-md-6">
                        <label for="MatKhau">Mật khẩu</label>
                        <input type="text" id="MatKhau" name="MatKhau" class="form-control" maxlength="50"
                            placeholder="{{ $daiLy ? 'Để trống nếu không đổi mật khẩu' : '' }}">
                        @if($daiLy)
                            <small class="form-text text-muted">Để trống nếu không muốn đổi mật khẩu.</small>
                        @endif
                    </div>
                </div>

                <div class="form-group">
                    <label for="HoVaTen">Họ và tên</label>
                    <input type="text" id="HoVaTen" name="HoVaTen" class="form-control"
                        value="{{ old('HoVaTen', $daiLy->HoVaTen ?? '') }}" maxlength="250">
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="Phone">Số điện thoại</label>
                        <input type="text" id="Phone" name="Phone" class="form-control"
                            value="{{ old('Phone', $daiLy->Phone ?? '') }}" maxlength="50">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="Zalo">Zalo</label>
                        <input type="text" id="Zalo" name="Zalo" class="form-control"
                            value="{{ old('Zalo', $daiLy->Zalo ?? '') }}" maxlength="50">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="Facebook">Facebook</label>
                        <input type="text" id="Facebook" name="Facebook" class="form-control"
                            value="{{ old('Facebook', $daiLy->Facebook ?? '') }}" maxlength="250">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="NganHang">Ngân hàng</label>
                        <input type="text" id="NganHang" name="NganHang" class="form-control"
                            value="{{ old('NganHang', $daiLy->NganHang ?? '') }}" maxlength="50">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="SoTaiKhoan">Số tài khoản</label>
                        <input type="text" id="SoTaiKhoan" name="SoTaiKhoan" class="form-control"
                            value="{{ old('SoTaiKhoan', $daiLy->SoTaiKhoan ?? '') }}" maxlength="50">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="ChiNhanh">Chi nhánh</label>
                        <input type="text" id="ChiNhanh" name="ChiNhanh" class="form-control"
                            value="{{ old('ChiNhanh', $daiLy->ChiNhanh ?? '') }}" maxlength="250">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="iYuanBao">Số dư KNB</label>
                        <input type="number" id="iYuanBao" name="iYuanBao" class="form-control"
                            value="{{ old('iYuanBao', $daiLy->iYuanBao ?? 0) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="ChietKhau">Chiết khấu (%)</label>
                        <input type="number" id="ChietKhau" name="ChietKhau" class="form-control"
                            value="{{ old('ChietKhau', $daiLy->ChietKhau ?? 0) }}">
                    </div>
                    <div class="form-group col-md-4">
                        <label for="IsAdmin">Loại đại lý</label>
                        <select id="IsAdmin" name="IsAdmin" class="form-control">
                            <option value="0" @selected((int) old('IsAdmin', $daiLy->IsAdmin ?? 0) === 0)>Đại lý thường</option>
                            <option value="1" @selected((int) old('IsAdmin', $daiLy->IsAdmin ?? 0) !== 0)>Đại lý tổng</option>
                        </select>
                    </div>
                </div>

                <div class="form-group form-check">
                    <input type="checkbox" id="KichHoat" name="KichHoat" class="form-check-input" value="1"
                        @checked(old('KichHoat', $daiLy->KichHoat ?? true))>
                    <label for="KichHoat" class="form-check-label">Kích hoạt tài khoản</label>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu
                </button>
                @if($daiLy)
                    <button type="submit" form="form-delete" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xoá đại lý này không?');">
                        <i class="fas fa-trash mr-1"></i> Xoá
                    </button>
                @endif
                <a href="{{ route('admin.dai-ly.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
            </div>
        </form>

        @if($daiLy)
            <form id="form-delete" method="POST" action="{{ route('admin.dai-ly.delete', ['id' => $id]) }}">
                @csrf
            </form>
        @endif
    </div>
@endsection
