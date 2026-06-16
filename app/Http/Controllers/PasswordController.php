<?php

namespace App\Http\Controllers;

use App\Models\AccountInfo;
use Illuminate\Http\Request;

/**
 * Quên mật khẩu - port từ QuenMatKhauV2.aspx + QuenMatKhauV2.aspx.cs (route "quenmk2")
 *
 * Lưu ý: chức năng gốc KHÔNG đổi mật khẩu trực tiếp - chỉ hiển thị hướng dẫn
 * soạn tin nhắn SMS theo cú pháp "VOLAM1PK <tài khoản> NEWPASS <mật khẩu mới>"
 * gửi tới đầu số tổng đài (xử lý đổi mật khẩu nằm ở phía tổng đài SMS, ngoài site).
 */
class PasswordController extends Controller
{
    /**
     * GET /quen-mat-khau
     */
    public function show()
    {
        return view('auth.forgot-password', [
            'phoneOtp' => site_setting('phone_otp'),
        ]);
    }

    /**
     * POST /quen-mat-khau (AJAX) - tương đương ButtonGetCode_Click
     */
    public function sendCode(Request $request)
    {
        $accName = trim((string) $request->input('acc_name'));
        $newPass = trim((string) $request->input('new_pass'));
        $oldPhone = trim((string) $request->input('old_phone'));

        if (mb_strlen($accName) > 16 || mb_strlen($accName) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Tên tài khoản từ 6 đến 16 ký tự!',
            ]);
        }

        if (mb_strlen($newPass) > 16 || mb_strlen($newPass) < 6) {
            return response()->json([
                'success' => false,
                'message' => 'Mật khẩu từ 6 đến 16 ký tự!',
            ]);
        }

        $account = AccountInfo::where('cAccName', $accName)->first();

        if (! $account) {
            return response()->json([
                'success' => false,
                'message' => "Tài khoản [{$accName}] không tồn tại!",
            ]);
        }

        if ($oldPhone !== $account->cPhone) {
            return response()->json([
                'success' => false,
                'message' => "Tài khoản [{$accName}] không đúng số điện thoại!",
            ]);
        }

        $phoneOtp = site_setting('phone_otp');
        $expireAt = now()->addHour()->format('H:i:s d/m/Y');

        $html = "<span class='message-success'>Thành công: Vui lòng sử dụng số điện thoại <b>{$account->cPhone}</b> nhắn tin với cấu trúc"
            ."<br/>"
            ."<b>VOLAM1PK {$accName} NEWPASS {$newPass}</b> gửi đến số <b>{$phoneOtp}</b> để hoàn tất đổi mật khẩu."
            ."<br/>"
            ."Cước tin nhắn: 1000 đồng / 1 tin. Thời gian hiệu lực đến {$expireAt}"
            ."</span>";

        return response()->json([
            'success' => true,
            'message' => '',
            'html' => $html,
        ]);
    }
}
