<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

/**
 * Danh sách ảnh slide "TÍNH NĂNG ĐẶC SẮC" trên trang chủ, chỉnh được từ
 * /admin/slides. Lưu dưới dạng file JSON trong storage/app (cùng cách làm
 * với SeoSettings) - không cần thêm bảng vào database SQL Server gốc.
 *
 * Mỗi slide: ['id' => int, 'image' => string (đường dẫn ảnh, vd /img/slides/xxx.webp),
 *              'alt' => string, 'link' => string|null].
 *
 * Nếu chưa có file lưu, dùng 8 ảnh hiện có trong /img/ làm danh sách mặc định.
 */
class SlideSettings
{
    private const FILE = 'slides.json';

    /**
     * Danh sách slide mặc định - tương đương 8 ảnh hardcode trước đây trong
     * home/index.blade.php.
     */
    public static function defaults(): array
    {
        return self::withIds([
            ['image' => '/img/giao-dien-nhan-vat.webp', 'alt' => 'Giao diện nhân vật', 'link' => null],
            ['image' => '/img/he-thong-auto.webp', 'alt' => 'Hệ thống Auto', 'link' => null],
            ['image' => '/img/he-thong-auto-ingame.webp', 'alt' => 'Hệ thống Auto Ingame', 'link' => null],
            ['image' => '/img/he-thong-giao-dien.webp', 'alt' => 'Hệ thống giao diện', 'link' => null],
            ['image' => '/img/he-thong-ky-nang.webp', 'alt' => 'Hệ thống kỹ năng', 'link' => null],
            ['image' => '/img/he-thong-may-chu.webp', 'alt' => 'Hệ thống máy chủ', 'link' => null],
            ['image' => '/img/he-thong-uy-thac.webp', 'alt' => 'Hệ thống uỷ thác', 'link' => null],
            ['image' => '/img/trang-bi-vat-pham.webp', 'alt' => 'Trang bị vật phẩm', 'link' => null],
        ]);
    }

    /**
     * Toàn bộ slide hiện tại (đã lưu, hoặc mặc định nếu chưa có/lỗi).
     */
    public static function all(): array
    {
        if (! Storage::disk('local')->exists(self::FILE)) {
            return self::defaults();
        }

        $content = Storage::disk('local')->get(self::FILE);
        $slides = json_decode($content, true);

        if (! is_array($slides) || empty($slides)) {
            return self::defaults();
        }

        return array_values($slides);
    }

    public static function find(int $id): ?array
    {
        foreach (self::all() as $slide) {
            if ((int) ($slide['id'] ?? 0) === $id) {
                return $slide;
            }
        }

        return null;
    }

    /**
     * Thêm slide mới, trả về slide vừa tạo (đã có id).
     */
    public static function create(array $data): array
    {
        $slides = self::all();

        $nextId = 1;
        foreach ($slides as $slide) {
            $nextId = max($nextId, (int) ($slide['id'] ?? 0) + 1);
        }

        $slide = [
            'id' => $nextId,
            'image' => $data['image'],
            'alt' => trim((string) ($data['alt'] ?? '')),
            'link' => self::normalizeLink($data['link'] ?? null),
        ];

        $slides[] = $slide;
        self::write($slides);

        return $slide;
    }

    /**
     * Cập nhật 1 slide. Nếu $data['image'] rỗng/null, giữ nguyên ảnh cũ.
     */
    public static function update(int $id, array $data): bool
    {
        $slides = self::all();
        $found = false;

        foreach ($slides as &$slide) {
            if ((int) ($slide['id'] ?? 0) === $id) {
                $slide['alt'] = trim((string) ($data['alt'] ?? ($slide['alt'] ?? '')));
                $slide['link'] = self::normalizeLink($data['link'] ?? null);

                if (! empty($data['image'])) {
                    $slide['image'] = $data['image'];
                }

                $found = true;
                break;
            }
        }

        if ($found) {
            self::write($slides);
        }

        return $found;
    }

    public static function delete(int $id): bool
    {
        $slides = self::all();
        $remaining = array_values(array_filter(
            $slides,
            fn ($slide) => (int) ($slide['id'] ?? 0) !== $id
        ));

        if (count($remaining) === count($slides)) {
            return false;
        }

        self::write($remaining);

        return true;
    }

    /**
     * Đổi vị trí 1 slide trong danh sách (lên/xuống).
     * $direction: -1 = lên, +1 = xuống.
     */
    public static function move(int $id, int $direction): bool
    {
        $slides = self::all();
        $index = null;

        foreach ($slides as $i => $slide) {
            if ((int) ($slide['id'] ?? 0) === $id) {
                $index = $i;
                break;
            }
        }

        if ($index === null) {
            return false;
        }

        $target = $index + $direction;

        if ($target < 0 || $target >= count($slides)) {
            return false;
        }

        [$slides[$index], $slides[$target]] = [$slides[$target], $slides[$index]];
        self::write($slides);

        return true;
    }

    private static function normalizeLink(?string $link): ?string
    {
        $link = trim((string) $link);

        return $link === '' ? null : $link;
    }

    private static function withIds(array $slides): array
    {
        foreach ($slides as $i => &$slide) {
            $slide['id'] = $i + 1;
        }

        return $slides;
    }

    private static function write(array $slides): void
    {
        Storage::disk('local')->put(
            self::FILE,
            json_encode(array_values($slides), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)
        );
    }
}
