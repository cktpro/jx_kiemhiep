{{--
    Nút chat nổi Messenger + Zalo ở góc phải dưới, hiện trên cả máy tính và
    điện thoại. Link chỉnh ở /admin/cai-dat (link_messenger, link_zalo).
    Nếu chưa nhập link Messenger thì tự suy ra m.me/<tên page> từ link Fanpage.
--}}
@php
    $chatMessenger = trim((string) site_setting('link_messenger'));
    if ($chatMessenger === '') {
        $chatFacebook = (string) site_setting('link_facebook');
        if (preg_match('#facebook\.com/(?!profile\.php|groups/|share/)([A-Za-z0-9.\-_]+)#i', $chatFacebook, $chatMatch)) {
            $chatMessenger = 'https://m.me/'.$chatMatch[1];
        } else {
            $chatMessenger = $chatFacebook;
        }
    }
    $chatZalo = trim((string) site_setting('link_zalo'));
@endphp

@if($chatMessenger !== '' || $chatZalo !== '')
<style>
    .jx-chatfloat {
        position: fixed;
        right: 20px;
        bottom: calc(24px + env(safe-area-inset-bottom, 0px));
        z-index: 1040;
        display: flex;
        flex-direction: column;
        gap: 14px;
        transition: right .2s;
    }
    /* Trang chủ có sidebar nổi (#jxAside, hiện từ 992px, cao 470px giữa màn
       hình). Màn hình thấp thì 2 nút sẽ đè lên đáy sidebar => dời sang trái
       sidebar khi đang mở, hoặc né dải 43px còn lộ ra khi sidebar đóng. */
    @@media (min-width: 992px) and (max-height: 859.98px) {
        .jx-aside.active ~ .jx-chatfloat { right: 258px; }
        .jx-aside:not(.active) ~ .jx-chatfloat { right: 58px; }
    }
    .jx-chatfloat__btn {
        position: relative;
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        text-decoration: none;
        box-shadow: 0 6px 16px rgba(0, 0, 0, .28);
        transition: transform .2s ease, box-shadow .2s ease;
    }
    .jx-chatfloat__btn:hover,
    .jx-chatfloat__btn:focus-visible {
        color: #fff;
        transform: scale(1.08);
        box-shadow: 0 8px 22px rgba(0, 0, 0, .35);
    }
    .jx-chatfloat__btn::before {
        content: "";
        position: absolute;
        inset: 0;
        border-radius: 50%;
        background: inherit;
        z-index: -1;
        animation: jx-chatfloat-pulse 2s ease-out infinite;
    }
    .jx-chatfloat__btn--messenger {
        background: linear-gradient(45deg, #0a7cff 0%, #a033ff 60%, #ff5c87 100%);
        font-size: 30px;
    }
    .jx-chatfloat__btn--zalo {
        background: #0068ff;
    }
    .jx-chatfloat__btn--zalo::before {
        animation-delay: 1s;
    }
    .jx-chatfloat__btn svg {
        width: 34px;
        height: 34px;
        display: block;
    }
    @@keyframes jx-chatfloat-pulse {
        0%   { transform: scale(1);   opacity: .55; }
        100% { transform: scale(1.6); opacity: 0; }
    }
    @@media (prefers-reduced-motion: reduce) {
        .jx-chatfloat__btn::before { animation: none; opacity: 0; }
    }
    @@media (max-width: 768px) {
        .jx-chatfloat { right: 14px; bottom: calc(18px + env(safe-area-inset-bottom, 0px)); gap: 12px; }
        .jx-chatfloat__btn { width: 50px; height: 50px; }
        .jx-chatfloat__btn--messenger { font-size: 26px; }
        .jx-chatfloat__btn svg { width: 30px; height: 30px; }
    }
</style>

<div class="jx-chatfloat" aria-label="Liên hệ hỗ trợ">
    @if($chatMessenger !== '')
        <a href="{{ $chatMessenger }}" target="_blank" rel="noopener"
           class="jx-chatfloat__btn jx-chatfloat__btn--messenger" title="Chat qua Messenger" aria-label="Chat qua Messenger">
            <i class="fa-brands fa-facebook-messenger" aria-hidden="true"></i>
        </a>
    @endif
    @if($chatZalo !== '')
        <a href="{{ $chatZalo }}" target="_blank" rel="noopener"
           class="jx-chatfloat__btn jx-chatfloat__btn--zalo" title="Chat qua Zalo" aria-label="Chat qua Zalo">
            <svg viewBox="0 0 48 48" aria-hidden="true">
                <path fill="#fff" d="M11 7h26a7 7 0 0 1 7 7v15a7 7 0 0 1-7 7H21l-9 6.5V36h-1a7 7 0 0 1-7-7V14a7 7 0 0 1 7-7z"/>
                <text x="24" y="26.5" text-anchor="middle" font-family="Arial, Helvetica, sans-serif"
                      font-weight="700" font-size="12.5" fill="#0068ff">Zalo</text>
            </svg>
        </a>
    @endif
</div>
@endif
