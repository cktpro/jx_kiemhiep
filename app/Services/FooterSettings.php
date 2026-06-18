<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Cài đặt nội dung Footer (logo, thông tin sản phẩm, liên kết nhanh) hiển
 * thị ở cuối Trang chủ / Trang tin tức (layouts/app.blade.php), chỉnh được
 * từ /admin/footer.
 *
 * Lưu dưới dạng file JSON trong storage/app (không cần thêm bảng vào
 * database SQL Server gốc) - tương tự App\Services\SeoSettings và
 * App\Services\SlideSettings. Nếu chưa có file hoặc thiếu field, dùng giá
 * trị mặc định lấy từ layouts/app.blade.php hiện tại.
 */
class FooterSettings
{
    private const FILE = 'footer_settings.json';

    /**
     * Giá trị mặc định - tương đương nội dung hardcode trước đây trong
     * <footer> của layouts/app.blade.php.
     */
    public static function defaults(): array
    {
        return [
            'logo' => '/img/logo-kiem-hiep.webp',
            'logo_alt' => 'JX Kiểm Hiệp 1 Mobile',
            'info_lines' => [
                ['label' => 'Thông tin sản phẩm:', 'value' => 'JX Kiểm Hiệp 1 Mobile'],
                ['label' => 'Hệ điều hành hỗ trợ:', 'value' => 'IOS 15 trở lên | Android 5.0 trở lên'],
                ['label' => 'Dung lượng yêu cầu:', 'value' => '3GB'],
            ],
            'links' => [
                ['icon' => 'fa-solid fa-download', 'label' => 'Tải game', 'url' => site_setting('link_tai_game')],
                ['icon' => 'fa-solid fa-circle-info', 'label' => 'Hỗ trợ', 'url' => site_setting('link_zalo')],
                ['icon' => 'fa-solid fa-user-group', 'label' => 'Nhóm Zalo', 'url' => site_setting('link_zalo')],
                ['icon' => 'fa-brands fa-facebook', 'label' => 'Facebook', 'url' => site_setting('link_facebook')],
            ],
        ];
    }

    /**
     * Toàn bộ cài đặt Footer (mặc định + đã lưu, ưu tiên dữ liệu đã lưu).
     */
    public static function all(): array
    {
        $defaults = self::defaults();
        $stored = self::readStored();

        return [
            'logo' => $stored['logo'] ?? $defaults['logo'],
            'logo_alt' => $stored['logo_alt'] ?? $defaults['logo_alt'],
            'info_lines' => $stored['info_lines'] ?? $defaults['info_lines'],
            'links' => $stored['links'] ?? $defaults['links'],
        ];
    }

    /**
     * Lưu cài đặt Footer.
     *
     * @param  array  $data  ['logo' => string|null, 'logo_alt' => string,
     *                         'info_lines' => array<array{label:string,value:string}>,
     *                         'links' => array<array{icon:string,label:string,url:string}>]
     *                        'logo' chỉ cần truyền khi có ảnh mới (đã được
     *                        AdminController xử lý upload và chuyển thành
     *                        đường dẫn /img/...); nếu null/rỗng thì giữ logo cũ.
     */
    public static function save(array $data): void
    {
        $current = self::all();

        $logo = trim((string) ($data['logo'] ?? ''));
        $logoAlt = trim((string) ($data['logo_alt'] ?? ''));

        $infoLines = [];
        foreach ($data['info_lines'] ?? [] as $line) {
            $label = trim((string) ($line['label'] ?? ''));
            $value = trim((string) ($line['value'] ?? ''));

            if ($label === '' && $value === '') {
                continue;
            }

            $infoLines[] = ['label' => $label, 'value' => $value];
        }

        $links = [];
        foreach ($data['links'] ?? [] as $link) {
            $icon = trim((string) ($link['icon'] ?? ''));
            $label = trim((string) ($link['label'] ?? ''));
            $url = trim((string) ($link['url'] ?? ''));

            if ($label === '' && $url === '') {
                continue;
            }

            $links[] = ['icon' => $icon, 'label' => $label, 'url' => $url];
        }

        $stored = [
            'logo' => $logo !== '' ? $logo : $current['logo'],
            'logo_alt' => $logoAlt,
            'info_lines' => $infoLines,
            'links' => $links,
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
