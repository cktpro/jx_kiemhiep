<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use RuntimeException;

/**
 * Upload file (APK, ảnh, zip...) từ /admin/cai-dat vào public/download/ để
 * lấy link chia sẻ.
 *
 * Upload chia khúc (CHUNK_SIZE mỗi request) vì server giới hạn 50MB/request
 * (PHP + nginx) và Cloudflare giới hạn 100MB/request, trong khi file APK
 * thường > 100MB. Các khúc được ghép dần vào storage/app/private/chunks/,
 * xong khúc cuối mới chuyển sang public/download/.
 */
class FileUploadService
{
    public const CHUNK_SIZE = 5 * 1024 * 1024;      // 5 MB mỗi khúc

    public const MAX_SIZE = 500 * 1024 * 1024;      // 500 MB mỗi file

    /** Chỉ cho phép các đuôi này - tuyệt đối không có php/html/svg/js. */
    public const ALLOWED_EXTS = [
        'apk', 'ipa', 'xapk', 'obb',
        'zip', 'rar', '7z',
        'jpg', 'jpeg', 'png', 'webp', 'gif',
        'mp4', 'webm', 'mp3',
        'pdf', 'txt',
    ];

    public const PUBLIC_DIR = 'download';

    public static function dir(): string
    {
        return public_path(self::PUBLIC_DIR);
    }

    private static function chunkDir(): string
    {
        return storage_path('app/private/chunks');
    }

    /**
     * Danh sách file đã upload, mới nhất trước.
     *
     * @return array<int, array{name:string,size:int,mtime:int,path:string,url:string}>
     */
    public static function list(): array
    {
        $dir = self::dir();

        if (! is_dir($dir)) {
            return [];
        }

        $files = [];

        foreach (scandir($dir) ?: [] as $name) {
            $full = $dir.DIRECTORY_SEPARATOR.$name;

            if ($name[0] === '.' || ! is_file($full)) {
                continue;
            }

            $files[] = [
                'name' => $name,
                'size' => (int) filesize($full),
                'mtime' => (int) filemtime($full),
                'path' => '/'.self::PUBLIC_DIR.'/'.rawurlencode($name),
                'url' => url('/'.self::PUBLIC_DIR.'/'.rawurlencode($name)),
            ];
        }

        usort($files, fn ($a, $b) => $b['mtime'] <=> $a['mtime']);

        return $files;
    }

    /**
     * Làm sạch tên file: chỉ giữ chữ, số, . _ -; đuôi phải nằm trong ALLOWED_EXTS.
     */
    public static function sanitizeName(string $name): string
    {
        $name = basename(str_replace('\\', '/', $name));
        $ext = strtolower(pathinfo($name, PATHINFO_EXTENSION));

        if (! in_array($ext, self::ALLOWED_EXTS, true)) {
            throw new RuntimeException('Định dạng file không được phép (.'.($ext ?: '?').').');
        }

        $base = pathinfo($name, PATHINFO_FILENAME);
        $base = \Illuminate\Support\Str::ascii($base);
        $base = preg_replace('/[^A-Za-z0-9._-]+/', '_', $base);
        $base = trim((string) $base, '._-');

        if ($base === '') {
            $base = 'file-'.date('Ymd-His');
        }

        return substr($base, 0, 150).'.'.$ext;
    }

    /**
     * Nhận 1 khúc. Trả về thông tin file khi đã nhận đủ (khúc cuối), null nếu chưa xong.
     *
     * @return array{name:string,size:int,path:string,url:string}|null
     */
    public static function receiveChunk(
        string $uploadId,
        int $index,
        int $total,
        string $fileName,
        int $totalSize,
        UploadedFile $chunk
    ): ?array {
        if (! preg_match('/^[a-f0-9]{32}$/', $uploadId)) {
            throw new RuntimeException('Mã upload không hợp lệ.');
        }

        if ($totalSize <= 0 || $totalSize > self::MAX_SIZE) {
            throw new RuntimeException('File vượt quá giới hạn '.(self::MAX_SIZE / 1024 / 1024).' MB.');
        }

        $expectedTotal = (int) ceil($totalSize / self::CHUNK_SIZE);

        if ($total !== $expectedTotal || $index < 0 || $index >= $total) {
            throw new RuntimeException('Thông tin khúc upload không hợp lệ.');
        }

        if (! $chunk->isValid()) {
            throw new RuntimeException('Khúc upload bị lỗi, vui lòng thử lại.');
        }

        $name = self::sanitizeName($fileName);

        $chunkDir = self::chunkDir();
        if (! is_dir($chunkDir)) {
            mkdir($chunkDir, 0755, true);
        }

        $part = $chunkDir.DIRECTORY_SEPARATOR.$uploadId.'.part';

        if ($index === 0) {
            self::cleanupStaleChunks();
            @unlink($part);
        }

        // Khúc phải tới đúng thứ tự: kích thước hiện tại = vị trí bắt đầu khúc này.
        clearstatcache(true, $part);
        $currentSize = is_file($part) ? filesize($part) : 0;

        if ($currentSize !== $index * self::CHUNK_SIZE) {
            throw new RuntimeException('Khúc upload bị sai thứ tự, vui lòng upload lại.');
        }

        $in = fopen($chunk->getRealPath(), 'rb');
        $out = fopen($part, 'ab');
        stream_copy_to_stream($in, $out);
        fclose($in);
        fclose($out);
        clearstatcache(true, $part);

        if ($index < $total - 1) {
            return null;
        }

        if (filesize($part) !== $totalSize) {
            @unlink($part);
            throw new RuntimeException('File nhận được không đủ dung lượng, vui lòng upload lại.');
        }

        $dir = self::dir();
        if (! is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $name = self::uniqueName($dir, $name);
        $dest = $dir.DIRECTORY_SEPARATOR.$name;

        if (! rename($part, $dest)) {
            @unlink($part);
            throw new RuntimeException('Không lưu được file lên server.');
        }

        @chmod($dest, 0644);

        return [
            'name' => $name,
            'size' => $totalSize,
            'path' => '/'.self::PUBLIC_DIR.'/'.rawurlencode($name),
            'url' => url('/'.self::PUBLIC_DIR.'/'.rawurlencode($name)),
        ];
    }

    /**
     * Xoá 1 file trong public/download/.
     */
    public static function delete(string $name): void
    {
        $name = basename(str_replace('\\', '/', $name));
        $full = self::dir().DIRECTORY_SEPARATOR.$name;

        if ($name === '' || $name[0] === '.' || ! is_file($full)) {
            throw new RuntimeException('Không tìm thấy file.');
        }

        if (! @unlink($full)) {
            throw new RuntimeException('Không xoá được file.');
        }
    }

    /** Không ghi đè file cũ: trùng tên thì thêm -1, -2... */
    private static function uniqueName(string $dir, string $name): string
    {
        $base = pathinfo($name, PATHINFO_FILENAME);
        $ext = pathinfo($name, PATHINFO_EXTENSION);
        $candidate = $name;
        $i = 1;

        while (file_exists($dir.DIRECTORY_SEPARATOR.$candidate)) {
            $candidate = $base.'-'.$i.'.'.$ext;
            $i++;
        }

        return $candidate;
    }

    /** Xoá các khúc upload dở dang quá 1 ngày. */
    private static function cleanupStaleChunks(): void
    {
        foreach (glob(self::chunkDir().DIRECTORY_SEPARATOR.'*.part') ?: [] as $file) {
            if (filemtime($file) < time() - 86400) {
                @unlink($file);
            }
        }
    }
}
