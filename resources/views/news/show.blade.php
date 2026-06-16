@extends('layouts.app')

{{-- port từ ChiTietTinV2.aspx + .aspx.cs -> /tin-tuc/{slug}.{id} --}}

{{-- SEO (title/description/keywords/OG) lấy từ cài đặt "Trang chi tiết tin"
     tại /admin/seo (App\Services\SeoSettings) - xem layouts/app.blade.php --}}
@section('seo_page', 'news_detail')

@section('title', $metaTitle)

@section('head')
    <meta name="description" content="{{ $metaDescription }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription }}">
    <meta property="og:image" content="{{ $ogImage }}">
@endsection

@push('head-extra')
    {{--
        Khung nền cho phần nội dung/sidebar - bản gốc dùng ảnh nền cố định
        (bg-new.png, list-new.png) theo kích thước desktop nên không tái sử
        dụng được; ở đây dùng gradient kem + viền/bo góc cho đồng bộ tông màu
        site nhưng vẫn co giãn responsive.
    --}}
    <style>
        .news-detail-box,
        .news-sidebar-box {
            background: linear-gradient(to bottom, #fffdf6 0%, #fbf4dd 100%);
            border: 1px solid #e6d3a3;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            padding: 16px 18px;
        }

        .news-detail-box .box-tittle {
            border-bottom: 2px solid #e6d3a3;
        }

        .news-sidebar-box {
            margin-top: 0;
        }

        .news-breadcrumb {
            background: linear-gradient(to bottom, #fffdf6 0%, #fbf4dd 100%);
            border: 1px solid #e6d3a3;
            border-radius: 8px;
            padding: 8px 14px;
            margin-bottom: 16px;
        }

        .news-breadcrumb .breadcrumb {
            margin-bottom: 0;
        }
    </style>
@endpush

@section('content')
    <div class="container py-3">
        <nav aria-label="breadcrumb" class="news-breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="/"><i class="fa-solid fa-house"></i> Trang chủ</a></li>
                <li class="breadcrumb-item"><a href="/tin-tuc/all"><i class="fa-solid fa-newspaper"></i> Tin tức</a></li>
                @if ($categoryName)
                    <li class="breadcrumb-item"><i class="fa-solid fa-tag"></i> {{ $categoryName }}</li>
                @endif
                <li class="breadcrumb-item active" aria-current="page">
                    <i class="fa-solid fa-file-lines"></i> {{ \Illuminate\Support\Str::limit($news->title, 40, '...') }}
                </li>
            </ol>
        </nav>

        <div class="row">
            {{-- Nội dung bài viết --}}
            <div class="col-lg-8">
                <article class="box-detail news-detail-box">
                    <div class="box-tittle">
                        <h1 class="mb-2">{{ $news->title }}</h1>
                        <span class="m-Itime d-flex flex-wrap align-items-center gap-2">
                            @if ($news->date)
                                <span><i class="fa-regular fa-clock"></i> {{ $news->date->format('d/m/Y') }}</span>
                            @endif
                            @if ($categoryName)
                                <span class="badge bg-success">{{ $categoryName }}</span>
                            @endif
                        </span>
                    </div>

                    @if (!empty($news->fksubcontent))
                        <p class="fw-semibold focus">{{ $news->fksubcontent }}</p>
                    @endif

                    <div class="dataBody">
                        {!! $news->fkcontent !!}
                    </div>

                    {{-- Chia sẻ bài viết --}}
                    <div class="fb-like d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-semibold">Chia sẻ:</span>
                        <a class="btn btn-sm btn-primary" target="_blank" rel="noopener"
                            href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}">
                            <i class="fa-brands fa-facebook-f"></i> Facebook
                        </a>
                        <a class="btn btn-sm" style="background:#0068ff;color:#fff" target="_blank" rel="noopener"
                            href="https://zalo.me/share?u={{ urlencode(url()->current()) }}&t={{ urlencode($news->title) }}">
                            <i class="fa-solid fa-comment-dots"></i> Zalo
                        </a>
                    </div>
                </article>
            </div>

            {{-- Sidebar: Tin mới nhất --}}
            <div class="col-lg-4">
                <div class="box-other news-sidebar-box">
                    <h2 class="s-boxTitle">Tin mới nhất</h2>

                    @if ($latestNews->isNotEmpty())
                        @include('home.partials.list-tin', ['items' => $latestNews])
                    @else
                        <p class="text-muted">Chưa có bài viết khác.</p>
                    @endif

                    <a href="/tin-tuc/all" class="btn btn-warning w-100 mt-3 fw-semibold">
                        <i class="fa-solid fa-list-ul"></i> Xem tất cả bài viết
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection
