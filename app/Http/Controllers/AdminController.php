<?php

namespace App\Http\Controllers;

/**
 * @deprecated Đã tách thành các controller riêng trong namespace Admin\:
 *
 *   Admin\AuthController         — đăng nhập / đăng xuất
 *   Admin\HomeController         — trang chủ + quản lý nạp thẻ
 *   Admin\NewsController         — quản lý tin tức
 *   Admin\SlidesController       — quản lý slide trang chủ
 *   Admin\SettingsController     — SEO / cài đặt chung / footer / giao diện đại lý
 *   Admin\DaiLyAccountController — quản lý tài khoản Đại lý
 *
 * Routes đã được cập nhật trong routes/web.php.
 */
class AdminController extends Controller {}
