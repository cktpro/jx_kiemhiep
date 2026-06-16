<?php

namespace App\Http\Controllers;

/**
 * Controller cơ sở - phần của bộ khung Laravel 11 (app/Http/Controllers/Controller.php)
 * bị thiếu khi project được khởi tạo thủ công (không qua `laravel new` / composer create-project).
 * Tất cả controller khác (AuthController, AccountController, NapTheController,
 * DaiLyController, PasswordController, NewsController, HomeController, ...) đều
 * extends class này.
 */
abstract class Controller
{
    //
}
