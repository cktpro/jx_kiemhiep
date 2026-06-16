<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountInfo;
use App\Models\CardHistory;
use App\Models\DaiLyKnb;
use Illuminate\Http\Request;

/**
 * Trang chủ quản trị + Quản lý nạp thẻ (AdminNapThe.aspx).
 * Tách từ AdminController (cũ).
 */
class HomeController extends Controller
{
    /**
     * Tài khoản được phép truy cập "Quản lý nạp thẻ".
     */
    public const NAP_THE_USERS = ['phongkieu84', 'nguyenlm'];

    /**
     * GET /admin
     */
    public function home(Request $request)
    {
        $isFullAdmin = (int) $request->session()->get('admin_role', 0) === 1;
        $canNapThe   = $isFullAdmin
            && in_array($request->session()->get('admin_user'), self::NAP_THE_USERS, true);

        return view('admin.home', ['canNapThe' => $canNapThe]);
    }

    /**
     * GET /admin/nap-the
     */
    public function napThe(Request $request)
    {
        $accstr = $request->session()->get('admin_user');

        if (! in_array($accstr, self::NAP_THE_USERS, true)) {
            return redirect()->route('admin.home');
        }

        return view('admin.nap-the', $this->buildNapTheView(
            $accstr,
            $request->session()->get('napthe_message')
        ));
    }

    /**
     * POST /admin/nap-the — Post/Redirect/Get để tránh resubmit khi F5.
     */
    public function napTheSubmit(Request $request)
    {
        $accstr = $request->session()->get('admin_user');

        if (! in_array($accstr, self::NAP_THE_USERS, true)) {
            return redirect()->route('admin.home');
        }

        $message = $this->processNapThe($request, $accstr);

        return redirect()->route('admin.napthe')->with('napthe_message', $message);
    }

    // ── Private helpers ────────────────────────────────────────────────────

    private function processNapThe(Request $request, string $accstr): string
    {
        $account = AccountInfo::where('cAccName', $accstr)->first();

        if (! $account) {
            return 'Thông báo kết quả';
        }

        $knbCon  = (int) $account->iYuanbao;
        $knbNap  = (int) $request->input('menh_gia');
        $hoVaTen = (string) $request->input('ho_va_ten');

        if ($knbCon < $knbNap) {
            return "Số KNB còn lại {$knbCon} KIM không đủ để nạp {$knbNap} KIM";
        }

        $daiLy = DaiLyKnb::where('HoVaTen', $hoVaTen)->first();

        if (! $daiLy) {
            return 'Thông tin Đại Lý không chính xác tắt trình duyệt mở lại';
        }

        $account->iYuanbao = $knbCon - $knbNap;
        $account->save();

        $knbTruoc      = (int) $daiLy->iYuanBao;
        $daiLy->iYuanBao = $knbTruoc + $knbNap;
        $daiLy->save();

        CardHistory::create([
            'cCardCode' => $accstr,
            'dDate'     => now(),
            'cUserName' => $daiLy->TenDangNhap,
            'iFlag'     => $knbNap,
            'Money'     => $knbNap * 1000,
            'KNBTruoc'  => $knbTruoc,
            'KNBSau'    => (int) $daiLy->iYuanBao,
        ]);

        return "Đã nạp thành công [{$knbNap}] KIM vào Tài khoản [{$daiLy->TenDangNhap}]";
    }

    private function buildNapTheView(string $accstr, ?string $message): array
    {
        $account        = AccountInfo::where('cAccName', $accstr)->first();
        $kimNguyenBaoCon = '';

        if ($account) {
            $soDu            = ((int) $account->iYuanbao) * 1000;
            $kimNguyenBaoCon = 'KNB hiện đang còn: ' . number_format($soDu, 0, ',', '.') . ' VNĐ';
        }

        $daiLyList = DaiLyKnb::where('TenDangNhap', '!=', $accstr)
            ->where('KichHoat', true)
            ->take(10)
            ->get();

        $history  = CardHistory::orderByDesc('iid')->get();
        $totalNap = $history->sum(fn ($item) => (int) $item->Money);

        return [
            'kimNguyenBaoCon' => $kimNguyenBaoCon,
            'daiLyList'       => $daiLyList,
            'history'         => $history,
            'totalNap'        => $totalNap,
            'naptheMessage'   => $message,
        ];
    }
}
