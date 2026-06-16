<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yêu cầu đăng nhập Admin6 (session "admin_user")
 * - tương đương AdminSite1.Master.cs Page_Load:
 *
 *     if (Session["UserName"] == null)
 *     {
 *         Response.Redirect("/Admin6/Default.aspx");
 *         return;
 *     }
 */
class EnsureAdminLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Session::has('admin_user')) {
            return redirect()->route('admin.login');
        }

        return $next($request);
    }
}
