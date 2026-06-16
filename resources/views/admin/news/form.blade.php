@extends('admin.layout')

{{-- port từ Admin6/AdminPageNewMNG.aspx + AdminPageNewMNG.aspx.cs --}}

@section('title', $news ? 'Sửa tin' : 'Thêm tin')

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('admin.news.index') }}">Quản lý tin</a></li>
    <li class="breadcrumb-item active"><span>{{ $news ? 'Sửa tin' : 'Thêm tin' }}</span></li>
@endsection

@section('head')
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
@endsection

@section('content')
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-{{ $news ? 'pen' : 'plus' }} mr-1"></i> {{ $news ? 'Sửa tin' : 'Thêm tin mới' }}
            </h3>
        </div>
        <form method="POST" action="{{ route('admin.news.save', ['id' => $id]) }}">
            @csrf
            <div class="card-body">
                @if($message)
                    <div class="alert alert-info">{{ $message }}</div>
                @endif

                <div class="form-group">
                    <label for="title">Tiêu đề</label>
                    <input type="text" id="title" name="title" class="form-control" value="{{ old('title', $news->title ?? '') }}">
                </div>

                <div class="form-group">
                    <label for="sub_content">Tóm tắt</label>
                    <textarea id="sub_content" name="sub_content" class="form-control" rows="3">{{ old('sub_content', $news->fksubcontent ?? '') }}</textarea>
                </div>

                <div class="form-row">
                    <div class="form-group col-md-4">
                        <label for="category_id">Danh mục</label>
                        <select id="category_id" name="category_id" class="form-control">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->Id }}" @selected(old('category_id', $news->categoryId ?? null) == $cat->Id)>{{ $cat->Name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group col-md-8">
                        <label>Ngày đăng</label>
                        <input type="text" class="form-control" disabled
                            value="{{ $news?->date ? $news->date->format('d/m/Y H:i:s') : 'Sẽ lấy theo thời gian hiện tại khi lưu' }}">
                    </div>
                </div>

                <div class="form-group">
                    <label for="txtSummernote">Nội dung</label>
                    <textarea id="txtSummernote" name="content" rows="2">{{ old('content', $news->fkcontent ?? '') }}</textarea>
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save mr-1"></i> Lưu
                </button>
                @if($news)
                    <button type="submit" form="form-delete" class="btn btn-danger" onclick="return confirm('Bạn có chắc muốn xóa bài viết này không?');">
                        <i class="fas fa-trash mr-1"></i> Xóa bài
                    </button>
                @endif
                <a href="{{ route('admin.news.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left mr-1"></i> Quay lại
                </a>
            </div>
        </form>

        @if($news)
            <form id="form-delete" method="POST" action="{{ route('admin.news.delete', ['id' => $id]) }}">
                @csrf
            </form>
        @endif
    </div>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script>
        $(function () {
            $('#txtSummernote').summernote({
                height: 250,
            });
        });
    </script>
@endsection
