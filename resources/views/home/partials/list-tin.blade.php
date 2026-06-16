{{--
    Partial render danh sách tin tức cho 1 tab - port từ Load_List_Tin($categoryId)
    trong Default.aspx.cs:

        <ul class='rs list-posts'>
            <li><a href='/tin-tuc/{slug}.{id}.aspx'>{title}</a><span class='date'>{date:dd/MM}</span></li>
            ...
        </ul>

    Biến truyền vào:
        $items - Collection<App\Models\News> (đã orderByDesc('date')->take(6))
--}}
<ul class="rs list-posts">
    @foreach ($items as $item)
        <li>
            <a href="{{ $item->url }}">{{ $item->title }}</a>
            @if ($item->date)
                <span class="date">{{ $item->date->format('d/m') }}</span>
            @endif
        </li>
    @endforeach
</ul>
