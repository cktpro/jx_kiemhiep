<?php

namespace App\Http\Controllers;

use App\Models\AccountInfo;
use App\Models\CardHistory;
use App\Models\DaiLyKnb;
use App\Models\DaiLyNapThe;
use App\Models\LichSuDoiSdt;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Nhóm trang quản lý của Đại lý - port từ:
 *  - DaiLyNapThe.aspx + .aspx.cs (class DaiLyNapThe1) -> /dai-ly-nap-the
 *  - TongDaiLy.aspx + .aspx.cs                        -> /tong-dai-ly
 *
 * Tất cả route trong controller này yêu cầu đăng nhập Đại lý (middleware
 * "daily.auth" - xem App\Http\Middleware\EnsureDaiLyLoggedIn). Middleware này
 * đặt sẵn $request->attributes->get('daiLy') = DaiLyKnb hiện tại.
 */
class DaiLyController extends Controller
{
    /**
     * GET /dai-ly-nap-the - tương đương DaiLyNapThe.aspx Page_Load
     */
    public function napThe(Request $request)
    {
        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        $message = null;

        // Xử lý xác nhận nạp KNB qua link "?id=" (Request.QueryString["id"])
        if ($request->query('id') !== null) {
            $result = $this->confirmTopUp($daiLy, (string) $request->query('id'));

            if ($result instanceof RedirectResponse) {
                return $result;
            }

            if ($result === null) {
                // Xử lý thành công -> redirect về trang sạch (giống Response.Redirect("/DaiLyNapThe.aspx"))
                return redirect('/dai-ly-nap-the');
            }

            $message = $result;
            $daiLy->refresh();
        }

        // (tính năng mới, không có trong code gốc) Xử lý hủy khoản nạp đang
        // chờ qua link "?cancel_id="
        if ($request->query('cancel_id') !== null) {
            $result = $this->cancelTopUp($daiLy, (string) $request->query('cancel_id'));

            if ($result instanceof RedirectResponse) {
                return $result;
            }

            if ($result === null) {
                // Xử lý thành công -> redirect về trang sạch (giống Response.Redirect("/DaiLyNapThe.aspx"))
                return redirect('/dai-ly-nap-the');
            }

            $message = $result;
        }

        $tenTaiKhoan = $daiLy->HoVaTen;
        $iYuanbao = (int) $daiLy->iYuanBao;
        $isAdmin = (int) $daiLy->IsAdmin;

        // Lưu ý: không còn set $message mặc định hiển thị số dư đại lý ở đây nữa
        // (đã hiển thị qua info-box "KNB hiện có" ở đầu trang) - $message chỉ
        // còn dùng để hiển thị kết quả xác nhận nạp KNB (?id=...).

        // (tính năng mới, không có trong code gốc) Lọc lịch sử nạp theo
        // khoảng "Từ ngày" - "Đến ngày", hiển thị / nhận vào theo định dạng
        // DD/MM/YYYY (xem buildHistory() để biết cách parse).
        $today = $request->query('today', now()->format('d/m/Y'));
        $endDate = $request->query('end_date', now()->format('d/m/Y'));

        // (tính năng mới, không có trong code gốc) Chỉ áp dụng lọc theo ngày
        // cho bảng "Lịch sử nạp" khi người dùng chủ động submit form lọc
        // (URL có "today"/"end_date") - nếu tải lại URL sạch /dai-ly-nap-the
        // thì bảng vẫn hiển thị đầy đủ như trước, không bị kẹt ở "dạng đã lọc".
        $hasDateFilter = $request->has('today') || $request->has('end_date');

        // Dùng $today/$endDate (đã áp giá trị mặc định) để "Tổng tiền nạp" /
        // "Đã chuyển KIM" luôn khớp với khoảng ngày đang hiển thị trên form lọc.
        $history = $this->buildHistory($daiLy, $isAdmin, $today, $endDate, $hasDateFilter);

        return view('dai-ly.nap-the', [
            'tenTaiKhoan' => $tenTaiKhoan,
            'sodienthoai' => $daiLy->Phone,
            'iYuanbao' => $iYuanbao,
            'isAdmin' => $isAdmin,
            'message' => $message,
            'today' => $today,
            'endDate' => $endDate,
            'history' => $history,
        ]);
    }

    /**
     * POST /dai-ly-nap-the/tim-sdt (AJAX) - tương đương ButtonTim_Click
     */
    public function searchPhone(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thủ lại trong giây lát',
            ]);
        }

        $accName = trim((string) $request->input('acc_name'));
        $account = AccountInfo::where('cAccName', $accName)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản [{$accName}] không chính xác!",
            ]);
        }

        if (empty($account->cPhone)) {
            return response()->json([
                'success' => true,
                'phone' => '',
                'message' => "Tên tài khoản [{$accName}] chưa đăng ký số điện thoại bảo vệ!",
            ]);
        }

        return response()->json([
            'success' => true,
            'phone' => $account->cPhone,
            'message' => '',
        ]);
    }

    /**
     * POST /dai-ly-nap-the/xem-pass (AJAX) - tương đương ButtonVPass_Clicks
     */
    public function showPassword(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thủ lại trong giây lát',
            ]);
        }

        $accName = trim((string) $request->input('acc_name'));

        if (mb_strlen($accName) < 6 || str_contains($accName, 'colauhong')) {
            return response()->json([
                'success' => false,
                'message' => 'Tên tài khoản không hợp lệ!',
            ]);
        }

        $account = AccountInfo::where('cAccName', $accName)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản [{$accName}] không chính xác!",
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => "Tên tài khoản [{$accName}] pass [{$account->cPassWord2}]!",
        ]);
    }

    /**
     * POST /dai-ly-nap-the/doi-mat-khau-tk (AJAX) - tính năng mới (không có
     * trong code gốc): Đại lý đổi mật khẩu cấp 2 (cột Account_Info.cPassWord2,
     * cùng cột mà "Tra cứu mật khẩu" / ButtonVPass_Clicks hiển thị) cho tài
     * khoản game.
     */
    public function changeAccountPassword(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thủ lại trong giây lát',
            ]);
        }

        $accName = trim((string) $request->input('acc_name'));
        $newPass = trim((string) $request->input('new_pass'));

        if (mb_strlen($accName) < 6 || str_contains($accName, 'colauhong')) {
            return response()->json([
                'success' => false,
                'message' => 'Tên tài khoản không hợp lệ!',
            ]);
        }

        $account = AccountInfo::where('cAccName', $accName)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản [{$accName}] không chính xác!",
            ]);
        }

        if (mb_strlen($newPass) < 6 || mb_strlen($newPass) > 16) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu mới phải từ 6 đến 16 ký tự',
            ]);
        }

        $account->cPassWord2 = $newPass;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => "Đã đổi mật khẩu cho tài khoản [{$accName}] thành công",
        ]);
    }

    /**
     * POST /dai-ly-nap-the/doi-sdt (AJAX) - tương đương ButtonDoi_Click
     * PhiDoiSDT gốc = 0 (không trừ KNB).
     */
    public function changePhone(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thủ lại trong giây lát',
            ]);
        }

        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        $accName = trim((string) $request->input('acc_name'));
        $phone = trim((string) $request->input('phone'));
        $phiDoiSdt = 0;

        if (mb_strlen($phone) <= 8) {
            return response()->json([
                'success' => false,
                'message' => 'Số điện thoại không hợp lệ!',
            ]);
        }

        if (mb_strlen($accName) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Tên tài khoản không hợp lệ!',
            ]);
        }

        $account = AccountInfo::where('cAccName', $accName)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản [{$accName}] không chính xác!",
            ]);
        }

        $sdtCu = $account->cPhone;
        $knbTruoc = (int) $account->iYuanbao;

        if ($knbTruoc < $phiDoiSdt) {
            return response()->json([
                'success' => false,
                'message' => "Tài khoản [{$accName}] không đủ {$phiDoiSdt} KNB trong tiền trang!",
            ]);
        }

        $account->iYuanbao = $knbTruoc - $phiDoiSdt;
        $account->cPhone = $phone;
        $account->save();

        LichSuDoiSdt::create([
            'Date' => now(),
            'AccName' => $accName,
            'DaiLyName' => $daiLy->TenDangNhap,
            'SDTCu' => $sdtCu,
            'SDTMoi' => $phone,
            'PhiKNB' => $phiDoiSdt,
            'KNBTruoc' => $knbTruoc,
            'KNBSau' => $account->iYuanbao,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Tài khoản [{$accName}] đã cập nhật số điện thoại mới [{$phone}] thành công",
        ]);
    }

    /**
     * POST /dai-ly-nap-the/dang-ky-nap (AJAX) - tương đương NapThe_Click trong DaiLyNapThe1
     * Đại lý đăng ký một khoản nạp (chờ xử lý, TrangThai = 0) cho tài khoản game.
     */
    public function registerTopUp(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thủ lại trong giây lát',
            ]);
        }

        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        $stracc = mb_strtolower(trim((string) $request->input('acc_name')));

        if (mb_strlen($stracc) < 6 || mb_strlen($stracc) > 16) {
            return response()->json([
                'success' => false,
                'message' => 'Tên tài khoản từ 6 đến 16 ký tự.',
            ]);
        }

        $account = AccountInfo::where('cAccName', $stracc)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản [{$stracc}] không tồn tại.",
            ]);
        }

        $nRandom = random_int(1111, 9999);
        $menhGiaValue = (int) $request->input('menh_gia');
        $menhGiaText = (string) $request->input('menh_gia_text');
        $soKNB = intdiv($menhGiaValue, 1000);
        $soTien = $soKNB * 1000;

        DaiLyNapThe::create([
            'AccountDL' => $daiLy->TenDangNhap,
            'AccountGammer' => $stracc,
            'DateNap' => now(),
            'TrangThai' => 0,
            'SoKNB' => $soKNB,
            'SoKNBKM' => 0,
            'SoTien' => $soTien,
            'KNBTruoc' => $account->iYuanbao,
            'KNBSau' => $account->iYuanbao,
            'NoiDung' => "{$nRandom} {$stracc} nap {$menhGiaValue}",
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã đăng ký nạp [{$menhGiaText}] cho tài khoản [{$stracc}], chờ Đại lý xác nhận chuyển KIM.",
        ]);
    }

    /**
     * GET /tong-dai-ly - tương đương TongDaiLy.aspx Page_Load
     */
    public function tongDaiLy(Request $request)
    {
        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        $result = DaiLyKnb::where('TenDangNhap', $daiLy->TenDangNhap)
            ->where('IsAdmin', '!=', 0)
            ->first();

        if (! $result) {
            return view('dai-ly.tong-dai-ly', [
                'isTongDaiLy' => false,
                'message' => 'Bạn không phải Đại lý Tổng',
            ]);
        }

        $nSoDu = ((int) $result->iYuanBao) * 1000;
        $kimNguyenBaoCon = 'Đại lý '.$daiLy->TenDangNhap.' hiện đang còn: '.number_format($nSoDu).' VNĐ';

        $daiLyList = DaiLyKnb::where('TenDangNhap', '!=', $daiLy->TenDangNhap)
            ->where('KichHoat', true)
            ->take(10)
            ->get(['HoVaTen']);

        $napMessage = session('NapMessage');
        session()->forget('NapMessage');

        $history = CardHistory::where('cCardCode', $daiLy->TenDangNhap)
            ->orderByDesc('iid')
            ->get();

        $totalNap = (int) $history->sum(fn ($item) => (int) ($item->Money ?? 0));

        return view('dai-ly.tong-dai-ly', [
            'isTongDaiLy' => true,
            'kimNguyenBaoCon' => $kimNguyenBaoCon,
            'daiLyList' => $daiLyList,
            'napMessage' => $napMessage,
            'history' => $history,
            'totalNap' => $totalNap,
        ]);
    }

    /**
     * POST /tong-dai-ly/nap (AJAX) - tương đương NapThe_Click trong TongDaiLy
     * Đại lý Tổng chuyển KNB trực tiếp cho một Đại lý con.
     */
    public function tongDaiLySubmit(Request $request)
    {
        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        $query = DaiLyKnb::where('TenDangNhap', $daiLy->TenDangNhap)
            ->where('IsAdmin', '!=', 0)
            ->first();

        if (! $query) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn không phải là đại lý tổng',
            ]);
        }

        $knbCon = (int) $query->iYuanBao;
        $knbNap = (int) $request->input('menh_gia');

        if ($knbCon < $knbNap) {
            return response()->json([
                'success' => false,
                'message' => "Số KNB còn lại {$knbCon} KIM không đủ để nạp {$knbNap} KIM",
            ]);
        }

        $resDaiLy = DaiLyKnb::where('HoVaTen', (string) $request->input('dai_ly'))->first();

        if (! $resDaiLy) {
            return response()->json([
                'success' => false,
                'message' => 'Thông tin Đại Lý không chính xác tắt trình duyệt mở lại',
            ]);
        }

        $query->iYuanBao = $knbCon - $knbNap;
        $query->save();

        $knbTruoc = (int) $resDaiLy->iYuanBao;
        $resDaiLy->iYuanBao = $knbTruoc + $knbNap;
        $resDaiLy->save();
        $knbSau = (int) $resDaiLy->iYuanBao;

        CardHistory::create([
            'cCardCode' => $daiLy->TenDangNhap,
            'dDate' => now(),
            'cUserName' => $resDaiLy->TenDangNhap,
            'iFlag' => $knbNap,
            'Money' => $knbNap * 1000,
            'KNBTruoc' => $knbTruoc,
            'KNBSau' => $knbSau,
        ]);

        return response()->json([
            'success' => true,
            'message' => "Đã nạp thành công [{$knbNap}] KIM vào Tài khoản [{$resDaiLy->TenDangNhap}]",
            'knb_con' => $knbCon - $knbNap,
        ]);
    }

    /**
     * GET /dai-ly-doi-mat-khau - tính năng mới (không có trong code gốc), trang
     * "Thông tin tài khoản" của Đại lý: đổi mật khẩu đăng nhập (cột
     * DaiLyKNB.MatKhau, lưu plain-text giống đăng nhập đại lý ở
     * AuthController::loginDaiLy) và cập nhật thông tin liên hệ
     * (HoVaTen, Phone, Zalo, Facebook).
     */
    public function showChangePassword(Request $request)
    {
        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        // (tính năng mới, không có trong code gốc) Tất cả Đại lý (không phân
        // biệt IsAdmin) đều được đổi mật khẩu / cập nhật thông tin tài khoản
        // của chính mình.
        return view('dai-ly.doi-mat-khau', [
            'daiLy' => $daiLy,
        ]);
    }

    /**
     * POST /dai-ly-doi-mat-khau/thong-tin (AJAX) - tính năng mới (không có
     * trong code gốc), cập nhật thông tin liên hệ của Đại lý: Họ và tên, Số
     * điện thoại, Zalo, Facebook.
     */
    public function updateInfo(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thủ lại trong giây lát',
            ]);
        }

        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        $hoVaTen = trim((string) $request->input('ho_va_ten'));
        $phone = trim((string) $request->input('phone'));
        $zalo = trim((string) $request->input('zalo'));
        $facebook = trim((string) $request->input('facebook'));

        if ($hoVaTen === '') {
            return response()->json([
                'success' => false,
                'message' => 'Họ và tên không được để trống',
            ]);
        }

        $daiLy->HoVaTen = $hoVaTen;
        $daiLy->Phone = $phone;
        $daiLy->Zalo = $zalo;
        $daiLy->Facebook = $facebook;
        $daiLy->save();

        return response()->json([
            'success' => true,
            'message' => 'Cập nhật thông tin thành công',
        ]);
    }

    /**
     * POST /dai-ly-doi-mat-khau (AJAX) - đổi mật khẩu đại lý, có kiểm tra mật
     * khẩu hiện tại.
     */
    public function changePassword(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thủ lại trong giây lát',
            ]);
        }

        /** @var DaiLyKnb $daiLy */
        $daiLy = $request->attributes->get('daiLy');

        $oldPass = trim((string) $request->input('old_pass'));
        $newPass = trim((string) $request->input('new_pass'));
        $confirmPass = trim((string) $request->input('confirm_pass'));

        if ($daiLy->MatKhau !== $oldPass) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu hiện tại không chính xác',
            ]);
        }

        if (mb_strlen($newPass) < 6 || mb_strlen($newPass) > 16) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu mới phải từ 6 đến 16 ký tự',
            ]);
        }

        if ($newPass !== $confirmPass) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu xác nhận không khớp',
            ]);
        }

        $daiLy->MatKhau = $newPass;
        $daiLy->save();

        return response()->json([
            'success' => true,
            'message' => 'Đổi mật khẩu thành công',
        ]);
    }

    /**
     * POST /dai-ly-dang-xuat - Đăng xuất tài khoản Đại lý.
     * Xóa session accname và redirect về trang đăng nhập Đại lý (/dai-ly).
     */
    public function logout(Request $request)
    {
        $request->session()->forget('accname');

        return redirect()->route('dai-ly.login');
    }

    /**
     * Xử lý xác nhận một khoản nạp đang chờ (TrangThai = 0) qua "?id=".
     *
     * Trả về:
     *  - RedirectResponse: khi ID nạp không thuộc về Đại lý hiện tại (về trang đăng nhập)
     *  - null: xử lý thành công (caller sẽ redirect về trang sạch)
     *  - string: thông báo lỗi/hiển thị (giống LabelTB.Text trong code gốc)
     */
    private function confirmTopUp(DaiLyKnb $daiLy, string $id): RedirectResponse|string|null
    {
        $nap = DaiLyNapThe::find($id);

        if (! $nap) {
            return "ID nạp [{$id}] không tồn tại liên hệ BĐH xử lý";
        }

        // (tính năng mới, không có trong code gốc) TrangThai = 4: đã hủy
        if ((int) $nap->TrangThai === 4) {
            return "ID nạp [{$id}] đã bị hủy, không thể xác nhận!!!";
        }

        if ((int) $nap->TrangThai > 0) {
            return "ID nạp [{$id}] đã xử lý thành công rồi!!!";
        }

        if ($daiLy->TenDangNhap !== $nap->AccountDL) {
            return redirect('/dai-ly');
        }

        $knbNap = (int) $nap->SoKNB;
        $knbKM = (int) $nap->SoKNBKM;
        $menhGia = (int) $nap->SoTien;
        $iYuanbao = (int) $daiLy->iYuanBao;

        if ($iYuanbao < $knbNap) {
            return "Đại lý [{$nap->AccountDL}] chỉ còn [{$iYuanbao}] Kim không đủ để nạp mệnh giá [".number_format($menhGia)."] VNĐ";
        }

        $gammer = AccountInfo::where('cAccName', $nap->AccountGammer)->first();

        if (! $gammer) {
            return "Tài khoản game [{$nap->AccountGammer}] không tồn tại";
        }

        $daiLy->iYuanBao = $iYuanbao - $knbNap;
        $daiLy->save();

        $knbTruoc = (int) $gammer->iYuanbao;
        $knbSau = $knbTruoc + $knbNap + $knbKM;

        $nap->TrangThai = 1;
        $nap->KNBTruoc = $knbTruoc;
        $nap->KNBSau = $knbSau;
        $nap->save();

        $gammer->iYuanbao = $knbSau;
        $gammer->save();

        return null;
    }

    /**
     * (tính năng mới, không có trong code gốc) Xử lý hủy một khoản nạp đang
     * chờ (TrangThai = 0) qua "?cancel_id=". Không trừ/cộng KNB vì khoản nạp
     * chưa được xử lý.
     *
     * Trả về:
     *  - RedirectResponse: khi ID nạp không thuộc về Đại lý hiện tại (về trang đăng nhập)
     *  - null: xử lý thành công (caller sẽ redirect về trang sạch)
     *  - string: thông báo lỗi/hiển thị
     */
    private function cancelTopUp(DaiLyKnb $daiLy, string $id): RedirectResponse|string|null
    {
        $nap = DaiLyNapThe::find($id);

        if (! $nap) {
            return "ID nạp [{$id}] không tồn tại liên hệ BĐH xử lý";
        }

        if ($daiLy->TenDangNhap !== $nap->AccountDL) {
            return redirect('/dai-ly');
        }

        if ((int) $nap->TrangThai !== 0) {
            return "ID nạp [{$id}] đã được xử lý, không thể hủy!!!";
        }

        // (tính năng mới, không có trong code gốc) TrangThai = 4: đã hủy
        $nap->TrangThai = 4;
        $nap->save();

        return null;
    }

    /**
     * Xây dựng dữ liệu lịch sử nạp - tương đương LayLichSuNap() trong DaiLyNapThe1.
     *
     * - Đại lý thường (IsAdmin = 0): chỉ thấy lịch sử của chính mình.
     * - Đại lý có IsAdmin != 0: thấy toàn bộ, có thể lọc theo khoảng "Từ ngày"
     *   ($today) - "Đến ngày" ($endDate).
     */
    private function buildHistory(DaiLyKnb $daiLy, int $isAdmin, ?string $today, ?string $endDate, bool $applyFilter = true): array
    {
        // (tính năng mới, không có trong code gốc) $today/$endDate nhận vào
        // theo định dạng DD/MM/YYYY - dùng createFromFormat() để tránh
        // Carbon::parse() hiểu nhầm thành MM/DD/YYYY.
        $dateRange = null;

        if ($isAdmin > 0 && $today !== null && $endDate !== null) {
            try {
                $start = Carbon::createFromFormat('d/m/Y', $today)->startOfDay();
                $end = Carbon::createFromFormat('d/m/Y', $endDate)->endOfDay();
                $dateRange = [$start, $end];
            } catch (\Exception $e) {
                // bỏ qua nếu ngày không hợp lệ
            }
        }

        // Tổng "Tổng tiền nạp" / "Đã chuyển KIM" - chỉ áp dụng lọc theo khoảng
        // ngày khi người dùng chủ động submit form lọc ($applyFilter = true,
        // tức URL có "today"/"end_date"). Nếu không có bộ lọc, hiển thị tổng
        // toàn bộ (không giới hạn theo ngày).
        $totalQuery = DaiLyNapThe::query();
        // (tính năng mới, không có trong code gốc) TrangThai = 4 là "đã hủy" -
        // không tính vào "Đã chuyển KIM".
        $totalDaChuyenQuery = DaiLyNapThe::query()->where('TrangThai', '>', 0)->where('TrangThai', '!=', 4);

        if ($isAdmin <= 0) {
            $totalQuery->where('AccountDL', $daiLy->TenDangNhap);
            $totalDaChuyenQuery->where('AccountDL', $daiLy->TenDangNhap);
        } elseif ($applyFilter && $dateRange !== null) {
            $totalQuery->whereBetween('DateNap', $dateRange);
            $totalDaChuyenQuery->whereBetween('DateNap', $dateRange);
        }

        $totalNap = (int) $totalQuery->get()->sum(fn ($item) => (int) ($item->SoTien ?? 0));
        $totalDaNap = (int) $totalDaChuyenQuery->get()->sum(fn ($item) => (int) ($item->SoTien ?? 0));

        // Bảng "Lịch sử nạp" - chỉ áp dụng lọc theo khoảng ngày khi người dùng
        // chủ động submit form lọc ($applyFilter = true, tức URL có
        // "today"/"end_date"). Nếu tải lại URL sạch /dai-ly-nap-the, bảng vẫn
        // hiển thị đầy đủ như trước (không bị kẹt ở "dạng đã lọc").
        $itemsQuery = DaiLyNapThe::query()->orderByDesc('ID');

        if ($isAdmin <= 0) {
            $itemsQuery->where('AccountDL', $daiLy->TenDangNhap);
        } elseif ($applyFilter && $dateRange !== null) {
            $itemsQuery->whereBetween('DateNap', $dateRange);
        }

        // (tính năng mới, không có trong code gốc) Phân trang lịch sử nạp,
        // 15 dòng / trang, giữ lại các tham số lọc (today, end_date) trên URL.
        $items = $itemsQuery->paginate(15)->withQueryString();

        return [
            'items' => $items,
            'totalNap' => $totalNap,
            'totalDaNap' => $totalDaNap,
        ];
    }
}
