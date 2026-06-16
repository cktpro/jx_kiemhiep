@extends('admin.layout')

{{-- Thêm/sửa ảnh slide khu "Tính năng đặc sắc" trang chủ (tính năng mới) --}}

@section('title', $slide ? 'Sửa slide' : 'Thêm slide')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.slides.index') }}">Quản lý slide</a></li>
    <li class="breadcrumb-item active"><span>{{ $slide ? 'Sửa slide' : 'Thêm slide' }}</span></li>
@endsection

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-{{ $slide ? 'pen' : 'plus' }} mr-1"></i> {{ $slide ? 'Sửa slide' : 'Thêm slide mới' }}
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.slides.save', ['id' => $id]) }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                @if($message)
                    <div class="alert alert-info">{{ $message }}</div>
                @endif

                <div class="form-group">
                    <label>Ảnh hiện tại</label><br>
                    @if($slide)
                        <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] ?? '' }}" class="img-fluid rounded border" style="max-height:160px;">
                    @else
                        <div class="text-muted">Chưa có ảnh - vui lòng chọn ảnh để tải lên.</div>
                    @endif
                </div>

                <div class="form-group">
                    <label for="image">{{ $slide ? 'Đổi ảnh (bỏ trống nếu giữ ảnh cũ)' : 'Chọn ảnh' }}</label>
                    <input type="file" id="image" name="image" class="form-control-file" accept="image/jpeg,image/png,image/webp,image/gif">
                    <small class="form-text text-muted">Hỗ trợ JPG, PNG, WEBP, GIF - tối đa 5MB. Ảnh sẽ được hiển thị trong slider khu "Tính năng đặc sắc" trên trang chủ.</small>
                </div>

                <div class="form-group">
                    <label for="alt">Chú thích (alt)</label>
                    <input type="text" id="alt" name="alt" class="form-control" value="{{ old('alt', $slide['alt'] ?? '') }}" maxlength="255" placeholder="Ví dụ: Hệ thống Auto">
                </div>

                <div class="form-group">
                    <label for="link">Liên kết khi bấm vào ảnh (không bắt buộc)</label>
                    <input type="text" id="link" name="link" class="form-control" value="{{ old('link', $slide['link'] ?? '') }}" maxlength="500" placeholder="https://...">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu
                </button>
                @if($slide)
                    <button type="submit" form="form-delete" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa ảnh slide này không?');">
                        <i class="fas fa-trash mr-1"></i> Xóa
                    </button>
                @endif
                <a href="{{ route('admin.slides.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
            </div>
        </form>

        @if($slide)
            <form id="form-delete" method="POST" action="{{ route('admin.slides.delete', ['id' => $slide['id']]) }}">
                @csrf
            </form>
        @endif
    </div>
@endsection
