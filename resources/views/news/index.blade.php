@extends('layouts.app')

{{-- port từ DanhSachTin.aspx + .aspx.cs -> /tin-tuc/all (route gốc "alltin") --}}

{{-- Tiêu đề + meta SEO lấy từ cài đặt "Trang tin tức" tại /admin/seo (App\Services\SeoSettings) --}}
@section('seo_page', 'news')

@push('head-extra')
    {{--
        Khung nền đồng bộ với trang chi tiết tin (news/show.blade.php) - gradient
        kem + viền/bo góc, co giãn responsive.
    --}}
    <style>
        .news-list-box {
            background: linear-gradient(to bottom, #fffdf6 0%, #fbf4dd 100%);
            border: 1px solid #e6d3a3;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            padding: 16px 18px;
        }

        .news-list-box .nav-tabs {
            border-bottom: 2px solid #e6d3a3;
        }

        .news-list-box .nav-tabs .nav-link {
            color: #5a3b00;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            border-radius: 0;
        }

        .news-list-box .nav-tabs .nav-link:hover {
            color: #ff6600;
        }

        .news-list-box .nav-tabs .nav-link.active {
            color: #ff6600;
            background: transparent;
            border-bottom: 3px solid #ff6600;
        }

        .post-list__item {
            border-bottom: 1px solid #e6d3a3;
            padding: 10px 0;
        }

        .post-list__item:last-child {
            border-bottom: none;
        }

        .post-list__title {
            color: #545454;
            text-decoration: none;
        }

        .post-list__title:hover {
            color: #ff6600;
        }

        .post-list__item time {
            color: #b3b3b3;
            font-size: 0.8125rem;
            white-space: nowrap;
        }
    </style>
@endpush

@section('content')
    <div class="container py-3">
        <h1 class="mb-3">Danh sách bài viết</h1>

        <div class="news-list-box">
            {{-- Tab chuyên mục --}}
            <ul class="nav nav-tabs mb-3">
                <li class="nav-item">
                    <a class="nav-link @if(!$activeCategory) active @endif" href="{{ route('news.index') }}">
                        Tất cả
                    </a>
                </li>
                @foreach ($categories as $cat)
                    <li class="nav-item">
                        <a class="nav-link @if($activeCategory === $cat->Id) active @endif"
                            href="{{ route('news.index', ['category' => $cat->Id]) }}">
                            {{ $cat->Name }}
                        </a>
                    </li>
                @endforeach
            </ul>

            <div class="post-list">
                @forelse ($news as $item)
                    <div class="post-list__item">
                        <div class="post-list__content d-flex justify-content-between align-items-center gap-2">
                            <a href="{{ $item->url }}" class="post-list__title">
                                {{ \Illuminate\Support\Str::limit($item->title, 70, '...') }}
                            </a>
                            @if ($item->date)
                                <time>{{ $item->date->format('d/m/Y') }}</time>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-muted mb-0">Không có bài viết nào.</p>
                @endforelse
            </div>

            <div class="mt-3">
                {{ $news->onEachSide(2)->links() }}
            </div>
        </div>
    </div>
@endsection
