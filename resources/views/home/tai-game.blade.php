@extends('layouts.app')

{{--
    Trang tải game (/tai-game): Android APK, iOS TestFlight, giả lập PC.
    Link chỉnh ở /admin/cai-dat (link_download_android / _ios / _emulator).
    Thẻ hợp với thiết bị đang xem được đánh dấu "Khuyên dùng" và xếp lên đầu trên mobile.
--}}

@section('title', 'Tải game - '.site_setting('footer1'))

@section('meta_description')
    <meta name="description" content="Tải JX Kiếm Hiệp 1 Mobile cho Android (APK), iOS (TestFlight) và giả lập trên máy tính (LDPlayer, BlueStacks). Hướng dẫn cài đặt chi tiết.">
@endsection

@php
    $fmtSize = function (?int $bytes): ?string {
        if (! $bytes) return null;
        if ($bytes >= 1073741824) return number_format($bytes / 1073741824, 2).' GB';
        if ($bytes >= 1048576) return number_format($bytes / 1048576, 0).' MB';
        return number_format($bytes / 1024, 0).' KB';
    };

    $platforms = [
        'android' => [
            'title' => 'Android',
            'sub' => 'File APK cho điện thoại / máy tính bảng Android',
            'icon' => 'fa-brands fa-android',
            'btn' => 'Tải APK',
            'steps' => [
                'Bấm <strong>Tải APK</strong> và chờ tải xong.',
                'Mở file vừa tải (trong mục <em>Tải xuống</em>).',
                'Nếu máy hỏi, bật <strong>Cho phép cài ứng dụng từ nguồn này</strong> rồi bấm <strong>Cài đặt</strong>.',
            ],
        ],
        'ios' => [
            'title' => 'iOS',
            'sub' => 'Cài qua TestFlight cho iPhone / iPad',
            'icon' => 'fa-brands fa-apple',
            'btn' => 'Tải qua TestFlight',
            'steps' => [
                'Cài ứng dụng <a href="https://apps.apple.com/app/testflight/id899247664" target="_blank" rel="noopener"><strong>TestFlight</strong></a> từ App Store.',
                'Quay lại trang này, bấm <strong>Tải qua TestFlight</strong>.',
                'Bấm <strong>Chấp nhận</strong> rồi <strong>Cài đặt</strong> trong TestFlight.',
            ],
        ],
        'emulator' => [
            'title' => 'Giả lập (PC)',
            'sub' => 'Chơi trên máy tính bằng LDPlayer, BlueStacks...',
            'icon' => 'fa-solid fa-desktop',
            'btn' => 'Tải bản giả lập',
            'steps' => [
                'Cài giả lập <a href="https://www.ldplayer.net/" target="_blank" rel="noopener"><strong>LDPlayer</strong></a> hoặc <a href="https://www.bluestacks.com/" target="_blank" rel="noopener"><strong>BlueStacks</strong></a>.',
                'Bấm <strong>Tải bản giả lập</strong> để tải file APK.',
                'Kéo thả file APK vào cửa sổ giả lập để cài, rồi mở game.',
            ],
        ],
    ];

    $recommended = $device === 'pc' ? 'emulator' : $device;
    // Trên mobile đưa thẻ khuyên dùng lên đầu.
    $order = array_keys($platforms);
    usort($order, fn ($a, $b) => ($b === $recommended) <=> ($a === $recommended));
@endphp

@push('head-extra')
    <style>
        .dl-page { padding-top: 24px; padding-bottom: 48px; }
        .dl-head { text-align: center; margin-bottom: 24px; }
        .dl-head img { max-width: 260px; width: 60%; height: auto; }
        .dl-head h1 {
            margin: 8px 0 6px; font-size: 1.9rem; font-weight: 800; color: #5a3b00;
            text-shadow: 0 1px 0 #fff;
        }
        .dl-head p { margin: 0; color: #6b5532; }

        .dl-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
        .dl-card {
            position: relative; display: flex; flex-direction: column;
            background: linear-gradient(to bottom, #fffdf6 0%, #fbf4dd 100%);
            border: 1px solid #e6d3a3; border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, .08);
            padding: 22px 20px 20px;
        }
        .dl-card--rec { border: 2px solid #ff6600; box-shadow: 0 6px 18px rgba(255, 102, 0, .2); }
        .dl-badge {
            position: absolute; top: -12px; left: 50%; transform: translateX(-50%);
            background: #ff6600; color: #fff; font-size: .78rem; font-weight: 700;
            padding: 3px 12px; border-radius: 999px; white-space: nowrap;
        }
        .dl-card__top { display: flex; align-items: center; gap: 14px; margin-bottom: 14px; }
        .dl-card__icon {
            flex: 0 0 56px; width: 56px; height: 56px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center;
            font-size: 30px; color: #fff;
        }
        .dl-card--android .dl-card__icon { background: #3ddc84; }
        .dl-card--ios .dl-card__icon { background: #1c1c1e; }
        .dl-card--emulator .dl-card__icon { background: #2f6fde; }
        .dl-card__title { margin: 0; font-size: 1.25rem; font-weight: 800; color: #5a3b00; }
        .dl-card__sub { margin: 2px 0 0; font-size: .86rem; color: #7a6440; }

        .dl-btn {
            display: flex; align-items: center; justify-content: center; gap: 8px;
            width: 100%; padding: 12px 16px; border-radius: 10px; border: 0;
            font-weight: 800; font-size: 1.02rem; text-decoration: none; color: #fff;
            background: linear-gradient(to bottom, #ff8a2b, #e85d00);
            box-shadow: 0 3px 0 #b84800; transition: transform .15s, filter .15s;
        }
        .dl-btn:hover, .dl-btn:focus-visible { color: #fff; filter: brightness(1.06); transform: translateY(-1px); }
        .dl-btn--off { background: #c9bfa8; box-shadow: 0 3px 0 #a99e86; cursor: not-allowed; }
        .dl-btn--off:hover { transform: none; filter: none; }
        .dl-meta { margin: 8px 0 0; text-align: center; font-size: .8rem; color: #8a7550; min-height: 1.2em; }

        .dl-steps { margin: 16px 0 0; padding: 14px 0 0; border-top: 1px dashed #e0cb98; list-style: none; counter-reset: dl; }
        .dl-steps li { position: relative; padding-left: 30px; margin-bottom: 9px; font-size: .9rem; color: #4d3a1c; line-height: 1.45; }
        .dl-steps li:last-child { margin-bottom: 0; }
        .dl-steps li::before {
            counter-increment: dl; content: counter(dl);
            position: absolute; left: 0; top: 0; width: 21px; height: 21px; border-radius: 50%;
            background: #5a3b00; color: #fff; font-size: .74rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
        }
        .dl-steps a { color: #e85d00; }

        .dl-help {
            margin-top: 22px; text-align: center; color: #5a3b00; font-size: .92rem;
            background: rgba(255, 253, 246, .85); border: 1px solid #e6d3a3; border-radius: 10px; padding: 12px 16px;
        }
        .dl-help a { color: #e85d00; font-weight: 700; }

        @@media (max-width: 1199.98px) {
            .dl-grid { grid-template-columns: 1fr; max-width: 520px; margin: 0 auto; gap: 22px; }
            .dl-card__order-0 { order: 0; } .dl-card__order-1 { order: 1; } .dl-card__order-2 { order: 2; }
        }
        @@media (max-width: 991.98px) {
            .dl-head h1 { font-size: 1.5rem; }
        }
        @@media (min-width: 1200px) {
            /* Máy tính: giữ thứ tự cố định Android - iOS - Giả lập */
            .dl-card--android { order: 0; } .dl-card--ios { order: 1; } .dl-card--emulator { order: 2; }
        }
        /* Sidebar nổi #jxAside (rộng ~243px, hiện từ 992px) che mép phải nội
           dung khi màn hình chưa đủ rộng => chừa chỗ bên phải cho nó. */
        @@media (min-width: 992px) and (max-width: 1649.98px) {
            .dl-page { max-width: none; padding-left: 24px; padding-right: 268px; }
        }
    </style>
@endpush

@section('content')
    <div class="container dl-page">
        <div class="dl-head">
            <img src="/img/logo-kiem-hiep.webp" alt="JX Kiếm Hiệp 1 Mobile" width="260" height="176">
            <h1>Tải game JX Kiếm Hiệp 1 Mobile</h1>
            <p>Chọn phiên bản phù hợp với thiết bị của bạn</p>
        </div>

        <div class="dl-grid">
            @foreach($order as $pos => $key)
                @php $p = $platforms[$key]; $d = $downloads[$key]; $isRec = $key === $recommended; @endphp
                <div class="dl-card dl-card--{{ $key }} dl-card__order-{{ $pos }} @if($isRec) dl-card--rec @endif" id="dl-{{ $key }}">
                    @if($isRec)
                        <span class="dl-badge"><i class="fa-solid fa-star me-1"></i> Khuyên dùng cho thiết bị của bạn</span>
                    @endif

                    <div class="dl-card__top">
                        <div class="dl-card__icon"><i class="{{ $p['icon'] }}" aria-hidden="true"></i></div>
                        <div>
                            <h2 class="dl-card__title">{{ $p['title'] }}</h2>
                            <p class="dl-card__sub">{{ $p['sub'] }}</p>
                        </div>
                    </div>

                    @if($d['url'])
                        <a href="{{ $d['url'] }}" class="dl-btn" @if($key === 'ios') target="_blank" rel="noopener" @endif>
                            <i class="fa-solid fa-download" aria-hidden="true"></i> {{ $p['btn'] }}
                        </a>
                        <p class="dl-meta">
                            @if($d['size'])
                                {{ $fmtSize($d['size']) }} · Cập nhật {{ date('d/m/Y', $d['updated']) }}
                            @endif
                        </p>
                    @else
                        <span class="dl-btn dl-btn--off" aria-disabled="true">
                            <i class="fa-solid fa-clock" aria-hidden="true"></i> Đang cập nhật
                        </span>
                        <p class="dl-meta">Phiên bản này sẽ sớm có link tải.</p>
                    @endif

                    <ol class="dl-steps">
                        @foreach($p['steps'] as $step)
                            <li>{!! $step !!}</li>
                        @endforeach
                    </ol>
                </div>
            @endforeach
        </div>

        <div class="dl-help">
            Gặp khó khăn khi cài đặt?
            @if($guideLink)
                Xem <a href="{{ $guideLink }}">hướng dẫn chi tiết</a> hoặc
            @endif
            liên hệ hỗ trợ qua <a href="{{ site_setting('link_zalo') }}" target="_blank" rel="noopener">Zalo</a>
            / <a href="{{ site_setting('link_facebook') }}" target="_blank" rel="noopener">Fanpage</a>.
        </div>
    </div>
@endsection
