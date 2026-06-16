<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResolveDownloadLink;
use App\Models\AccountHabitus;
use App\Models\AccountInfo;
use App\Models\DaiLyKnb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

/**
 * Đăng nhập / Đăng ký / Đăng xuất - port từ:
 *  - DangNhapV2.aspx + DangNhapV2.aspx.cs  (đăng nhập tài khoản game + đăng nhập đại lý)
 *  - DangKyV2.aspx + DangKyV2.aspx.cs      (đăng ký tài khoản)
 *
 * Toàn bộ xử lý submit được chuyển sang AJAX (JSON), thay cho cơ chế
 * postback (OnClick="..._Click") của ASP.NET WebForms.
 */
class AuthController extends Controller
{
    use ResolveDownloadLink;
    /**
     * GET /dang-nhap - tương đương DangNhapV2.aspx (route "dangnhap2")
     */
    public function showLogin(Request $request)
    {
        return view('auth.login', [
            'isDaiLy' => false,
            'linkDownload' => $this->resolveDownloadLink($request),
        ]);
    }

    /**
     * GET /dai-ly - tương đương DangNhapV2.aspx khi RouteData["Id"] != null (route "daily")
     * Trang đăng nhập riêng cho đại lý nạp thẻ (DaiLyKNB).
     */
    public function showDaiLyLogin(Request $request)
    {
        return view('auth.login', [
            'isDaiLy' => true,
            'linkDownload' => $this->resolveDownloadLink($request),
        ]);
    }

    /**
     * GET /dang-ky - tương đương DangKyV2.aspx (route "dangky2")
     */
    public function showRegister(Request $request)
    {
        return view('auth.register', [
            'linkDownload' => $this->resolveDownloadLink($request),
        ]);
    }

    /**
     * POST /dang-nhap (AJAX) - tương đương ButtonLogin_Click khi RouteData["Id"] == null
     * Đăng nhập tài khoản game (bảng Account_Info).
     */
    public function login(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đợi vài giây trước khi thử lại!',
            ]);
        }

        $username = strtolower(str_replace(' ', '', trim((string) $request->input('username'))));
        $username = loc_dau($username);
        $password = trim((string) $request->input('password'));

        $maxLen = (int) site_setting('max_acc_len', 16);
        $minLen = (int) site_setting('min_acc_len', 6);

        if (mb_strlen($username) > $maxLen || mb_strlen($username) < $minLen) {
            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản độ dài tối đa {$maxLen} ký tự, vui lòng nhập lại!",
            ]);
        }

        if (mb_strlen($password) > $maxLen || mb_strlen($password) < $minLen) {
            return response()->json([
                'success' => false,
                'message' => "Mật khẩu độ dài tối đa {$maxLen} ký tự, vui lòng nhập lại!",
            ]);
        }

        $hashed = calculate_md5_hash($password);

        $account = AccountInfo::where('cAccName', $username)
            ->where('cPassWord', $hashed)
            ->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tài khoản [{$username}] không hợp lệ vui lòng nhập lại!",
            ]);
        }

        Session::put('accname', $username);
        set_auth_cookie($username);

        return response()->json([
            'success' => true,
            'message' => "Đăng nhập thành công, đang chuyển hướng...",
            'redirect' => '/tai-khoan',
        ]);
    }

    /**
     * POST /dai-ly (AJAX) - tương đương ButtonLogin_Click khi RouteData["Id"] != null
     * Đăng nhập đại lý nạp thẻ (bảng DaiLyKNB, mật khẩu lưu plain-text như code gốc).
     */
    public function loginDaiLy(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đợi vài giây trước khi thử lại!',
            ]);
        }

        $username = strtolower(str_replace(' ', '', trim((string) $request->input('username'))));
        $password = trim((string) $request->input('password'));

        $dl = DaiLyKnb::where('TenDangNhap', $username)
            ->where('MatKhau', $password)
            ->first();

        if (! $dl) {
            return response()->json([
                'success' => false,
                'message' => "Tài khoản đại lý [{$username}] không hợp lệ vui lòng nhập lại!",
            ]);
        }

        Session::put('accname', $username);

        return response()->json([
            'success' => true,
            'message' => 'Đăng nhập đại lý thành công, đang chuyển hướng...',
            'redirect' => '/dai-ly-nap-the',
        ]);
    }

    /**
     * POST /dang-ky (AJAX) - tương đương ButtonReg_Click
     * Tạo Account_Info + Account_Habitus mới (giữ nguyên các giá trị mặc định gốc).
     */
    public function register(Request $request)
    {
        if (! check_delay()) {
            return response()->json([
                'success' => false,
                'message' => 'Vui lòng đợi vài giây trước khi thử lại!',
            ]);
        }

        $phone = trim((string) $request->input('phone'));
        $password = strtolower(str_replace(' ', '', trim((string) $request->input('password'))));
        $username = strtolower(str_replace(' ', '', trim((string) $request->input('username'))));
        $username = loc_dau($username);

        $maxLen = (int) site_setting('max_acc_len', 16);
        $minLen = (int) site_setting('min_acc_len', 6);

        if (mb_strlen($phone) < 10) {
            Session::put('accname', '-1');

            return response()->json([
                'success' => false,
                'message' => 'Số điện thoại không hợp lệ',
            ]);
        }

        if (mb_strlen($username) > $maxLen || mb_strlen($username) < $minLen) {
            Session::put('accname', '-1');

            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản phải có độ dài {$minLen} đến {$maxLen} ký tự",
            ]);
        }

        if (mb_strlen($password) > $maxLen || mb_strlen($password) < $minLen) {
            Session::put('accname', '-1');

            return response()->json([
                'success' => false,
                'message' => "Mật khẩu phải có độ dài {$minLen} đến {$maxLen} ký tự",
            ]);
        }

        $existing = AccountInfo::where('cAccName', $username)->first();

        if ($existing) {
            Session::put('accname', '-1');

            return response()->json([
                'success' => false,
                'message' => "Tên tài khoản [{$username}] đã tồn tại",
            ]);
        }

        $hashed = calculate_md5_hash($password);
        $now = now();

        AccountInfo::create([
            'cAccName' => $username,
            'cPassWord' => $hashed,
            'cSecPassword' => $hashed,
            'cRealName' => $username,
            'dBirthDay' => $now,
            'cPassWord2' => $password,
            'cArea' => '0',
            'dRegDate' => $now,
            'cPhone' => $phone,
            'iClientID' => 0,
            'dLoginDate' => $now->toDateTimeString(),
            'dLogoutDate' => $now->toDateTimeString(),
            'iTimeCount' => 0,
            'cEMail' => $username,
            'iMoney' => 0,
            'iYuanbao' => 0,
            'cIDNum' => '0',
            'cQuestion' => 'Unlicensed',
            'cAnswer' => 'Unlicensed',
            'cSex' => 'Sex',
            'cDegree' => 'cDegree',
            'StatusVerify' => 'Unlicensed',
        ]);

        AccountHabitus::create([
            'cAccName' => $username,
            'iFlag' => 0,
            'iLeftSecond' => 0,
            'nExtPoint' => 0,
            'dBeginDate' => $now,
            'iLeftMonth' => 0,
            'dEndDate' => $now->copy()->addYears(10),
            'iClientID' => 0,
            'isUse' => 0,
            'iAddDay' => 0,
            'iAddHour' => 0,
            'iMoney' => 0,
            'nExtPoint1' => 0,
            'nExtPoint2' => 0,
            'nExtPoint3' => 0,
            'nExtPoint4' => 0,
            'nExtPoint5' => 0,
            'nExtPoint6' => 0,
            'nExtPoint7' => 0,
            'nExtPoint8' => 0,
            'nExtPoint9' => 0,
        ]);

        Session::put('accname', $username);

        return response()->json([
            'success' => true,
            'message' => "Tạo tài khoản [{$username}] thành công, bạn có thể đăng nhập vào game!",
        ]);
    }

    /**
     * Đăng xuất: xoá session + cookie "auth"
     */
    public function logout()
    {
        Session::forget('accname');
        clear_auth_cookie();

        return redirect('/dang-nhap');
    }

}
