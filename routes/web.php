<?php

use App\Http\Controllers\AccountController;
use App\Http\Controllers\Admin;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DaiLyController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\NapTheController;
use App\Http\Controllers\NewsController;
use App\Http\Controllers\PasswordController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Đang chuyển đổi dần từ Global.asax.cs (RouteCollection.MapPageRoute) của
| project ASP.NET WebForms gốc. Các route dưới đây ứng với phần đăng nhập/
| đăng ký/quên mật khẩu (task #5), tài khoản (task #6), nạp thẻ/đại lý
| (task #7), tin tức/trang chủ/tải game (task #8).
|
*/

/*
|--------------------------------------------------------------------------
| Trang chủ + Tin tức + Tải game (task #8)
|--------------------------------------------------------------------------
*/

// Không có route Global.asax cho "/" - DefaultV2.aspx được coi là trang chủ
// (theo quy ước đặt tên V2 dùng cho mọi trang đã chuyển đổi khác).
Route::get('/', [HomeController::class, 'index'])->name('home');

// Ảnh nền khung tin tức trang chủ (".news-frame") - file gốc nằm ở
// "laravel/assests/images" (ngoài thư mục public), nên phải serve qua route
// thay vì URL tĩnh.
Route::get('/assests/images/news-frame.jpg', function () {
    return response()->file(base_path('assests/images/news-frame.jpg'));
});

// TaiGame.aspx.cs -> luôn redirect sang /tin-tuc/{slug}.3 (bài hướng dẫn tải game)
Route::get('/tai-game', [HomeController::class, 'taiGame'])->name('tai-game');

// "alltin" -> tin-tuc/all.aspx -> DanhSachTin.aspx
Route::get('/tin-tuc/all', [NewsController::class, 'index'])->name('news.index');

// Redirect URL cũ /tin-tuc/{slug}.{id} -> /tin-tuc/{slug}
Route::get('/tin-tuc/{slug}.{id}', function (string $slug) {
    return redirect('/tin-tuc/' . $slug, 301);
})->whereNumber('id');

// "chitiettin" -> /tin-tuc/{slug}
Route::get('/tin-tuc/{slug}', [NewsController::class, 'show'])->name('news.show');

// "dangnhap2" -> /dang-nhap -> DangNhapV2.aspx
Route::get('/dang-nhap', [AuthController::class, 'showLogin'])->name('login');
Route::post('/dang-nhap', [AuthController::class, 'login']);

// "daily" -> /dai-ly -> DangNhapV2.aspx (RouteData["Id"] != null)
Route::get('/dai-ly', [AuthController::class, 'showDaiLyLogin'])->name('dai-ly.login');
Route::post('/dai-ly', [AuthController::class, 'loginDaiLy']);

Route::post('/dang-xuat', [AuthController::class, 'logout'])->name('logout');

// "dangky2" -> /dang-ky -> DangKyV2.aspx
Route::get('/dang-ky', [AuthController::class, 'showRegister'])->name('register');
Route::post('/dang-ky', [AuthController::class, 'register']);

// "quenmk2" -> /quen-mat-khau -> QuenMatKhauV2.aspx
Route::get('/quen-mat-khau', [PasswordController::class, 'show'])->name('forgot-password');
Route::post('/quen-mat-khau', [PasswordController::class, 'sendCode']);

/*
|--------------------------------------------------------------------------
| Nhóm trang Tài khoản (task #6) - yêu cầu đã đăng nhập (session "accname")
|--------------------------------------------------------------------------
*/
Route::middleware('account.auth')->group(function () {
    // "taikhoan2" -> /tai-khoan -> ThongTinTaiKhoanV2.aspx
    Route::get('/tai-khoan', [AccountController::class, 'show'])->name('account.show');
    Route::post('/tai-khoan/giai-ket', [AccountController::class, 'unlock']);
    Route::post('/tai-khoan/doi-mat-khau', [AccountController::class, 'changePassword']);

    // "doimk2" -> /doi-mat-khau -> DoiMatKhauV2.aspx
    Route::get('/doi-mat-khau', [AccountController::class, 'showChangePasswordSimple'])->name('account.change-password');
    Route::post('/doi-mat-khau', [AccountController::class, 'changePasswordSimple']);

    // "doisdt2" -> /cap-nhat-so-dien-thoai -> DoiSoDienThoaiV2.aspx
    Route::get('/cap-nhat-so-dien-thoai', [AccountController::class, 'showChangePhone'])->name('account.change-phone');
    Route::post('/cap-nhat-so-dien-thoai', [AccountController::class, 'changePhone']);
});

/*
|--------------------------------------------------------------------------
| Nhóm trang Nạp thẻ / Đại lý (task #7)
|--------------------------------------------------------------------------
*/

// /nap-the -> redirect về /nap-coin (đã gộp trang)
Route::get('/nap-the', fn() => redirect('/nap-coin', 301))->name('napthe.show');
Route::post('/nap-the', [NapTheController::class, 'submit']);

// "napthe" -> /nap-the.{Id}.{Sv}.aspx -> NapThe.aspx (đại lý cố định theo {id})
Route::get('/nap-the/{id}/{sv}', [NapTheController::class, 'show'])
    ->whereNumber(['id', 'sv'])
    ->name('napthe.show.fixed');

// /nap-coin -> trang nạp KNB (gộp hướng dẫn + form)
Route::get('/nap-coin', [NapTheController::class, 'showNapCoin'])->name('napthe.coin');
Route::post('/nap-coin', [NapTheController::class, 'submit']);

// "daily" -> /dai-ly.{Id}.aspx -> DangNhapV2.aspx (đã khai báo ở trên: /dai-ly)
Route::post('/dai-ly-dang-xuat', [DaiLyController::class, 'logout'])->name('daily.logout');

// Các trang quản lý của Đại lý - yêu cầu đăng nhập bằng cookie "auth" + bảng DaiLyKNB
Route::middleware('daily.auth')->group(function () {
    // DaiLyNapThe.aspx -> /dai-ly-nap-the
    Route::get('/dai-ly-nap-the', [DaiLyController::class, 'napThe'])->name('daily.napthe');
    Route::post('/dai-ly-nap-the/tim-sdt', [DaiLyController::class, 'searchPhone']);
    Route::post('/dai-ly-nap-the/xem-pass', [DaiLyController::class, 'showPassword']);
    Route::post('/dai-ly-nap-the/doi-sdt', [DaiLyController::class, 'changePhone']);
    Route::post('/dai-ly-nap-the/dang-ky-nap', [DaiLyController::class, 'registerTopUp']);
    // (tính năng mới, không có trong code gốc) Đổi mật khẩu cấp 2 cho tài khoản game
    Route::post('/dai-ly-nap-the/doi-mat-khau-tk', [DaiLyController::class, 'changeAccountPassword']);

    // TongDaiLy.aspx -> /tong-dai-ly
    Route::get('/tong-dai-ly', [DaiLyController::class, 'tongDaiLy'])->name('daily.tongdaily');
    Route::post('/tong-dai-ly/nap', [DaiLyController::class, 'tongDaiLySubmit']);

    // Thông tin tài khoản đại lý: đổi mật khẩu + cập nhật thông tin liên hệ
    // (tính năng mới, không có trong code gốc)
    Route::get('/dai-ly-doi-mat-khau', [DaiLyController::class, 'showChangePassword'])->name('daily.change-password');
    Route::post('/dai-ly-doi-mat-khau', [DaiLyController::class, 'changePassword']);
    Route::post('/dai-ly-doi-mat-khau/thong-tin', [DaiLyController::class, 'updateInfo']);
});

/*
|--------------------------------------------------------------------------
| Khu vực quản trị Admin6 (task #20-#26)
|--------------------------------------------------------------------------
| Port từ thư mục WebDaiChienTongKim/Admin6 (Default.aspx, AdminSite1.Master,
| AdminPageHome.aspx, AdminPageNews.aspx, AdminPageNewMNG.aspx, AdminNapThe.aspx).
| Đăng nhập dùng session "admin_user" (tương đương Session["UserName"] gốc).
*/
Route::prefix('admin')->group(function () {
    Route::get('/dang-nhap',  [Admin\AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/dang-nhap', [Admin\AuthController::class, 'login'])->name('admin.login.submit');

    Route::middleware(['admin.auth', 'admin.role'])->group(function () {
        Route::post('/dang-xuat', [Admin\AuthController::class, 'logout'])->name('admin.logout');

        Route::get('/', [Admin\HomeController::class, 'home'])->name('admin.home');

        // Tin tức
        Route::get('/tin-tuc',        [Admin\NewsController::class, 'index'])->name('admin.news.index');
        Route::get('/tin-tuc/form',   [Admin\NewsController::class, 'form'])->name('admin.news.form');
        Route::post('/tin-tuc/form',  [Admin\NewsController::class, 'save'])->name('admin.news.save');
        Route::post('/tin-tuc/xoa',   [Admin\NewsController::class, 'delete'])->name('admin.news.delete');

        // Slides
        Route::get('/slides',          [Admin\SlidesController::class, 'index'])->name('admin.slides.index');
        Route::get('/slides/form',     [Admin\SlidesController::class, 'form'])->name('admin.slides.form');
        Route::post('/slides/form',    [Admin\SlidesController::class, 'save'])->name('admin.slides.save');
        Route::post('/slides/xoa',     [Admin\SlidesController::class, 'delete'])->name('admin.slides.delete');
        Route::post('/slides/sap-xep', [Admin\SlidesController::class, 'reorder'])->name('admin.slides.reorder');

        // Nạp thẻ
        Route::get('/nap-the',  [Admin\HomeController::class, 'napThe'])->name('admin.napthe');
        Route::post('/nap-the', [Admin\HomeController::class, 'napTheSubmit']);

        // Cài đặt
        Route::get('/seo',           [Admin\SettingsController::class, 'seoIndex'])->name('admin.seo');
        Route::post('/seo',          [Admin\SettingsController::class, 'seoSave'])->name('admin.seo.save');
        Route::get('/footer',        [Admin\SettingsController::class, 'footerIndex'])->name('admin.footer');
        Route::post('/footer',       [Admin\SettingsController::class, 'footerSave'])->name('admin.footer.save');
        Route::get('/dai-ly-config', [Admin\SettingsController::class, 'dailyIndex'])->name('admin.daily-config');
        Route::post('/dai-ly-config',[Admin\SettingsController::class, 'dailySave'])->name('admin.daily-config.save');
        Route::get('/cai-dat',       [Admin\SettingsController::class, 'generalIndex'])->name('admin.general');
        Route::post('/cai-dat',      [Admin\SettingsController::class, 'generalSave'])->name('admin.general.save');

        // Quản lý tài khoản Đại lý
        Route::get('/dai-ly',             [Admin\DaiLyAccountController::class, 'index'])->name('admin.dai-ly.index');
        Route::get('/dai-ly/form',        [Admin\DaiLyAccountController::class, 'form'])->name('admin.dai-ly.form');
        Route::post('/dai-ly/form',       [Admin\DaiLyAccountController::class, 'save'])->name('admin.dai-ly.save');
        Route::post('/dai-ly/xoa',        [Admin\DaiLyAccountController::class, 'delete'])->name('admin.dai-ly.delete');
        Route::post('/dai-ly/kich-hoat',  [Admin\DaiLyAccountController::class, 'toggle'])->name('admin.dai-ly.toggle');
        Route::post('/dai-ly/nap',        [Admin\DaiLyAccountController::class, 'topUp'])->name('admin.dai-ly.topup');
    });
});
