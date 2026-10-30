<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng FK_News (database "jxm_news", MariaDB)
 *
 * Dùng cho các trang Tin tức / Trang chủ (DefaultV2, ChiTietTinV2, DanhSachTin).
 */
class News extends Model
{
    /**
     * Ảnh og:image mặc định khi không tìm thấy ảnh nào trong nội dung bài
     * viết - có thể tuỳ chỉnh ở /admin/seo (tab "Trang chi tiết tin").
     */
    public const DEFAULT_OG_IMAGE = '/images/share-nhtl.png';

    protected $connection = 'mysql_news';

    protected $table = 'fk_news';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['id'];

    protected static function boot(): void
    {
        parent::boot();

        // Tự động sinh slug khi tạo bài mới (dùng created() vì id chưa có
        // lúc creating()). Khi update title cũng tái sinh để giữ slug đồng bộ.
        static::created(function (self $model): void {
            if (empty($model->slug)) {
                $model->slug = static::uniqueSlug($model->title, $model->id);
                $model->saveQuietly();
            }
        });

        static::updating(function (self $model): void {
            // Chỉ tự sinh lại khi title thay đổi mà admin KHÔNG tự nhập slug mới,
            // hoặc khi slug bị trống.
            if (($model->isDirty('title') && ! $model->isDirty('slug')) || empty($model->slug)) {
                $model->slug = static::uniqueSlug($model->title, $model->id);
            }
        });
    }

    /**
     * Sinh slug duy nhất: nếu slug từ title bị trùng với bài khác thì thêm
     * hậu tố "-{id}".
     */
    public static function uniqueSlug(string $title, int $id): string
    {
        $base = slugify_vn($title);

        $exists = static::where('slug', $base)->where('id', '!=', $id)->exists();

        return $exists ? $base . '-' . $id : $base;
    }

    protected function casts(): array
    {
        return [
            'date' => 'datetime',
            'categoryId' => 'integer',
        ];
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryId', 'Id');
    }

    /**
     * Slug dùng cho URL /tin-tuc/{slug}.{id}.
     * Ưu tiên cột slug đã lưu trong DB; fallback sang compute từ title nếu
     * chưa có (bài cũ trước khi chạy migration).
     */
    public function getSlugAttribute(): string
    {
        return (string) ($this->attributes['slug'] ?? slugify_vn((string) $this->title));
    }

    /**
     * URL chi tiết bài viết - /tin-tuc/{slug}
     */
    public function getUrlAttribute(): string
    {
        return '/tin-tuc/'.$this->slug;
    }

    /**
     * Đoạn mô tả ngắn dùng cho meta description - tương đương đoạn xử lý
     * subcontent trong ChiTietTinV2.aspx.cs Page_Load.
     */
    public function getMetaDescriptionAttribute(): string
    {
        $subcontent = '';

        if (! empty($this->fkcontent)) {
            $subcontent = mb_strlen($this->fkcontent) > 30
                ? mb_substr($this->fkcontent, 0, 30)
                : $this->fkcontent;
        }

        if (! empty($this->fksubcontent) && mb_strlen($this->fksubcontent) > 6) {
            $subcontent = $this->fksubcontent;
        }

        return $subcontent;
    }

    /**
     * Ảnh og:image - lấy link ảnh (http/https) đầu tiên trong nội dung bài
     * viết (fkcontent), tương đương FetchImgsFromSource() trong
     * ChiTietTinV2.aspx.cs.
     *
     * Chỉ nhận src dạng link tuyệt đối (http/https) vì Open Graph (Facebook,
     * Zalo...) yêu cầu URL ảnh đầy đủ - bỏ qua ảnh dán trực tiếp dạng base64
     * (src="data:image/...") hoặc đường dẫn tương đối.
     */
    public function getOgImageAttribute(): string
    {
        if (! empty($this->fkcontent)) {
            if (preg_match('/<img[^>]*?src\s*=\s*["\'](https?:\/\/[^"\']+)["\']/i', $this->fkcontent, $m)) {
                return $m[1];
            }
        }

        return self::DEFAULT_OG_IMAGE;
    }
}
