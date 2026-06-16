{{--
    Partial nav dùng chung cho app / auth / account layouts.
    Render jx-topnav-wrap (burger dropdown) + header_inner_lstNavtop (mobile bottom nav).
    Tự định nghĩa $navItems từ site_setting('nav_items') để không phụ thuộc
    vào biến từ layout cha.
--}}
@php
    $navItems      = json_decode(site_setting('nav_items') ?: '[]', true) ?: [];
    $resolveNavUrl = function ($url) {
        return str_starts_with($url, 'setting:') ? site_setting(substr($url, 8)) : $url;
    };
    $isNavActive = function ($match) {
        if ($match === '') return false;
        return $match === '/' ? request()->is('/') : request()->is(ltrim($match, '/'));
    };
@endphp

{{-- ── Topnav mobile (burger) ──────────────────────────────────────────── --}}
<div class="jx-topnav-wrap" id="jxTopnavWrap">
    <div class="jx-topnav-bar">
        <a href="{{ route('home') }}" class="jx-topnav-logo">
            <img src="/img/logo.webp" alt="Logo">
        </a>
        <div class="jx-topnav-actions">
            <a href="{{ route('napthe.coin') }}" class="jx-topnav-pill jx-topnav-pill--topup">Nạp Thẻ</a>
            <a href="{{ route('tai-game') }}" class="jx-topnav-pill jx-topnav-pill--install">Cài Đặt</a>
            <button type="button" id="jxTopnavBurger" class="jx-topnav-burger" aria-label="Menu">
                <i class="fa-solid fa-bars"></i>
            </button>
        </div>
    </div>
    <div class="jx-topnav-dropdown">
        <ul>
            @foreach ($navItems as $navItem)
            <li>
                <a class="{{ $isNavActive($navItem['url_match'] ?? '') ? 'active' : '' }}"
                   href="{{ $resolveNavUrl($navItem['url']) }}"
                   @if(!empty($navItem['target'])) target="{{ $navItem['target'] }}" @endif>
                    <i class="{{ $navItem['icon'] }}"></i> {{ $navItem['label'] }}
                </a>
            </li>
            @endforeach
        </ul>
    </div>
</div>

{{-- ── Header desktop + nav pill mobile (bottom bar) ─────────────────────── --}}
<div class="bg-new {{ $bgNewClass ?? '' }}"
     @if(!empty($bgNewStyle)) style="{{ $bgNewStyle }}" @endif>
    <header class="header">
        <div class="header_inner">
            <div class="container">
                <div class="r-menu-header gap-2 py-1">
                    <a href="/"><img class="r-header-icon" src="/img/logo.webp" alt="icon"></a>
                    <div>
                        <ul class="header_inner_lstNavtop cf rs">
                            @foreach ($navItems as $navItem)
                            <li>
                                <a class="d-flex gap-2 align-items-center justify-content-center py-1 py-md-2 {{ $isNavActive($navItem['url_match'] ?? '') ? 'active' : '' }}"
                                   href="{{ $resolveNavUrl($navItem['url']) }}"
                                   @if(!empty($navItem['target'])) target="{{ $navItem['target'] }}" @endif>
                                    <i class="{{ $navItem['icon'] }}"></i><span class="d-none d-md-inline">{{ $navItem['label'] }}</span>
                                </a>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="fixCen"><h1 class="rs"></h1></div>
        </div>
    </header>
</div>
