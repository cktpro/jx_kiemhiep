@extends('admin.layout')

{{-- Quản lý ảnh slide "TÍNH NĂNG ĐẶC SẮC" trang chủ (tính năng mới) --}}

@section('title', 'Quản lý slide')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Quản lý slide</span></li>
@endsection

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-images mr-1"></i> Slide khu "Tính năng đặc sắc"</h3>
            <div class="card-tools">
                <a href="{{ route('admin.slides.form') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Thêm ảnh
                </a>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover align-middle mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th style="width:180px">Ảnh</th>
                        <th>Chú thích (alt)</th>
                        <th>Liên kết</th>
                        <th style="width:110px" class="text-center">Thứ tự</th>
                        <th style="width:120px" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($slides as $index => $slide)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <img src="{{ $slide['image'] }}" alt="{{ $slide['alt'] ?? '' }}" class="img-fluid rounded" style="max-height:60px; max-width:160px; object-fit:cover;">
                            </td>
                            <td>{{ $slide['alt'] ?? '' }}</td>
                            <td>
                                @if(!empty($slide['link']))
                                    <a href="{{ $slide['link'] }}" target="_blank" rel="noopener">{{ $slide['link'] }}</a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <form method="POST" action="{{ route('admin.slides.reorder') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $slide['id'] }}">
                                    <input type="hidden" name="direction" value="up">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Lên" @if($index === 0) disabled @endif>
                                        <i class="fas fa-arrow-up"></i>
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.slides.reorder') }}" class="d-inline">
                                    @csrf
                                    <input type="hidden" name="id" value="{{ $slide['id'] }}">
                                    <input type="hidden" name="direction" value="down">
                                    <button type="submit" class="btn btn-sm btn-outline-secondary" title="Xuống" @if($index === count($slides) - 1) disabled @endif>
                                        <i class="fas fa-arrow-down"></i>
                                    </button>
                                </form>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.slides.form', ['id' => $slide['id']]) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                                <form method="POST" action="{{ route('admin.slides.delete', ['id' => $slide['id']]) }}" class="d-inline" onsubmit="return confirm('Bạn có chắc muốn xóa ảnh slide này không?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Chưa có ảnh slide nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted">
            Hiển thị {{ count($slides) }} ảnh slide. Thứ tự ở đây cũng là thứ tự hiển thị trên trang chủ.
        </div>
    </div>
@endsection
