<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            // Yêu cầu đã đăng nhập tài khoản game (session "accname")
            // - tương đương các kiểm tra "if (Session["accname"] == null) Response.Redirect("/dang-nhap")"
            'account.auth' => \App\Http\Middleware\EnsureAccountLoggedIn::class,

            // Yêu cầu đăng nhập bằng tài khoản Đại lý (cookie "auth" + bảng DaiLyKNB)
            // - tương đương "if (!ClassHeader1.CheckAuth()) Response.Redirect("/dai-ly.1.aspx")"
            'daily.auth' => \App\Http\Middleware\EnsureDaiLyLoggedIn::class,

            // Yêu cầu đăng nhập trang quản trị Admin6 (session "admin_user")
            // - tương đương AdminSite1.Master.cs: "if (Session["UserName"] == null) Response.Redirect("/Admin6/Default.aspx")"
            'admin.auth' => \App\Http\Middleware\EnsureAdminLoggedIn::class,

            // Phân quyền Admin theo UserManager.iRole (tính năng mới, không có
            // trong code gốc) - iRole khác 1 chỉ được Trang chủ/Quản lý tin/Quản lý slide.
            'admin.role' => \App\Http\Middleware\EnsureAdminRole::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
