@extends('admin.layout')

{{-- port từ Admin6/AdminPageNews.aspx + AdminPageNews.aspx.cs (GetListTin) --}}

@section('title', 'Quản lý tin')

@section('breadcrumb')
    <li class="breadcrumb-item active"><span>Quản lý tin</span></li>
@endsection

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-newspaper mr-1"></i> Quản lý tin tức</h3>
            <div class="card-tools">
                <a href="{{ route('admin.news.form') }}" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus mr-1"></i> Thêm mới
                </a>
            </div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-hover text-nowrap mb-0">
                <thead>
                    <tr>
                        <th style="width:60px">#</th>
                        <th>Tiêu đề</th>
                        <th style="width:120px">Ngày</th>
                        <th style="width:130px">Danh mục</th>
                        <th style="width:140px" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($newsList as $index => $item)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $item->title }}</td>
                            <td>{{ optional($item->date)->format('d/m/Y') }}</td>
                            <td><span class="badge badge-info">Hướng dẫn</span></td>
                            <td class="text-center">
                                <a href="{{ route('news.show', ['slug' => $item->slug]) }}" target="_blank" class="btn btn-sm btn-outline-secondary" title="Xem">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.news.form', ['id' => $item->id]) }}" class="btn btn-sm btn-outline-primary" title="Sửa">
                                    <i class="fas fa-pen"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Chưa có tin tức nào</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer text-muted">
            Hiển thị {{ count($newsList) }} tin mới nhất
        </div>
    </div>
@endsection
