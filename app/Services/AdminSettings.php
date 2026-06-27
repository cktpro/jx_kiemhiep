<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Cài đặt chung cho khu vực quản trị /admin - "Tên trang quản trị" cùng các
 * giá trị trước đây hardcode trong config/site.php (link mạng xã hội, link
 * tải game, số điện thoại OTP, độ dài tài khoản/mật khẩu, footer...), chỉnh
 * được từ /admin/cai-dat.
 *
 * Lưu dưới dạng file JSON trong storage/app (không cần thêm bảng vào
 * database SQL Server gốc) - tương tự App\Services\SeoSettings,
 * App\Services\FooterSettings. Nếu chưa có file hoặc thiếu field, dùng giá
 * trị mặc định lấy từ config/site.php (tính năng mới, không có trong code gốc).
 */
class AdminSettings
{
    private const FILE = 'admin_settings.json';

    /**
     * Giá trị mặc định - tương đương "Admin VLTN" hardcode trước đây trong
     * resources/views/admin/layout.blade.php, và các giá trị mặc định của
     * config/site.php (dùng làm fallback ban đầu cho các cài đặt mới).
     */
    public static function defaults(): array
    {
        return [
            'admin_title' => 'Admin VLTN',
            'admin_footer_text' => 'JX Kiểm Hiệp 1 Mobile',

            // Link đăng nhập / đăng ký
            'link_login'    => config('site.link_login'),
            'link_register' => config('site.link_register'),

            // Mạng xã hội / cộng đồng (trước đây config/site.php)
            'link_facebook' => config('site.link_facebook'),
            'link_zalo' => config('site.link_zalo'),
            'link_tiktok' => config('site.link_tiktok'),
            'link_youtube' => config('site.link_youtube'),

            // Link hướng dẫn / tải game
            'link_tai_game' => config('site.link_tai_game'),
            'link_download_android' => config('site.link_download_android'),
            'link_download_ios' => config('site.link_download_ios'),
            'link_download_default' => config('site.link_download_default'),
            'link_download_googleplay' => config('site.link_download_googleplay'),

            // Số điện thoại nhận OTP đổi SĐT / quên mật khẩu
            'phone_otp' => config('site.phone_otp'),

            // Văn bản footer chung (SEO, trang Đại lý...)
            'footer1' => config('site.footer1'),

            // Độ dài tối đa/tối thiểu cho tài khoản
            'max_acc_len' => (int) config('site.max_acc_len', 16),
            'min_acc_len' => (int) config('site.min_acc_len', 6),

            // (tính năng mới, không có trong code gốc) Ảnh nền (background)
            // dùng chung cho tất cả trang (chủ, đăng nhập, đăng ký, tài khoản)
            // - rỗng = dùng ảnh nền mặc định có sẵn trong CSS.
            'bg_desktop' => '',
            'bg_mobile' => '',
            'banner_news' => '',

            // (tính năng mới, không có trong code gốc) Favicon áp dụng cho
            // toàn bộ trang (trang chủ, đăng nhập, đăng ký, tài khoản, admin,
            // đại lý...) - rỗng = mỗi khu vực dùng favicon mặc định riêng.
            'favicon' => '',

            // (tính năng mới) Danh sách menu điều hướng trang chủ - dùng chung
            // cho cả dropdown burger và thanh nav mobile. Lưu dưới dạng JSON
            // array; mỗi phần tử có: label, icon (FA class), url (path hoặc
            // "setting:key" để lấy từ AdminSettings), url_match (path prefix để
            // detect active), target ("_blank" hoặc "").
            'nav_items' => json_encode([
                ['label' => 'Trang chủ',  'icon' => 'fa-solid fa-house',                'url' => '/',              'url_match' => '/',         'target' => ''],
                ['label' => 'Đăng nhập',  'icon' => 'fa-solid fa-right-to-bracket',     'url' => 'setting:link_login',    'url_match' => 'dang-nhap', 'target' => ''],
                ['label' => 'Đăng ký',    'icon' => 'fa-solid fa-user-plus',            'url' => 'setting:link_register', 'url_match' => 'dang-ky',   'target' => ''],
                ['label' => 'Nạp thẻ',   'icon' => 'fa-solid fa-credit-card',          'url' => '/nap-coin',      'url_match' => 'nap-coin',  'target' => ''],
                ['label' => 'Cộng đồng', 'icon' => 'fa-solid fa-users',               'url' => 'setting:link_facebook', 'url_match' => '', 'target' => '_blank'],
                ['label' => 'Hỗ trợ',    'icon' => 'fa-solid fa-paper-plane',          'url' => 'setting:link_zalo',    'url_match' => '', 'target' => '_blank'],
                ['label' => 'Tải game',  'icon' => 'fa-solid fa-download',             'url' => '/tai-game',      'url_match' => 'tai-game',  'target' => ''],
            ], JSON_UNESCAPED_UNICODE),
        ];
    }

    /**
     * Toàn bộ cài đặt chung (mặc định + đã lưu, ưu tiên dữ liệu đã lưu).
     */
    public static function all(): array
    {
        $defaults = self::defaults();
        $stored = self::readStored();

        // Migration: gộp 4 key cũ thành 2 key mới nếu file JSON còn key cũ.
        if (!isset($stored['bg_desktop']) && isset($stored['bg_home']) && $stored['bg_home'] !== '') {
            $stored['bg_desktop'] = $stored['bg_home'];
        }
        if (!isset($stored['bg_mobile']) && isset($stored['bg_home_mobile']) && $stored['bg_home_mobile'] !== '') {
            $stored['bg_mobile'] = $stored['bg_home_mobile'];
        }

        // Migration: cập nhật nav_items cũ (URL cứng) sang dùng setting: prefix
        // để link đăng nhập/đăng ký phản ánh đúng link_login / link_register.
        if (!empty($stored['nav_items'])) {
            $navItems = json_decode($stored['nav_items'], true);
            if (is_array($navItems)) {
                $changed = false;
                foreach ($navItems as &$item) {
                    if (($item['url'] ?? '') === '/dang-nhap') {
                        $item['url'] = 'setting:link_login';
                        $changed = true;
                    }
                    if (($item['url'] ?? '') === '/dang-ky') {
                        $item['url'] = 'setting:link_register';
                        $changed = true;
                    }
                }
                unset($item);
                if ($changed) {
                    $stored['nav_items'] = json_encode($navItems, JSON_UNESCAPED_UNICODE);
                }
            }
        }

        $result = [];

        foreach ($defaults as $key => $defaultValue) {
            $result[$key] = $stored[$key] ?? $defaultValue;
        }

        return $result;
    }

    /**
     * Lưu cài đặt chung.
     *
     * @param  array  $data  Mảng key => value, các key rỗng/không có sẽ giữ
     *                        giá trị hiện tại (xem self::defaults() cho danh
     *                        sách key hỗ trợ).
     */
    public static function save(array $data): void
    {
        $current = self::all();

        $intKeys  = ['max_acc_len', 'min_acc_len'];
        $jsonKeys = ['nav_items'];

        $stored = [];

        foreach ($current as $key => $currentValue) {
            if ($key === 'admin_title' || $key === 'admin_footer_text') {
                continue;
            }

            if (in_array($key, $intKeys, true)) {
                $value = $data[$key] ?? null;
                $stored[$key] = ($value !== null && $value !== '') ? (int) $value : $currentValue;

                continue;
            }

            if (in_array($key, $jsonKeys, true)) {
                $value = trim((string) ($data[$key] ?? ''));
                $decoded = json_decode($value, true);
                $stored[$key] = is_array($decoded) ? $value : $currentValue;

                continue;
            }

            $value = trim((string) ($data[$key] ?? ''));
            $stored[$key] = $value !== '' ? $value : $currentValue;
        }

        $adminTitle = trim((string) ($data['admin_title'] ?? ''));
        $adminFooterText = trim((string) ($data['admin_footer_text'] ?? ''));

        $stored['admin_title'] = $adminTitle !== '' ? $adminTitle : $current['admin_title'];
        $stored['admin_footer_text'] = $adminFooterText !== '' ? $adminFooterText : $current['admin_footer_text'];

        Storage::disk('local')->put(
            self::FILE,
            json_encode($stored, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    private static function readStored(): array
    {
        if (! Storage::disk('local')->exists(self::FILE)) {
            return [];
        }

        $content = Storage::disk('local')->get(self::FILE);

        return json_decode($content, true) ?: [];
    }
}
