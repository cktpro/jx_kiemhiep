<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResolveDownloadLink;
use App\Models\AccountInfo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Nhóm trang Tài khoản - port từ:
 *  - ThongTinTaiKhoanV2.aspx + .aspx.cs (route "taikhoan2" -> /tai-khoan)
 *  - DoiMatKhauV2.aspx + .aspx.cs       (route "doimk2"    -> /doi-mat-khau)
 *  - DoiSoDienThoaiV2.aspx + .aspx.cs   (route "doisdt2"   -> /cap-nhat-so-dien-thoai)
 *
 * Tất cả các trang đều yêu cầu session "accname" (middleware account.auth).
 */
class AccountController extends Controller
{
    use ResolveDownloadLink;
    /**
     * GET /tai-khoan - tương đương ThongTinTaiKhoanV2.aspx (Page_Load)
     */
    public function show(Request $request)
    {
        $account = $this->currentAccount();

        return view('account.thong-tin', [
            'account' => $account,
            'linkDownload' => $this->resolveDownloadLink($request),
        ]);
    }

    /**
     * POST /tai-khoan/giai-ket (AJAX) - tương đương ButtonGiaiKet_Click
     * Đặt iClientID = 0 để "giải kẹt" tài khoản đang đăng nhập game.
     */
    public function unlock()
    {
        if (! check_delay(30)) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thử lại trong giây lát',
            ]);
        }

        $stracc = Session::get('accname');
        $account = AccountInfo::where('cAccName', $stracc)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản không hợp lệ',
            ]);
        }

        $account->iClientID = 0;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => "Đã giải kẹt tài khoản [{$stracc}] vui lòng đăng nhập lại sau 1 phút nữa!",
        ]);
    }

    /**
     * POST /tai-khoan/doi-mat-khau (AJAX) - tương đương ButtonChangePass_Click trong ThongTinTaiKhoanV2
     * Đổi mật khẩu có kiểm tra mật khẩu cũ.
     */
    public function changePassword(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Bạn thao tác quá nhanh vui lòng thử lại trong giây lát',
            ]);
        }

        $newPass = str_replace(' ', '', trim((string) $request->input('new_pass')));
        $oldPass = str_replace(' ', '', trim((string) $request->input('old_pass')));

        $maxLen = (int) site_setting('max_acc_len', 16);

        if (mb_strlen($newPass) > $maxLen || mb_strlen($newPass) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu mới tối đa 16 ký tự tối thiểu 6 ký tự',
            ]);
        }

        $stracc = Session::get('accname');
        $account = AccountInfo::where('cAccName', $stracc)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản hoặc mật khẩu không hợp lệ kiểm tra lại',
            ]);
        }

        if ($account->cPassWord !== calculate_md5_hash($oldPass)) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu cũ không chính xác',
            ]);
        }

        $hashed = calculate_md5_hash($newPass);
        $account->cPassWord = $hashed;
        $account->cSecPassword = $hashed;
        $account->cPassWord2 = $newPass;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật mật khẩu mới thành [{$newPass}]",
        ]);
    }

    /**
     * GET /doi-mat-khau - tương đương DoiMatKhauV2.aspx (Page_Load)
     */
    public function showChangePasswordSimple(Request $request)
    {
        $account = $this->currentAccount();

        return view('account.doi-mat-khau', [
            'account' => $account,
            'linkDownload' => $this->resolveDownloadLink($request),
        ]);
    }

    /**
     * POST /doi-mat-khau (AJAX) - tương đương ButtonChangePass_Click trong DoiMatKhauV2
     * Đổi mật khẩu KHÔNG kiểm tra mật khẩu cũ (giữ đúng hành vi gốc).
     */
    public function changePasswordSimple(Request $request)
    {
        $newPass = trim((string) $request->input('new_pass'));
        $maxLen = (int) site_setting('max_acc_len', 16);

        if (mb_strlen($newPass) > $maxLen || mb_strlen($newPass) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu mới tối đa 16 ký tự tối thiểu 6 ký tự',
            ]);
        }

        $stracc = Session::get('accname');
        $account = AccountInfo::where('cAccName', $stracc)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản hoặc mật khẩu không hợp lệ kiểm tra lại',
            ]);
        }

        $hashed = calculate_md5_hash($newPass);
        $account->cPassWord = $hashed;
        $account->cSecPassword = $hashed;
        $account->cPassWord2 = $newPass;
        $account->save();

        return response()->json([
            'success' => true,
            'message' => "Đã cập nhật mật khẩu mới thành [{$newPass}]",
        ]);
    }

    /**
     * GET /cap-nhat-so-dien-thoai - tương đương DoiSoDienThoaiV2.aspx (Page_Load)
     */
    public function showChangePhone(Request $request)
    {
        $account = $this->currentAccount();

        return view('account.doi-so-dien-thoai', [
            'account' => $account,
            'phoneOtp' => site_setting('phone_otp'),
            'linkDownload' => $this->resolveDownloadLink($request),
        ]);
    }

    /**
     * POST /cap-nhat-so-dien-thoai (AJAX) - tương đương ButtonChangePhone_Click
     *
     * Lưu ý: giống QuenMatKhau, chức năng gốc chỉ hiển thị hướng dẫn soạn SMS
     * "TINHNGHIA <tài khoản> PHONE <SĐT mới>" - không cập nhật DB trực tiếp ở đây.
     */
    public function changePhone(Request $request)
    {
        $newPhone = trim((string) $request->input('new_phone'));

        if (mb_strlen($newPhone) > 12 || mb_strlen($newPhone) < 10) {
            return response()->json([
                'success' => false,
                'message' => 'Số điện thoại mới không đúng định dạng',
            ]);
        }

        $stracc = Session::get('accname');
        $account = AccountInfo::where('cAccName', $stracc)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => 'Tài khoản hoặc mật khẩu không hợp lệ kiểm tra lại',
            ]);
        }

        $oldPhone = $account->cPhone;
        $phoneOtp = site_setting('phone_otp');
        $expireAt = now()->addHour()->format('H:i:s d/m/Y');

        $html = '<br />'
            .'<b>Hướng dẫn bước tiếp theo :</b>'
            .'<br/>'
            ."Sử dụng số điện thoại cũ <b>({$oldPhone})</b>soạn tin nhắn theo cấu trúc: "
            .'<br />'
            ."<b><u>TINHNGHIA {$stracc} PHONE {$newPhone}</u></b>"
            ." gửi đến số <b>{$phoneOtp}</b> để hoàn tất cập nhật SĐT sang số mới <b>{$newPhone}</b>"
            .'<br />'
            ."(Code tin nhắn có hiệu lực đến <u>{$expireAt}</u> và không phân biệt chữ hoa, chữ thường)"
            .'<br/>'
            .'(Cước tin nhắn: <b>1000 VNĐ / 1 tin nhắn</b>)';

        return response()->json([
            'success' => true,
            'html' => $html,
        ]);
    }

    /**
     * Lấy thông tin tài khoản hiện tại theo session "accname".
     * Middleware account.auth đảm bảo session đã tồn tại, nhưng vẫn fallback
     * redirect về /dang-nhap nếu tài khoản không còn tồn tại trong DB
     * (tương đương "if (query.Count() == 0) Response.Redirect("/dang-nhap")").
     */
    private function currentAccount(): AccountInfo
    {
        $stracc = Session::get('accname');
        $account = AccountInfo::where('cAccName', $stracc)->first();

        if (! $account) {
            abort(redirect('/dang-nhap'));
        }

        return $account;
    }

}
