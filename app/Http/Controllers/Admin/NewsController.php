<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\News;
use Illuminate\Http\Request;

/**
 * Quản lý tin tức trong khu vực quản trị (AdminPageNews + AdminPageNewMNG).
 * Tách từ AdminController (cũ).
 */
class NewsController extends Controller
{
    /**
     * GET /admin/tin-tuc
     */
    public function index()
    {
        $newsList = News::orderByDesc('id')->take(30)->get();

        return view('admin.news.index', ['newsList' => $newsList]);
    }

    /**
     * GET /admin/tin-tuc/form[?id=]
     */
    public function form(Request $request)
    {
        $id   = $request->query('id');
        $news = $id !== null ? News::find($id) : null;

        return view('admin.news.form', [
            'news'       => $news,
            'id'         => $id,
            'categories' => Category::all(),
            'message'    => null,
        ]);
    }

    /**
     * POST /admin/tin-tuc/form[?id=]
     */
    public function save(Request $request)
    {
        $id    = $request->query('id');
        $news  = $id !== null ? News::find($id) : null;

        $title      = trim((string) $request->input('title'));
        $content    = trim((string) $request->input('content'));
        $subContent = trim((string) $request->input('sub_content'));
        $categoryId = (int) $request->input('category_id');

        $viewData = [
            'news'       => $news,
            'id'         => $id,
            'categories' => Category::all(),
        ];

        if ($title === '' || $content === '' || $subContent === '') {
            return view('admin.news.form', $viewData + [
                'message' => 'Vui lòng nhập đầy đủ thông tin bài viết!',
            ]);
        }

        if (mb_strlen($title) >= 250 || mb_strlen($subContent) >= 500) {
            return view('admin.news.form', $viewData + [
                'message' => 'Tiêu đề hoặc tóm tắt quá dài: Giới hạn tiêu đề 255 ký tự, tóm tắt 500 ký tự',
            ]);
        }

        if ($id !== null && $news) {
            $news->title       = $title;
            $news->fkcontent   = $content;
            $news->fksubcontent = $subContent;
            $news->date        = now();
            $news->categoryId  = $categoryId;
            $news->save();
        } else {
            News::create([
                'title'        => $title,
                'fkcontent'    => $content,
                'fksubcontent' => $subContent,
                'date'         => now(),
                'categoryId'   => $categoryId,
            ]);
        }

        return redirect()->route('admin.news.index');
    }

    /**
     * POST /admin/tin-tuc/xoa?id=
     */
    public function delete(Request $request)
    {
        $news = News::find($request->query('id'));
        $news?->delete();

        return redirect()->route('admin.news.index');
    }
}
