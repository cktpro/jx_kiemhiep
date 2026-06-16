<?php

/**
 * Helper functions - port từ WebDaiChienTongKim/ClassHeader1.cs
 *
 * File này được autoload qua composer.json (key "files"), nên các hàm
 * dưới đây có thể gọi trực tiếp ở bất kỳ đâu trong code (Controller, Blade, ...)
 * giống như các hàm global helper khác của Laravel (ví dụ: route(), config()...).
 */

if (! function_exists('loc_dau')) {
    /**
     * Loại bỏ dấu tiếng Việt - tương đương ClassHeader1.LocDau(string str)
     */
    function loc_dau(string $str): string
    {
        static $map = null;

        if ($map === null) {
            $groups = [
                // base => danh sách ký tự có dấu tương ứng
                'a' => 'áàạảãâấầậẩẫăắằặẳẵ',
                'A' => 'ÁÀẠẢÃÂẤẦẬẨẪĂẮẰẶẲẴ',
                'e' => 'éèẹẻẽêếềệểễ',
                'E' => 'ÉÈẸẺẼÊẾỀỆỂỄ',
                'o' => 'óòọỏõôốồộổỗơớờợởỡ',
                'O' => 'ÓÒỌỎÕÔỐỒỘỔỖƠỚỜỢỞỠ',
                'u' => 'úùụủũưứừựửữ',
                'U' => 'ÚÙỤỦŨƯỨỪỰỬỮ',
                'i' => 'íìịỉĩ',
                'I' => 'ÍÌỊỈĨ',
                'd' => 'đ',
                'D' => 'Đ',
                'y' => 'ýỳỵỷỹ',
                'Y' => 'ÝỲỴỶỸ',
            ];

            $map = [];
            foreach ($groups as $base => $accented) {
                foreach (mb_str_split($accented, 1, 'UTF-8') as $char) {
                    $map[$char] = $base;
                }
            }
        }

        return str_replace(array_keys($map), array_values($map), $str);
    }

    /**
     * Alias giữ tên gốc cho dễ tra cứu khi đối chiếu code C# cũ
     */
    function LocDau(string $str): string
    {
        return loc_dau($str);
    }
}

if (! function_exists('slugify_vn')) {
    /**
     * Chuyển chuỗi tiếng Việt có dấu thành slug không dấu, viết thường,
     * cách nhau bằng "-" - tương đương hàm SQL Server dbo.fChuyenCoDauThanhKhongDau()
     * kết hợp với .ToLower() được dùng trong DefaultV2.aspx.cs (GetTin1),
     * DanhSachTin.aspx.cs, ChiTietTinV2.aspx.cs, ... để tạo URL
     * "/tin-tuc/{slug}.{id}.aspx".
     */
    function slugify_vn(string $str): string
    {
        $str = loc_dau($str);
        $str = mb_strtolower($str, 'UTF-8');

        // Mọi ký tự không phải chữ/số -> "-"
        $str = preg_replace('/[^a-z0-9]+/u', '-', $str);

        // Bỏ "-" thừa ở đầu/cuối và gộp các "-" liên tiếp
        $str = trim((string) $str, '-');
        $str = preg_replace('/-+/', '-', (string) $str);

        return (string) $str;
    }
}

if (! function_exists('calculate_md5_hash')) {
    /**
     * MD5 hash dạng HEX viết HOA - tương đương ClassHeader1.CalculateMD5Hash
     * (C# dùng format "X2" -> hex uppercase)
     */
    function calculate_md5_hash(string $input): string
    {
        return strtoupper(md5($input));
    }

    function CalculateMD5Hash(string $input): string
    {
        return calculate_md5_hash($input);
    }
}

if (! function_exists('get_hmac_hash')) {
    /**
     * HMAC-SHA256 -> Base64 - tương đương ClassHeader1.GetHmacHash
     */
    function get_hmac_hash(string $data, string $key): string
    {
        return base64_encode(hash_hmac('sha256', $data, $key, true));
    }

    function GetHmacHash(string $data, string $key): string
    {
        return get_hmac_hash($data, $key);
    }
}

if (! function_exists('auth_cookie_parts')) {
    /**
     * Đọc cookie "auth" (định dạng "data=...&hash=..." giống HttpCookie nhiều giá trị
     * của ASP.NET) và trả về mảng ['data' => ..., 'hash' => ...] hoặc null nếu không có.
     */
    function auth_cookie_parts(): ?array
    {
        $raw = request()->cookie('auth');

        if (empty($raw)) {
            return null;
        }

        parse_str($raw, $parts);

        if (empty($parts['data']) || empty($parts['hash'])) {
            return null;
        }

        return $parts;
    }
}

if (! function_exists('check_auth')) {
    /**
     * Kiểm tra cookie "auth" hợp lệ (hash đúng + chưa hết hạn)
     * - tương đương ClassHeader1.CheckAuth()
     */
    function check_auth(): bool
    {
        $parts = auth_cookie_parts();

        if ($parts === null) {
            return false;
        }

        $data = $parts['data'];
        $hash = $parts['hash'];

        // 1. Kiểm tra hash
        $computedHash = get_hmac_hash($data, config('site.secret_key'));
        if (! hash_equals($computedHash, $hash)) {
            return false;
        }

        // 2. Kiểm tra thời hạn hết hạn
        $segments = explode('|', $data);
        if (count($segments) < 2) {
            return false;
        }

        [$username, $expString] = $segments;

        try {
            $expTime = new \DateTime($expString);
        } catch (\Exception $e) {
            return false;
        }

        $now = new \DateTime('now', new \DateTimeZone('UTC'));
        $expTime->setTimezone(new \DateTimeZone('UTC'));

        if ($now > $expTime) {
            return false; // cookie hết hạn
        }

        return true;
    }

    function CheckAuth(): bool
    {
        return check_auth();
    }
}

if (! function_exists('get_user_auth')) {
    /**
     * Lấy username từ cookie "auth" (không kiểm tra hash/hết hạn)
     * - tương đương ClassHeader1.GetUserAuth()
     */
    function get_user_auth(): ?string
    {
        $parts = auth_cookie_parts();

        if ($parts === null) {
            return null;
        }

        $segments = explode('|', $parts['data']);
        if (count($segments) < 2) {
            return null;
        }

        return $segments[0];
    }

    function GetUserAuth(): ?string
    {
        return get_user_auth();
    }
}

if (! function_exists('set_auth_cookie')) {
    /**
     * Tạo cookie "auth" sau khi đăng nhập thành công.
     * Định dạng: data = "<username>|<expISO8601>" , hash = HMAC-SHA256(data, secretKey)
     *
     * @param  string  $username
     * @param  int|null  $hours  Số giờ sống của cookie (mặc định lấy theo config site.auth_cookie_hours)
     */
    function set_auth_cookie(string $username, ?int $hours = null): void
    {
        $hours = $hours ?? (int) config('site.auth_cookie_hours', 24);

        $exp = new \DateTime('now', new \DateTimeZone('UTC'));
        $exp->modify("+{$hours} hours");

        $data = $username.'|'.$exp->format(\DateTime::ATOM);
        $hash = get_hmac_hash($data, config('site.secret_key'));

        $value = 'data='.rawurlencode($data).'&hash='.rawurlencode($hash);

        // Cookie sống theo $hours giờ, dùng chung path "/"
        cookie()->queue('auth', $value, $hours * 60, '/', null, false, false);
    }
}

if (! function_exists('clear_auth_cookie')) {
    /**
     * Xoá cookie "auth" - dùng khi đăng xuất
     */
    function clear_auth_cookie(): void
    {
        cookie()->queue(cookie()->forget('auth'));
    }
}

if (! function_exists('seo_setting')) {
    /**
     * Lấy cài đặt SEO (meta title/description/keywords, Open Graph) của 1
     * trang ('home' hoặc 'news'), chỉnh được từ /admin/seo.
     *
     * @return array{meta_title:string,meta_description:string,meta_keywords:string,og_title:string,og_description:string,og_image:string}
     */
    function seo_setting(string $page): array
    {
        return \App\Services\SeoSettings::get($page);
    }
}

if (! function_exists('footer_settings')) {
    /**
     * Lấy cài đặt nội dung Footer (logo, thông tin sản phẩm, liên kết nhanh)
     * hiển thị ở cuối Trang chủ / Trang tin tức, chỉnh được từ /admin/footer.
     *
     * @return array{logo:string,logo_alt:string,info_lines:array<array{label:string,value:string}>,links:array<array{icon:string,label:string,url:string}>}
     */
    function footer_settings(): array
    {
        return \App\Services\FooterSettings::all();
    }
}

if (! function_exists('admin_settings')) {
    /**
     * Lấy cài đặt chung cho khu vực quản trị (tên trang quản trị, các link/cấu
     * hình trước đây nằm trong config/site.php...), chỉnh được từ /admin/cai-dat.
     *
     * @return array{admin_title:string}
     */
    function admin_settings(): array
    {
        return \App\Services\AdminSettings::all();
    }
}

if (! function_exists('site_setting')) {
    /**
     * Lấy 1 giá trị cấu hình chung của site (trước đây hardcode trong
     * config/site.php), ưu tiên giá trị đã lưu qua /admin/cai-dat
     * (App\Services\AdminSettings), nếu chưa có thì lấy mặc định từ
     * config/site.php (tính năng mới, không có trong code gốc).
     *
     * Lưu ý: các key nhạy cảm như "secret_key", "auth_cookie_hours" không
     * nằm trong AdminSettings, vẫn đọc trực tiếp từ config('site.*').
     */
    function site_setting(string $key, mixed $default = null)
    {
        $settings = \App\Services\AdminSettings::all();

        if (array_key_exists($key, $settings)) {
            return $settings[$key];
        }

        return config('site.'.$key, $default);
    }
}

if (! function_exists('daily_settings')) {
    /**
     * Lấy cài đặt giao diện khu vực Đại lý (favicon, title, tên brand
     * sidebar, nội dung footer) hiển thị ở layouts/daily.blade.php, chỉnh
     * được từ /admin/dai-ly-config.
     *
     * @return array{favicon:string,title:string,brand_text:string,footer_text:string}
     */
    function daily_settings(): array
    {
        return \App\Services\DaiLySettings::all();
    }
}

if (! function_exists('check_delay')) {
    /**
     * Chống spam click liên tục - tương đương ClassHeader1.CheckDelay(int time = 5)
     * Trả về true nếu được phép thực hiện hành động (đã quá $time giây kể từ lần trước).
     */
    function check_delay(int $time = 5): bool
    {
        $now = microtime(true);
        $last = session('LastClick');

        if ($last !== null && ($now - (float) $last) < $time) {
            return false;
        }

        session(['LastClick' => $now]);

        return true;
    }

    function CheckDelay(int $time = 5): bool
    {
        return check_delay($time);
    }
}
