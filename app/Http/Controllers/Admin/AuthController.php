<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserManager;
use Illuminate\Http\Request;

/**
 * Đăng nhập / Đăng xuất khu vực quản trị Admin6.
 * Tách từ AdminController (cũ).
 */
class AuthController extends Controller
{
    /**
     * GET /admin/dang-nhap
     */
    public function showLogin(Request $request)
    {
        if ($request->session()->has('admin_user')) {
            return redirect()->route('admin.home');
        }

        return view('admin.login', ['message' => null]);
    }

    /**
     * POST /admin/dang-nhap
     */
    public function login(Request $request)
    {
        $username = trim((string) $request->input('username'));
        $password = trim((string) $request->input('password'));

        $user = UserManager::query()
            ->whereRaw('UPPER(LTRIM(RTRIM(cUserName))) = UPPER(?)', [$username])
            ->whereRaw('LTRIM(RTRIM(cPassWord)) = ?', [$password])
            ->first();

        if ($user) {
            $request->session()->regenerate();
            $request->session()->put('admin_user', $user->cUserName);
            $request->session()->put('admin_role', (int) $user->iRole);

            return redirect()->route('admin.home');
        }

        return view('admin.login', [
            'message' => 'Tên đăng nhập hoặc mật khẩu không đúng!',
        ]);
    }

    /**
     * POST /admin/dang-xuat
     */
    public function logout(Request $request)
    {
        $request->session()->forget(['admin_user', 'admin_role']);

        return redirect()->route('admin.login');
    }
}
