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
                    <label for="slug">
                        Slug (URL)
                        <small class="text-muted ml-1">— để trống sẽ tự động tạo từ tiêu đề</small>
                    </label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text text-muted">/tin-tuc/</span>
                        </div>
                        <input type="text" id="slug" name="slug" class="form-control"
                               value="{{ old('slug', $news->attributes['slug'] ?? '') }}"
                               placeholder="vi-du-slug-bai-viet">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-outline-secondary" id="btn-gen-slug" title="Tạo từ tiêu đề">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
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
            $('#txtSummernote').summernote({ height: 250 });

            function slugifyVn(str) {
                const map = {
                    'á':'a','à':'a','ạ':'a','ả':'a','ã':'a','â':'a','ấ':'a','ầ':'a','ậ':'a','ẩ':'a','ẫ':'a','ă':'a','ắ':'a','ằ':'a','ặ':'a','ẳ':'a','ẵ':'a',
                    'Á':'a','À':'a','Ạ':'a','Ả':'a','Ã':'a','Â':'a','Ấ':'a','Ầ':'a','Ậ':'a','Ẩ':'a','Ẫ':'a','Ă':'a','Ắ':'a','Ằ':'a','Ặ':'a','Ẳ':'a','Ẵ':'a',
                    'é':'e','è':'e','ẹ':'e','ẻ':'e','ẽ':'e','ê':'e','ế':'e','ề':'e','ệ':'e','ể':'e','ễ':'e',
                    'É':'e','È':'e','Ẹ':'e','Ẻ':'e','Ẽ':'e','Ê':'e','Ế':'e','Ề':'e','Ệ':'e','Ể':'e','Ễ':'e',
                    'ó':'o','ò':'o','ọ':'o','ỏ':'o','õ':'o','ô':'o','ố':'o','ồ':'o','ộ':'o','ổ':'o','ỗ':'o','ơ':'o','ớ':'o','ờ':'o','ợ':'o','ở':'o','ỡ':'o',
                    'Ó':'o','Ò':'o','Ọ':'o','Ỏ':'o','Õ':'o','Ô':'o','Ố':'o','Ồ':'o','Ộ':'o','Ổ':'o','Ỗ':'o','Ơ':'o','Ớ':'o','Ờ':'o','Ợ':'o','Ở':'o','Ỡ':'o',
                    'ú':'u','ù':'u','ụ':'u','ủ':'u','ũ':'u','ư':'u','ứ':'u','ừ':'u','ự':'u','ử':'u','ữ':'u',
                    'Ú':'u','Ù':'u','Ụ':'u','Ủ':'u','Ũ':'u','Ư':'u','Ứ':'u','Ừ':'u','Ự':'u','Ử':'u','Ữ':'u',
                    'í':'i','ì':'i','ị':'i','ỉ':'i','ĩ':'i','Í':'i','Ì':'i','Ị':'i','Ỉ':'i','Ĩ':'i',
                    'đ':'d','Đ':'d',
                    'ý':'y','ỳ':'y','ỵ':'y','ỷ':'y','ỹ':'y','Ý':'y','Ỳ':'y','Ỵ':'y','Ỷ':'y','Ỹ':'y',
                };
                return str
                    .split('').map(c => map[c] || c).join('')
                    .toLowerCase()
                    .replace(/[^a-z0-9]+/g, '-')
                    .replace(/^-+|-+$/g, '')
                    .replace(/-+/g, '-');
            }

            // Nút tạo slug từ tiêu đề
            $('#btn-gen-slug').on('click', function () {
                const title = $('#title').val().trim();
                if (title) $('#slug').val(slugifyVn(title));
            });

            // Bài mới: tự điền slug theo tiêu đề khi chưa có slug
            @if(!$news)
            $('#title').on('input', function () {
                if ($('#slug').data('manual')) return;
                $('#slug').val(slugifyVn($(this).val().trim()));
            });
            $('#slug').on('input', function () {
                $(this).data('manual', $(this).val() !== '');
            });
            @endif
        });
    </script>
@endsection
