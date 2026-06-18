<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Cài đặt SEO (meta title/description/keywords, Open Graph) cho Trang chủ
 * và Trang tin tức, chỉnh được từ /admin/seo.
 *
 * Lưu dưới dạng file JSON trong storage/app (không cần thêm bảng vào
 * database SQL Server gốc). Nếu chưa có file hoặc thiếu field, dùng giá
 * trị mặc định lấy từ layouts/app.blade.php hiện tại.
 */
class SeoSettings
{
    private const FILE = 'seo_settings.json';

    public const PAGES = ['home', 'news', 'napthe', 'news_detail', 'daily', 'login', 'register', 'account', 'doi-sdt'];

    /**
     * Giá trị mặc định - tương đương các thẻ meta hardcode trước đây trong
     * layouts/app.blade.php.
     */
    public static function defaults(): array
    {
        return [
            'home' => [
                'meta_title' => 'Trang chủ | JX Kiểm Hiệp 1 Mobile',
                'meta_description' => 'Tái hiện nguyên bản Võ Lâm 1 PC năm 2005. Sống lại ký ức Tống – Kim, bang hội và những tháng ngày cày cấp nơi phòng net.',
                'meta_keywords' => 'võ lâm 1 jx kiểm hiệp 1 mobile, vl1 2005, võ lâm 1 pc, vo lam truyen ky 2005, tống kim, công thành chiến, game kiếm hiệp mobile, mmorpg',
                'og_title' => 'JX Kiểm Hiệp 1 Mobile – Huyền Thoại Võ Lâm 2005 Trở Lại',
                'og_description' => 'Tái hiện nguyên bản Võ Lâm 1 PC năm 2005. Sống lại ký ức Tống – Kim, bang hội và những tháng ngày cày cấp nơi phòng net.',
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
            'news' => [
                'meta_title' => 'Danh sách bài viết - JX Kiểm Hiệp 1 Mobile',
                'meta_description' => 'Tổng hợp tin tức, sự kiện, hướng dẫn mới nhất từ JX Kiểm Hiệp 1 Mobile.',
                'meta_keywords' => 'tin tức võ lâm, sự kiện jx kiểm hiệp 1 mobile, hướng dẫn vl1 mobile, vo lam truyen ky 2005',
                'og_title' => 'Tin tức - JX Kiểm Hiệp 1 Mobile',
                'og_description' => 'Cập nhật tin tức, sự kiện và hướng dẫn mới nhất từ JX Kiểm Hiệp 1 Mobile.',
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
            'napthe' => [
                'meta_title' => 'Nạp thẻ - '.site_setting('footer1'),
                'meta_description' => 'Hướng dẫn nạp thẻ, nạp Coin/KNB nhanh chóng, tỉ lệ ưu đãi cho JX Kiểm Hiệp 1 Mobile.',
                'meta_keywords' => 'nạp thẻ võ lâm, nạp coin jx kiểm hiệp 1 mobile, nạp knb vl1 mobile, vo lam truyen ky 2005',
                'og_title' => 'Nạp thẻ - JX Kiểm Hiệp 1 Mobile',
                'og_description' => 'Hướng dẫn nạp thẻ, nạp Coin/KNB nhanh chóng, tỉ lệ ưu đãi cho JX Kiểm Hiệp 1 Mobile.',
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
            'news_detail' => [
                'meta_title' => 'JX Kiểm Hiệp 1 Mobile | {title}',
                'meta_description' => 'SonHaXaTac Mobile | Võ Lâm Truyền Kỳ Mobile | {description}',
                'meta_keywords' => 'tin tức võ lâm, sự kiện jx kiểm hiệp 1 mobile, hướng dẫn vl1 mobile, vo lam truyen ky 2005',
                'og_title' => 'JX Kiểm Hiệp 1 Mobile | {title}',
                'og_description' => 'SonHaXaTac Mobile | Võ Lâm Truyền Kỳ Mobile | {description}',
                'og_image' => '/images/share-nhtl.png',
            ],
            'daily' => [
                'meta_title' => 'Đại lý | '.site_setting('footer1'),
                'meta_description' => 'Trang quản lý Đại lý nạp KNB - '.site_setting('footer1'),
                'meta_keywords' => 'đại lý nạp knb, quản lý đại lý, vo lam truyen ky 2005',
                'og_title' => 'Đại lý | '.site_setting('footer1'),
                'og_description' => 'Trang quản lý Đại lý nạp KNB - '.site_setting('footer1'),
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
            'login' => [
                'meta_title' => 'Đăng nhập | JX Kiểm Hiệp 1 Mobile',
                'meta_description' => 'Đăng nhập tài khoản JX Kiểm Hiệp 1 Mobile để tiếp tục hành trình Tống – Kim, bang hội và công thành chiến.',
                'meta_keywords' => 'đăng nhập võ lâm 1, đăng nhập jx kiểm hiệp 1 mobile, vo lam truyen ky 2005',
                'og_title' => 'Đăng nhập - JX Kiểm Hiệp 1 Mobile',
                'og_description' => 'Đăng nhập tài khoản JX Kiểm Hiệp 1 Mobile để tiếp tục hành trình Tống – Kim, bang hội và công thành chiến.',
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
            'register' => [
                'meta_title' => 'Đăng ký | JX Kiểm Hiệp 1 Mobile',
                'meta_description' => 'Đăng ký tài khoản JX Kiểm Hiệp 1 Mobile - tái hiện nguyên bản Võ Lâm 1 PC năm 2005, sống lại ký ức Tống – Kim.',
                'meta_keywords' => 'đăng ký võ lâm 1, tạo tài khoản jx kiểm hiệp 1 mobile, vo lam truyen ky 2005',
                'og_title' => 'Đăng ký - JX Kiểm Hiệp 1 Mobile',
                'og_description' => 'Đăng ký tài khoản JX Kiểm Hiệp 1 Mobile - tái hiện nguyên bản Võ Lâm 1 PC năm 2005, sống lại ký ức Tống – Kim.',
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
            'account' => [
                'meta_title' => 'Tài khoản | JX Kiểm Hiệp 1 Mobile',
                'meta_description' => 'Quản lý tài khoản JX Kiểm Hiệp 1 Mobile - xem thông tin, đổi mật khẩu, nạp KNB và giải kẹt tài khoản.',
                'meta_keywords' => 'tài khoản võ lâm 1, quản lý tài khoản jx kiểm hiệp 1 mobile, đổi mật khẩu vl1',
                'og_title' => 'Tài khoản - JX Kiểm Hiệp 1 Mobile',
                'og_description' => 'Quản lý tài khoản JX Kiểm Hiệp 1 Mobile - xem thông tin, đổi mật khẩu, nạp KNB và giải kẹt tài khoản.',
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
            'doi-sdt' => [
                'meta_title' => 'Đổi số điện thoại | JX Kiểm Hiệp 1 Mobile',
                'meta_description' => 'Cập nhật số điện thoại liên kết với tài khoản JX Kiểm Hiệp 1 Mobile.',
                'meta_keywords' => 'đổi số điện thoại võ lâm 1, cập nhật sđt jx kiểm hiệp 1 mobile',
                'og_title' => 'Đổi số điện thoại - JX Kiểm Hiệp 1 Mobile',
                'og_description' => 'Cập nhật số điện thoại liên kết với tài khoản JX Kiểm Hiệp 1 Mobile.',
                'og_image' => 'https://sonhaxatac.mobi/img/sonhaxatac_header_icon.png',
            ],
        ];
    }

    /**
     * Toàn bộ cài đặt (mặc định + đã lưu, ưu tiên dữ liệu đã lưu).
     */
    public static function all(): array
    {
        $defaults = self::defaults();
        $stored = self::readStored();

        $result = [];
        foreach ($defaults as $page => $values) {
            $result[$page] = array_merge($values, $stored[$page] ?? []);
        }

        return $result;
    }

    /**
     * Cài đặt SEO của 1 trang ('home' hoặc 'news').
     */
    public static function get(string $page): array
    {
        return self::all()[$page] ?? self::defaults()['home'];
    }

    /**
     * Lưu cài đặt SEO của 1 trang.
     */
    public static function save(string $page, array $data): void
    {
        if (! in_array($page, self::PAGES, true)) {
            return;
        }

        $stored = self::readStored();

        $fields = ['meta_title', 'meta_description', 'meta_keywords', 'og_title', 'og_description', 'og_image'];

        $stored[$page] = [];
        foreach ($fields as $field) {
            $stored[$page][$field] = trim((string) ($data[$field] ?? ''));
        }

        Storage::disk('local')->put(
            self::FILE,
            json_encode($stored, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }

    /**
     * Thay các placeholder dạng "{key}" trong chuỗi mẫu (ví dụ meta_title/
     * meta_description của trang 'news_detail': "JX Kiểm Hiệp 1 Mobile | {title}")
     * bằng giá trị thực tế tương ứng.
     *
     * @param  array<string,string>  $replacements  ['title' => ..., 'description' => ...]
     */
    public static function applyPlaceholders(string $template, array $replacements): string
    {
        $search = [];
        $values = [];

        foreach ($replacements as $key => $value) {
            $search[] = '{'.$key.'}';
            $values[] = $value;
        }

        return str_replace($search, $values, $template);
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
