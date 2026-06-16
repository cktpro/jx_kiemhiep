<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\AdminSettings;
use App\Services\DaiLySettings;
use App\Services\FooterSettings;
use App\Services\ImageUploadService;
use App\Services\SeoSettings;
use Illuminate\Http\Request;

/**
 * Cài đặt khu vực quản trị: SEO, cài đặt chung, footer, giao diện đại lý.
 * Tách từ AdminController (cũ). Dùng ImageUploadService thay cho uploadBackgroundImage().
 */
class SettingsController extends Controller
{
    // ── SEO ───────────────────────────────────────────────────────────────

    public function seoIndex(Request $request)
    {
        return view('admin.seo', [
            'settings' => SeoSettings::all(),
            'saved'    => $request->session()->get('seo_saved'),
        ]);
    }

    public function seoSave(Request $request)
    {
        $page = $request->input('page');

        if (in_array($page, SeoSettings::PAGES, true)) {
            SeoSettings::save($page, $request->only([
                'meta_title', 'meta_description', 'meta_keywords',
                'og_title', 'og_description', 'og_image',
            ]));
        }

        return redirect()->route('admin.seo')->with('seo_saved', $page);
    }

    // ── Cài đặt chung ─────────────────────────────────────────────────────

    public function generalIndex(Request $request)
    {
        return view('admin.general', [
            'settings' => AdminSettings::all(),
            'saved'    => $request->session()->get('general_saved', false),
        ]);
    }

    public function generalSave(Request $request)
    {
        $data = $request->only([
            'admin_title', 'admin_footer_text',
            'link_facebook', 'link_zalo', 'link_tiktok', 'link_youtube',
            'link_tai_game', 'link_download_android', 'link_download_ios', 'link_download_default',
            'phone_otp', 'footer1', 'max_acc_len', 'min_acc_len',
            'bg_desktop', 'bg_mobile',
            'banner_news', 'favicon', 'nav_items',
        ]);

        // Upload ảnh — ưu tiên file upload, giữ URL text nếu không có file
        $imageFields = [
            'bg_desktop' => 'background',
            'bg_mobile'  => 'background',
            'banner_news'    => 'background',
            'favicon'        => 'background',
        ];

        foreach ($imageFields as $field => $subDir) {
            $exts     = $field === 'favicon' ? ImageUploadService::FAVICON_EXTS : ImageUploadService::IMAGE_EXTS;
            $uploaded = ImageUploadService::uploadFromRequest($request, $field . '_file', $subDir, $field, $exts);

            if ($uploaded !== null) {
                $data[$field] = $uploaded;
            }
        }

        AdminSettings::save($data);

        return redirect()->route('admin.general')->with('general_saved', true);
    }

    // ── Footer ────────────────────────────────────────────────────────────

    public function footerIndex(Request $request)
    {
        return view('admin.footer', [
            'settings' => FooterSettings::all(),
            'saved'    => $request->session()->get('footer_saved', false),
        ]);
    }

    public function footerSave(Request $request)
    {
        $data     = $request->only(['logo_alt', 'info_lines', 'links']);
        $uploaded = ImageUploadService::uploadFromRequest($request, 'logo', 'footer', 'logo');

        if ($uploaded !== null) {
            $data['logo'] = $uploaded;
        }

        FooterSettings::save($data);

        return redirect()->route('admin.footer')->with('footer_saved', true);
    }

    // ── Giao diện Đại lý ──────────────────────────────────────────────────

    public function dailyIndex(Request $request)
    {
        return view('admin.daily', [
            'settings'  => DaiLySettings::all(),
            'seo'       => SeoSettings::get('daily'),
            'saved'     => $request->session()->get('daily_saved', false),
            'seoSaved'  => $request->session()->get('seo_saved'),
        ]);
    }

    public function dailySave(Request $request)
    {
        $data     = $request->only(['title', 'brand_text', 'footer_text']);
        $uploaded = ImageUploadService::uploadFromRequest(
            $request, 'favicon', 'daily', 'favicon', ImageUploadService::FAVICON_EXTS
        );

        if ($uploaded !== null) {
            $data['favicon'] = $uploaded;
        }

        DaiLySettings::save($data);

        return redirect()->route('admin.daily-config')->with('daily_saved', true);
    }
}
