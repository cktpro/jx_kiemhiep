{{--
    Form cài đặt SEO dùng chung cho 1 trang ('home' hoặc 'news').
    Biến đầu vào: $page (string), $data (array: meta_title, meta_description,
    meta_keywords, og_title, og_description, og_image).
--}}
<form method="POST" action="{{ route('admin.seo.save') }}">
    @csrf
    <input type="hidden" name="page" value="{{ $page }}">

    <h5 class="mb-3"><i class="fas fa-tags mr-1"></i> Thẻ Meta</h5>

    @if($page === 'news_detail')
        <div class="alert alert-info">
            Dùng <code>{title}</code> và <code>{description}</code> trong các trường dưới đây - hệ thống sẽ tự
            thay bằng tiêu đề/mô tả của từng bài viết. Ví dụ: <code>JX Kiểm Hiệp 1 Mobile | {title}</code>.
        </div>
    @endif

    <div class="form-group">
        <label for="meta_title_{{ $page }}">Meta Title</label>
        <input type="text" id="meta_title_{{ $page }}" name="meta_title" class="form-control"
            value="{{ old('meta_title', $data['meta_title']) }}" maxlength="255">
        <small class="form-text text-muted">Hiển thị trên thẻ &lt;title&gt; và kết quả tìm kiếm.</small>
    </div>

    <div class="form-group">
        <label for="meta_description_{{ $page }}">Meta Description</label>
        <textarea id="meta_description_{{ $page }}" name="meta_description" class="form-control" rows="3" maxlength="500">{{ old('meta_description', $data['meta_description']) }}</textarea>
        <small class="form-text text-muted">Đoạn mô tả ngắn hiển thị dưới tiêu đề trên Google.</small>
    </div>

    <div class="form-group">
        <label for="meta_keywords_{{ $page }}">Meta Keywords</label>
        <textarea id="meta_keywords_{{ $page }}" name="meta_keywords" class="form-control" rows="2">{{ old('meta_keywords', $data['meta_keywords']) }}</textarea>
        <small class="form-text text-muted">Các từ khoá cách nhau bởi dấu phẩy.</small>
    </div>

    <hr>

    <h5 class="mb-3"><i class="fab fa-facebook mr-1"></i> Open Graph (chia sẻ Facebook/Zalo...)</h5>

    <div class="form-group">
        <label for="og_title_{{ $page }}">OG Title</label>
        <input type="text" id="og_title_{{ $page }}" name="og_title" class="form-control"
            value="{{ old('og_title', $data['og_title']) }}" maxlength="255">
    </div>

    <div class="form-group">
        <label for="og_description_{{ $page }}">OG Description</label>
        <textarea id="og_description_{{ $page }}" name="og_description" class="form-control" rows="3" maxlength="500">{{ old('og_description', $data['og_description']) }}</textarea>
    </div>

    <div class="form-group">
        <label for="og_image_{{ $page }}">OG Image (URL)</label>
        <input type="text" id="og_image_{{ $page }}" name="og_image" class="form-control"
            value="{{ old('og_image', $data['og_image']) }}" placeholder="https://...">
        @if(!empty($data['og_image']))
            <div class="mt-2">
                <img src="{{ $data['og_image'] }}" alt="OG image" class="img-thumbnail" style="max-height:120px">
            </div>
        @endif
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fas fa-save mr-1"></i> Lưu cài đặt
    </button>
</form>
