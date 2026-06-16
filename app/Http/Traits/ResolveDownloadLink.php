<?php

namespace App\Http\Traits;

use Illuminate\Http\Request;

/**
 * Chọn link tải game phù hợp theo thiết bị người dùng (Android / iOS / khác).
 * Trước đây resolveDownloadLink() bị copy-paste trong AuthController và AccountController.
 */
trait ResolveDownloadLink
{
    protected function resolveDownloadLink(Request $request): string
    {
        $ua = (string) $request->userAgent();

        if (str_contains($ua, 'Android')) {
            return (string) site_setting('link_download_android');
        }

        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') || str_contains($ua, 'iPod')) {
            return (string) site_setting('link_download_ios');
        }

        return (string) site_setting('link_download_default');
    }
}
