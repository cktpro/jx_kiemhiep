<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Symfony\Component\HttpFoundation\Response;

/**
 * Yêu cầu session "accname" tồn tại trước khi truy cập trang tài khoản
 * - tương đương đoạn lặp lại nhiều lần trong code gốc:
 *
 *     if (Session["accname"] == null)
 *     {
 *         Response.Redirect("/dang-nhap");
 *         return;
 *     }
 */
class EnsureAccountLoggedIn
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Session::has('accname') || Session::get('accname') === '-1') {
            return redirect('/dang-nhap');
        }

        return $next($request);
    }
}
