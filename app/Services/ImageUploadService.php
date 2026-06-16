<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

/**
 * Xử lý upload ảnh (background, logo, favicon, slide...) cho khu vực quản trị.
 * Trước đây logic này được copy-paste trong nhiều method của AdminController.
 */
class ImageUploadService
{
    public const DEFAULT_MAX_SIZE = 5 * 1024 * 1024; // 5 MB

    public const IMAGE_EXTS   = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg'];
    public const FAVICON_EXTS = ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'ico'];

    /**
     * Upload file từ request field và lưu vào public/img/{subDir}/.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  string  $field      Tên input file trong form
     * @param  string  $subDir     Thư mục con trong public/img/ (vd: "background", "footer")
     * @param  string  $prefix     Tiền tố đặt tên file (mặc định = $field)
     * @param  array   $allowedExts  Danh sách đuôi file cho phép
     * @return string|null  Đường dẫn public (/img/...) nếu upload thành công, null nếu không
     */
    public static function uploadFromRequest(
        $request,
        string $field,
        string $subDir,
        string $prefix = '',
        array $allowedExts = self::IMAGE_EXTS
    ): ?string {
        $file = $request->file($field);

        if (! $file) {
            return null;
        }

        return self::store($file, $subDir, $prefix ?: $field, $allowedExts);
    }

    /**
     * Lưu một UploadedFile vào public/img/{subDir}/.
     *
     * @return string|null  Đường dẫn public nếu thành công, null nếu không hợp lệ
     */
    public static function store(
        UploadedFile $file,
        string $subDir,
        string $prefix = 'upload',
        array $allowedExts = self::IMAGE_EXTS,
        int $maxSize = self::DEFAULT_MAX_SIZE
    ): ?string {
        if (! $file->isValid() || $file->getSize() > $maxSize) {
            return null;
        }

        $ext = strtolower((string) $file->getClientOriginalExtension());

        if (! in_array($ext, $allowedExts, true)) {
            return null;
        }

        $dir = public_path("img/{$subDir}");

        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = $prefix . '-' . time() . '.' . $ext;
        $file->move($dir, $filename);

        return "/img/{$subDir}/{$filename}";
    }
}
