<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\News;
use App\Services\SeoSettings;
use Illuminate\Http\Request;

/**
 * Nhóm trang Tin tức - port từ:
 *  - ChiTietTinV2.aspx + .aspx.cs (route "chitiettin": "tin-tuc/{link}.{Id}.aspx" -> ChiTietTinV2.aspx)
 *  - DanhSachTin.aspx + .aspx.cs  (route "alltin": "tin-tuc/all.aspx" -> DanhSachTin.aspx)
 *
 * Database: jxm_news (connection "mysql_news") - bảng FK_News + Category.
 */
class NewsController extends Controller
{
    /**
     * GET /tin-tuc/{slug} - tương đương ChiTietTinV2.aspx Page_Load + GetChiTietTin()
     * Query theo cột slug trong DB (đã được populate từ title).
     */
    public function show(Request $request, string $slug)
    {
        $news = News::with('category')->where('slug', $slug)->first();

        abort_if(! $news, 404);

        // Tin mới nhất cho sidebar - tương đương Load_List_Tin() trên Default.aspx,
        // loại bỏ bài đang xem.
        $latestNews = News::where('id', '!=', $news->id)
            ->orderByDesc('date')
            ->take(6)
            ->get();

        // Cài đặt SEO cho "Trang chi tiết tin" (mẫu meta_title/meta_description/
        // og_title/og_description dùng placeholder {title}/{description}),
        // chỉnh được ở /admin/seo.
        $seoNewsDetail = SeoSettings::get('news_detail');

        $placeholders = [
            'title' => $news->title,
            'description' => $news->meta_description,
        ];

        // Ảnh og:image - lấy ảnh đầu tiên trong bài viết; nếu bài viết không
        // có ảnh nào (og_image trả về ảnh mặc định của model) thì dùng ảnh
        // mặc định cấu hình ở /admin/seo.
        $articleOgImage = $news->og_image;
        $ogImage = $articleOgImage !== News::DEFAULT_OG_IMAGE ? $articleOgImage : $seoNewsDetail['og_image'];

        return view('news.show', [
            'news' => $news,
            'categoryName' => $news->category?->Name,
            'latestNews' => $latestNews,
            'metaTitle' => SeoSettings::applyPlaceholders($seoNewsDetail['meta_title'], $placeholders),
            'metaDescription' => SeoSettings::applyPlaceholders($seoNewsDetail['meta_description'], $placeholders),
            'ogTitle' => SeoSettings::applyPlaceholders($seoNewsDetail['og_title'], $placeholders),
            'ogDescription' => SeoSettings::applyPlaceholders($seoNewsDetail['og_description'], $placeholders),
            'ogImage' => $ogImage,
        ]);
    }

    /**
     * GET /tin-tuc/all - tương đương DanhSachTin.aspx.cs GetDanhSachTin() + PhanTrangDanhSachTin()
     * (10 bài / trang, sắp xếp theo ngày giảm dần).
     *
     * Bổ sung tab lọc theo chuyên mục (Sự kiện, Hướng dẫn, Tin tức...) qua
     * query string ?category= - tính năng mới, không có trong bản gốc.
     */
    public function index(Request $request)
    {
        $categoryId = $request->query('category');
        $categoryId = ($categoryId !== null && $categoryId !== '') ? (int) $categoryId : null;

        $news = News::query()
            ->when($categoryId, fn ($q) => $q->where('categoryId', $categoryId))
            ->orderByDesc('date')
            ->paginate(10)
            ->withQueryString();

        return view('news.index', [
            'news' => $news,
            'categories' => Category::all(),
            'activeCategory' => $categoryId,
        ]);
    }
}
