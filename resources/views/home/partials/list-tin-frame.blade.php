{{--
    Partial render danh sách tin tức cho 1 tab trong khung ".news-frame"
    (trang chủ, giao diện kiểu khtd.vn) - dùng riêng cho home/index.blade.php,
    không dùng chung với list-tin.blade.php (sidebar trang chi tiết tin).

    Biến truyền vào:
        $items - Collection<App\Models\News> (đã orderByDesc('date')->take(6))
--}}
@forelse ($items->take(4) as $item)
    <div class="news-frame__item">
        <a href="{{ $item->url }}" title="{{ $item->title }}">
            {{ \Illuminate\Support\Str::limit($item->title, 48, '...') }}
        </a>
        @if ($item->date)
            <time>{{ $item->date->format('d/m/Y') }}</time>
        @endif
    </div>
@empty
    <p class="text-muted mb-0">Chưa có bài viết.</p>
@endforelse
