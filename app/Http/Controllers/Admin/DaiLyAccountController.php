<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CardHistory;
use App\Models\DaiLyKnb;
use Illuminate\Http\Request;

/**
 * Quản lý tài khoản Đại lý trong khu vực quản trị (bảng DaiLyKNB).
 * Tách từ AdminController (cũ).
 */
class DaiLyAccountController extends Controller
{
    /**
     * GET /admin/dai-ly
     */
    public function index(Request $request)
    {
        $keyword = trim((string) $request->query('q', ''));
        $query   = DaiLyKnb::query();

        if ($keyword !== '') {
            $query->where(function ($q) use ($keyword) {
                $q->where('TenDangNhap', 'like', "%{$keyword}%")
                  ->orWhere('HoVaTen',   'like', "%{$keyword}%")
                  ->orWhere('Phone',     'like', "%{$keyword}%");
            });
        }

        return view('admin.dai-ly.index', [
            'daiLyList'    => $query->orderByDesc('ID')->take(200)->get(),
            'keyword'      => $keyword,
            'saved'        => $request->session()->get('dai_ly_saved'),
            'topupSuccess' => $request->session()->get('dai_ly_topup_success'),
            'topupError'   => $request->session()->get('dai_ly_topup_error'),
        ]);
    }

    /**
     * GET /admin/dai-ly/form[?id=]
     */
    public function form(Request $request)
    {
        $id    = $request->query('id');
        $daiLy = $id !== null ? DaiLyKnb::find($id) : null;

        return view('admin.dai-ly.form', [
            'daiLy'   => $daiLy,
            'id'      => $id,
            'message' => null,
        ]);
    }

    /**
     * POST /admin/dai-ly/form[?id=]
     */
    public function save(Request $request)
    {
        $id          = $request->query('id');
        $daiLy       = $id !== null ? DaiLyKnb::find($id) : null;
        $tenDangNhap = trim((string) $request->input('TenDangNhap'));
        $matKhau     = trim((string) $request->input('MatKhau'));

        $viewData = ['daiLy' => $daiLy, 'id' => $id];

        if ($tenDangNhap === '') {
            return view('admin.dai-ly.form', $viewData + ['message' => 'Vui lòng nhập tên đăng nhập!']);
        }

        if (! $daiLy && $matKhau === '') {
            return view('admin.dai-ly.form', $viewData + ['message' => 'Vui lòng nhập mật khẩu cho đại lý mới!']);
        }

        $exists = DaiLyKnb::where('TenDangNhap', $tenDangNhap)
            ->when($daiLy, fn ($q) => $q->where('ID', '!=', $daiLy->ID))
            ->exists();

        if ($exists) {
            return view('admin.dai-ly.form', $viewData + ['message' => 'Tên đăng nhập đã được sử dụng, vui lòng chọn tên khác!']);
        }

        $data = [
            'TenDangNhap' => $tenDangNhap,
            'HoVaTen'     => trim((string) $request->input('HoVaTen')),
            'NganHang'    => trim((string) $request->input('NganHang')),
            'SoTaiKhoan'  => trim((string) $request->input('SoTaiKhoan')),
            'ChiNhanh'    => trim((string) $request->input('ChiNhanh')),
            'Zalo'        => trim((string) $request->input('Zalo')),
            'Phone'       => trim((string) $request->input('Phone')),
            'Facebook'    => trim((string) $request->input('Facebook')),
            'iYuanBao'    => (int) $request->input('iYuanBao', 0),
            'IsAdmin'     => (int) $request->input('IsAdmin', 0),
            'ChietKhau'   => (int) $request->input('ChietKhau', 0),
            'KichHoat'    => $request->boolean('KichHoat'),
        ];

        if ($matKhau !== '') {
            $data['MatKhau'] = $matKhau;
        }

        $daiLy ? $daiLy->fill($data)->save() : DaiLyKnb::create($data + ['MatKhau' => $matKhau]);

        return redirect()->route('admin.dai-ly.index')->with('dai_ly_saved', true);
    }

    /**
     * POST /admin/dai-ly/xoa?id=
     */
    public function delete(Request $request)
    {
        DaiLyKnb::find($request->query('id'))?->delete();

        return redirect()->route('admin.dai-ly.index')->with('dai_ly_saved', true);
    }

    /**
     * POST /admin/dai-ly/kich-hoat?id= — đảo trạng thái kích hoạt.
     */
    public function toggle(Request $request)
    {
        $daiLy = DaiLyKnb::find($request->query('id'));

        if ($daiLy) {
            $daiLy->KichHoat = ! $daiLy->KichHoat;
            $daiLy->save();
        }

        return redirect()->route('admin.dai-ly.index', $request->only('q'));
    }

    /**
     * POST /admin/dai-ly/nap?id= — Admin nạp KNB trực tiếp cho đại lý.
     */
    public function topUp(Request $request)
    {
        $id           = $request->query('id');
        $soKnb        = (int) $request->input('so_knb');
        $redirectParams = $request->only('q');

        if ($id === null) {
            return redirect()->route('admin.dai-ly.index', $redirectParams)
                ->with('dai_ly_topup_error', 'Thiếu thông tin đại lý!');
        }

        $daiLy = DaiLyKnb::find($id);

        if (! $daiLy) {
            return redirect()->route('admin.dai-ly.index', $redirectParams)
                ->with('dai_ly_topup_error', 'Không tìm thấy đại lý!');
        }

        if ($soKnb <= 0) {
            return redirect()->route('admin.dai-ly.index', $redirectParams)
                ->with('dai_ly_topup_error', 'Vui lòng nhập số KNB lớn hơn 0!');
        }

        $knbTruoc      = (int) $daiLy->iYuanBao;
        $daiLy->iYuanBao = $knbTruoc + $soKnb;
        $daiLy->save();

        CardHistory::create([
            'cCardCode' => (string) $request->session()->get('admin_user'),
            'dDate'     => now(),
            'cUserName' => $daiLy->TenDangNhap,
            'iFlag'     => $soKnb,
            'Money'     => $soKnb * 1000,
            'KNBTruoc'  => $knbTruoc,
            'KNBSau'    => (int) $daiLy->iYuanBao,
        ]);

        return redirect()->route('admin.dai-ly.index', $redirectParams)
            ->with('dai_ly_topup_success', "Đã nạp [{$soKnb}] KNB cho đại lý [{$daiLy->TenDangNhap}]. Số dư mới: [{$daiLy->iYuanBao}] KNB.");
    }
}
