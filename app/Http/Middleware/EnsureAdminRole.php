<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

/**
 * (tính năng mới, không có trong code gốc) Phân quyền khu vực Admin theo
 * UserManager.iRole:
 * - iRole == 1: admin toàn quyền, truy cập tất cả chức năng.
 * - iRole khác 1: chỉ được phép truy cập Trang chủ, Quản lý tin và
 *   Quản lý slide (các route khác sẽ bị chặn và redirect về Trang chủ).
 *
 * Middleware này chạy sau "admin.auth" (EnsureAdminLoggedIn), nên session
 * "admin_user"/"admin_role" chắc chắn đã có nếu đăng nhập qua login().
 */
class EnsureAdminRole
{
    /**
     * Danh sách (pattern) tên route được phép truy cập khi iRole khác 1.
     */
    protected const ALLOWED_ROUTE_PATTERNS = [
        'admin.home',
        'admin.logout',
        'admin.news.*',
        'admin.slides.*',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $role = (int) Session::get('admin_role', 0);

        if ($role === 1) {
            return $next($request);
        }

        $routeName = $request->route()?->getName();

        if ($routeName !== null) {
            foreach (self::ALLOWED_ROUTE_PATTERNS as $pattern) {
                if (Str::is($pattern, $routeName)) {
                    return $next($request);
                }
            }
        }

        return redirect()->route('admin.home')
            ->with('admin_role_error', 'Bạn không có quyền truy cập chức năng này.');
    }
}
