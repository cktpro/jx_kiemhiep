<?php

namespace App\Http\Controllers;

use App\Models\News;
use App\Services\SlideSettings;
use Illuminate\Http\Request;

/**
 * Trang chủ + Tải game - port từ:
 *  - Default.aspx + .aspx.cs (route "/" -> trang chủ, theme jx1m skin-2020)
 *  - TaiGame.aspx + .aspx.cs   (route "/tai-game" -> luôn redirect sang
 *    bài viết hướng dẫn tải game, id = 3)
 */
class HomeController extends Controller
{
    /**
     * GET / - tương đương Default.aspx Page_Load + 4 lần gọi Load_List_Tin()
     */
    public function index(Request $request)
    {
        return view('home.index', [
            'linktaigame' => $this->resolveDownloadLink($request),
            'linkAdV4' => site_setting('link_download_android'),
            'linkAdV6' => site_setting('link_download_default'),
            'linkAndroid' => site_setting('link_download_default'),
            'linkIOS1' => site_setting('link_download_ios'),
            'linkIOS' => site_setting('link_download_ios'),
            'facebookgame' => site_setting('link_facebook'),
            'fgroupgame' => site_setting('link_zalo'),
            // 4 navtab tin tức - tương đương Load_List_Tin(categoryId) trong Default.aspx.cs
            'tinTatCa' => $this->loadListTin(0),
            'tinSuKien' => $this->loadListTin(2),
            'tinHuongDan' => $this->loadListTin(3),
            'tinTucList' => $this->loadListTin(1),
            // Slide khu "Tính năng đặc sắc" - quản lý được từ /admin/slides
            'featureSlides' => SlideSettings::all(),
        ]);
    }

    /**
     * GET /tai-game - trang tải game: Android (APK), iOS (TestFlight) và
     * bản giả lập PC. Link lấy từ /admin/cai-dat. Trước đây route này
     * redirect sang bài viết id=3 (TaiGame.aspx gốc).
     */
    public function taiGame(Request $request)
    {
        $ua = (string) $request->userAgent();
        $device = match (true) {
            str_contains($ua, 'Android') => 'android',
            str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') || str_contains($ua, 'iPod') => 'ios',
            default => 'pc',
        };

        $guide = trim((string) site_setting('link_tai_game'));

        return view('home.tai-game', [
            'device' => $device,
            'downloads' => [
                'android' => $this->downloadInfo(site_setting('link_download_android') ?: site_setting('link_download_default')),
                'ios' => $this->downloadInfo(site_setting('link_download_ios')),
                'emulator' => $this->downloadInfo(site_setting('link_download_emulator')),
            ],
            'guideLink' => ($guide !== '' && $guide !== '#' && $guide !== '/tai-game') ? $guide : null,
        ]);
    }

    /**
     * Link + dung lượng/ngày cập nhật (nếu là file trong public/, vd /download/x.apk).
     *
     * @return array{url:?string,size:?int,updated:?int}
     */
    private function downloadInfo(?string $url): array
    {
        $url = trim((string) $url);

        if ($url === '' || $url === '#') {
            return ['url' => null, 'size' => null, 'updated' => null];
        }

        $size = $updated = null;
        $path = parse_url($url, PHP_URL_PATH);
        $host = parse_url($url, PHP_URL_HOST);

        if ($path && (! $host || $host === request()->getHost())) {
            $file = realpath(public_path(rawurldecode(ltrim($path, '/'))));

            if ($file && str_starts_with($file, realpath(public_path())) && is_file($file)) {
                $size = (int) filesize($file);
                $updated = (int) filemtime($file);
            }
        }

        return ['url' => $url, 'size' => $size, 'updated' => $updated];
    }

    /**
     * Load_List_Tin(categoryId) - port từ Default.aspx.cs:
     * lấy 6 bài tin mới nhất (sắp xếp theo date giảm dần), nếu $categoryId
     * khác 0 thì lọc theo categoryId. Dùng cho 4 navtab Tin tức ở trang chủ
     * ("Tất cả" = 0, "Sự kiện" = 2, "Hướng dẫn" = 3, "Tin tức" = 1).
     */
    private function loadListTin(int $categoryId = 0)
    {
        return News::query()
            ->when($categoryId !== 0, fn ($q) => $q->where('categoryId', $categoryId))
            ->orderByDesc('date')
            ->take(6)
            ->get();
    }

    /**
     * Chọn link tải game theo loại thiết bị - tương đương:
     *   GetDeviceType() == "Android" -> linkAdV4
     *   GetDeviceType() == "iOS"     -> linkIOS1
     *   khác                          -> linkAdV6
     */
    private function resolveDownloadLink(Request $request): string
    {
        $ua = (string) $request->userAgent();

        if (str_contains($ua, 'Android')) {
            return site_setting('link_download_android');
        }

        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') || str_contains($ua, 'iPod')) {
            return site_setting('link_download_ios');
        }

        return site_setting('link_download_default');
    }
}
