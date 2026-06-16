<?php

namespace App\Http\Controllers;

use App\Models\AccountInfo;
use App\Models\DaiLyKnb;
use App\Models\DaiLyNapThe;
use App\Models\DaiLyNgayVang;
use Illuminate\Http\Request;

/**
 * Nhóm trang Nạp thẻ (qua Đại lý) - port từ:
 *  - NapThe.aspx + .aspx.cs     (route "napthe1" -> /nap-the, "napthe" -> /nap-the/{id}/{sv})
 *  - NapTheV2.aspx + .aspx.cs   (route "napthe2" -> /nap-coin)
 */
class NapTheController extends Controller
{
    /**
     * GET /nap-the hoặc /nap-the/{id}/{sv} - tương đương NapThe.aspx Page_Load
     *
     * - Lấy danh sách Đại lý (DaiLyKNB IsAdmin = 0), tối đa 10
     * - Nếu có {id} trên route (route "napthe" gốc: "nap-the.{Id}.{Sv}.aspx") thì
     *   chọn sẵn đại lý theo index {id} và disable dropdown
     * - Ngược lại chọn ngẫu nhiên 1 đại lý và cho phép đổi
     */
    public function show(Request $request, ?int $id = null, ?int $sv = null)
    {
        $daiLyList = DaiLyKnb::where('IsAdmin', 0)->take(10)->get(['HoVaTen']);

        $selectedIndex = 0;
        $lockDaiLy = false;

        if ($id !== null && $daiLyList->isNotEmpty()) {
            $selectedIndex = max(0, min($id, $daiLyList->count() - 1));
            $lockDaiLy = true;
        } elseif ($daiLyList->isNotEmpty()) {
            $selectedIndex = random_int(0, $daiLyList->count() - 1);
        }

        return view('napthe.nap-the', [
            'daiLyList' => $daiLyList,
            'selectedIndex' => $selectedIndex,
            'lockDaiLy' => $lockDaiLy,
        ]);
    }

    /**
     * POST /nap-the (AJAX) - tương đương ButtonOK_Click
     */
    public function submit(Request $request)
    {
        $stracc = trim((string) $request->input('acc_name'));
        $hoVaTen = (string) $request->input('dai_ly');
        $menhGiaValue = (int) $request->input('menh_gia');
        $menhGiaText = (string) $request->input('menh_gia_text');

        if (mb_strlen($stracc) < 2) {
            return response()->json([
                'success' => false,
                'message' => 'Tên tài khoản không thể để trống',
            ]);
        }

        $account = AccountInfo::where('cAccName', $stracc)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản [{$stracc}] không chính xác",
            ]);
        }

        $daiLy = DaiLyKnb::where('HoVaTen', $hoVaTen)->first();

        if (! $daiLy) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin Đại Lý không chính xác tắt trình duyệt mở lại',
            ]);
        }

        if (! $daiLy->KichHoat) {
            return response()->json([
                'success' => false,
                'message' => 'Đại Lý đang bảo trì kênh nạp vui lòng chọn Đại Lý khác',
            ]);
        }

        $nRandom = random_int(1111, 9999);

        $soKNB = intdiv($menhGiaValue, 1000);
        $soTien = $soKNB * 1000;
        $soKNBKM = 0;

        $perChietKhau = 0;
        if ($soTien >= 400000) {
            $perChietKhau = 20;
        }

        $ngayVang = DaiLyNgayVang::where('CfName', 'NgayVangNap')->first();

        if ($ngayVang && $ngayVang->DateTime && $ngayVang->EndDate) {
            $now = now();
            if ($ngayVang->DateTime->lt($now) && $ngayVang->EndDate->gt($now)) {
                $perChietKhau = 30;
            }
        }

        if ($perChietKhau > 0) {
            $soKNBKM = intdiv($soKNB * $perChietKhau, 100);
        }

        $noiDung = "{$nRandom} {$stracc} nap {$menhGiaValue}";

        DaiLyNapThe::create([
            'AccountDL' => $daiLy->TenDangNhap,
            'AccountGammer' => $stracc,
            'DateNap' => now(),
            'TrangThai' => 0,
            'SoKNB' => $soKNB,
            'SoKNBKM' => $soKNBKM,
            'SoTien' => $soTien,
            'KNBTruoc' => $account->iYuanbao,
            'KNBSau' => $account->iYuanbao,
            'NoiDung' => $noiDung,
        ]);

        $message = "Account [{$stracc}] đăng ký nạp thành công mệnh giá [{$menhGiaText}]."
            ." <br/>B1.Chuyển tiền vào ngân hàng [{$daiLy->NganHang}], tài khoản [{$daiLy->SoTaiKhoan}],"
            ." Tên [{$daiLy->HoVaTen}], Số tiền [{$menhGiaText}], Nội dung [{$noiDung}]."
            ." <br/> B2. Chụp hình biên lai và liên hệ zalo [{$daiLy->Zalo}] để nhận KNB.";

        return response()->json([
            'success' => true,
            'message' => $message,
        ]);
    }

    /**
     * GET /nap-coin - trang hướng dẫn + form nạp KNB (gộp từ NapTheV2.aspx và NapThe.aspx).
     */
    public function showNapCoin(Request $request)
    {
        $daiLyList = DaiLyKnb::where('IsAdmin', 0)->take(10)->get(['HoVaTen']);

        $selectedIndex = $daiLyList->isNotEmpty() ? random_int(0, $daiLyList->count() - 1) : 0;

        return view('napthe.nap-coin', [
            'linkDownload' => $this->resolveDownloadLink($request),
            'daiLyList'    => $daiLyList,
            'selectedIndex' => $selectedIndex,
        ]);
    }

    private function resolveDownloadLink(Request $request): string
    {
        $ua = (string) $request->userAgent();

        if (str_contains($ua, 'Android')) {
            return site_setting('link_download_android');
        }

        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad') || str_contains($ua, 'iPod')) {
            return site_setting('link_download_ios');
        }

        return site_setting('link_download_default');
    }
}
