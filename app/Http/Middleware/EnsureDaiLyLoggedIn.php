<?php

namespace App\Http\Middleware;

use App\Models\DaiLyKnb;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yêu cầu đăng nhập bằng tài khoản Đại lý (DaiLyKNB) - dùng cho các trang
 * /dai-ly-nap-the và /tong-dai-ly.
 *
 * Port từ kiểm tra trong các action (ví dụ ButtonTim_Click) của
 * DaiLyNapThe.aspx.cs / TongDaiLy.aspx.cs:
 *
 *     if (Session["accname"] == null) { Response.Redirect("/dai-ly.1.aspx"); return; }
 *     var queryDL = ... where TenDangNhap == Session["accname"].ToString() ...
 *     if (queryDL.Count() != 1) { Response.Redirect("/dai-ly.1.aspx"); }
 *
 * Lưu ý: đăng nhập đại lý (AuthController::loginDaiLy) chỉ set
 * Session["accname"], không set cookie "auth" - nên ở đây phải kiểm tra
 * session, không dùng check_auth()/get_user_auth() (vốn đọc cookie "auth"
 * do đăng nhập tài khoản game set qua set_auth_cookie()).
 *
 * "dai-ly.1.aspx" (route "daily" -> DangNhap.aspx) tương ứng /dai-ly (login).
 */
class EnsureDaiLyLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        $accName = Session::get('accname');

        if (! $accName) {
            return redirect('/dai-ly');
        }

        $daiLy = DaiLyKnb::where('TenDangNhap', $accName)->first();

        if (! $daiLy) {
            return redirect('/dai-ly');
        }

        $request->attributes->set('daiLy', $daiLy);

        return $next($request);
    }
}
