<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\ImageUploadService;
use App\Services\SlideSettings;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/**
 * Quản lý ảnh slide "TÍNH NĂNG ĐẶC SẮC" trang chủ.
 * Tách từ AdminController (cũ).
 */
class SlidesController extends Controller
{
    /**
     * GET /admin/slides
     */
    public function index()
    {
        return view('admin.slides.index', ['slides' => SlideSettings::all()]);
    }

    /**
     * GET /admin/slides/form[?id=]
     */
    public function form(Request $request)
    {
        $id    = $request->query('id');
        $slide = $id !== null ? SlideSettings::find((int) $id) : null;

        if ($id !== null && ! $slide) {
            return redirect()->route('admin.slides.index');
        }

        return view('admin.slides.form', ['slide' => $slide, 'id' => $id, 'message' => null]);
    }

    /**
     * POST /admin/slides/form[?id=]
     */
    public function save(Request $request)
    {
        $id    = $request->query('id');
        $slide = $id !== null ? SlideSettings::find((int) $id) : null;

        if ($id !== null && ! $slide) {
            return redirect()->route('admin.slides.index');
        }

        $viewData = ['slide' => $slide, 'id' => $id];
        $file     = $request->file('image');

        if (! $file && ! $slide) {
            return view('admin.slides.form', $viewData + ['message' => 'Vui lòng chọn ảnh cho slide!']);
        }

        $imagePath = null;

        if ($file) {
            if (! $file->isValid()) {
                return view('admin.slides.form', $viewData + ['message' => 'Tải ảnh lên không thành công, vui lòng thử lại!']);
            }

            $ext = strtolower((string) $file->getClientOriginalExtension());

            if (! in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
                return view('admin.slides.form', $viewData + ['message' => 'Chỉ hỗ trợ ảnh JPG, PNG, WEBP hoặc GIF!']);
            }

            if ($file->getSize() > ImageUploadService::DEFAULT_MAX_SIZE) {
                return view('admin.slides.form', $viewData + ['message' => 'Kích thước ảnh tối đa 5MB!']);
            }

            $prefix    = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
            $imagePath = ImageUploadService::store($file, 'slides', $prefix);
        }

        $data = ['alt' => trim((string) $request->input('alt')), 'link' => trim((string) $request->input('link'))];

        if ($imagePath !== null) {
            $data['image'] = $imagePath;
        }

        $slide ? SlideSettings::update((int) $slide['id'], $data) : SlideSettings::create($data);

        return redirect()->route('admin.slides.index');
    }

    /**
     * POST /admin/slides/xoa?id=
     */
    public function delete(Request $request)
    {
        if ($request->query('id') !== null) {
            SlideSettings::delete((int) $request->query('id'));
        }

        return redirect()->route('admin.slides.index');
    }

    /**
     * POST /admin/slides/sap-xep
     */
    public function reorder(Request $request)
    {
        $direction = $request->input('direction') === 'up' ? -1 : 1;
        SlideSettings::move((int) $request->input('id'), $direction);

        return redirect()->route('admin.slides.index');
    }
}
