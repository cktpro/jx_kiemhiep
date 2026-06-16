<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Cài đặt giao diện khu vực Đại lý (favicon, title, tên brand sidebar, nội
 * dung footer) hiển thị ở layouts/daily.blade.php, chỉnh được từ
 * /admin/dai-ly-config (tính năng mới, không có trong code gốc).
 *
 * Lưu dưới dạng file JSON trong storage/app - tương tự App\Services\AdminSettings
 * và App\Services\FooterSettings. Nếu chưa có file hoặc thiếu field, dùng giá
 * trị mặc định lấy từ layouts/daily.blade.php hiện tại.
 */
class DaiLySettings
{
    private const FILE = 'daily_settings.json';

    /**
     * Giá trị mặc định - tương đương nội dung hardcode trước đây trong
     * layouts/daily.blade.php.
     */
    public static function defaults(): array
    {
        return [
            // (tính năng mới, không có trong code gốc) Mặc định lấy theo
            // favicon chung ở /admin/cai-dat (favicon) nếu đã cấu hình, nếu
            // không thì dùng ảnh mặc định cũ. Trang Đại lý vẫn có thể tự đặt
            // favicon riêng tại /admin/dai-ly-config (sẽ ưu tiên hơn).
            'favicon' => site_setting('favicon') ?: '/img/logo-mobile-r.jpg',
            'title' => 'Đại lý',
            'brand_text' => 'Đại lý KNB',
            'footer_text' => site_setting('footer1'),
        ];
    }

    /**
     * Toàn bộ cài đặt (mặc định + đã lưu, ưu tiên dữ liệu đã lưu).
     */
    public static function all(): array
    {
        $defaults = self::defaults();
        $stored = self::readStored();

        return [
            'favicon' => $stored['favicon'] ?? $defaults['favicon'],
            'title' => $stored['title'] ?? $defaults['title'],
            'brand_text' => $stored['brand_text'] ?? $defaults['brand_text'],
            'footer_text' => $stored['footer_text'] ?? $defaults['footer_text'],
        ];
    }

    /**
     * Lưu cài đặt.
     *
     * @param  array  $data  ['favicon' => string|null, 'title' => string,
     *                         'brand_text' => string, 'footer_text' => string]
     *                        'favicon' chỉ cần truyền khi có ảnh mới (đã được
     *                        AdminController xử lý upload và chuyển thành
     *                        đường dẫn /img/...); nếu null/rỗng thì giữ favicon cũ.
     */
    public static function save(array $data): void
    {
        $current = self::all();

        $favicon = trim((string) ($data['favicon'] ?? ''));
        $title = trim((string) ($data['title'] ?? ''));
        $brandText = trim((string) ($data['brand_text'] ?? ''));
        $footerText = trim((string) ($data['footer_text'] ?? ''));

        $stored = [
            'favicon' => $favicon !== '' ? $favicon : $current['favicon'],
            'title' => $title !== '' ? $title : $current['title'],
            'brand_text' => $brandText !== '' ? $brandText : $current['brand_text'],
            'footer_text' => $footerText !== '' ? $footerText : $current['footer_text'],
        ];

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
