# JX Kiếm Hiệp Mobile - Laravel (chuyển đổi từ ASP.NET WebForms)

Dự án này là bản chuyển đổi các trang **công khai** (outside) của project ASP.NET
WebForms `WebDaiChienTongKim` sang **Laravel 11 / PHP 8.2**, giữ nguyên database
SQL Server (`account` + `jxm_news`) qua driver **PDO_SQLSRV/sqlsrv**, không thay
đổi schema/dữ liệu.

> ⚠️ Project được hand-author (viết tay từng file) trong sandbox không có
> Composer/PHP/artisan. Bạn cần chạy `composer install` trên máy có PHP 8.2 +
> extension sqlsrv để cài `vendor/` và sinh các file framework còn thiếu.

## 0. Cài đặt nhanh với XAMPP (Windows)

XAMPP cung cấp sẵn PHP + Apache. Project này **không dùng MySQL của XAMPP** (DB
vẫn là SQL Server `account` + `jxm_news` có sẵn), nên chỉ cần PHP + Apache.

### 0.1. Cài XAMPP + Composer

1. Cài **XAMPP cho PHP 8.2** (ví dụ XAMPP 8.2.x tải từ apachefriends.org).
   Kiểm tra lại version: mở CMD, gõ `C:\xampp\php\php.exe -v` → phải ra `8.2.x`.
2. Cài **Composer** (composer.exe installer) - khi cài nó sẽ tự nhận
   `C:\xampp\php\php.exe`.
3. Thêm `C:\xampp\php` vào biến môi trường `PATH` để gõ được `php`, `composer`
   trực tiếp.

### 0.2. Cài driver SQL Server cho PHP của XAMPP

PHP đi kèm XAMPP là bản **Thread Safe (TS)**. Tải đúng cặp DLL TS, PHP 8.2,
x64 từ trang Microsoft Drivers for PHP for SQL Server (repo `microsoft/msphpsql`,
phần Releases):

- `php_pdo_sqlsrv_82_ts_x64.dll`
- `php_sqlsrv_82_ts_x64.dll`

Copy 2 file vào `C:\xampp\php\ext\`, rồi mở `C:\xampp\php\php.ini` thêm:

```ini
extension=php_pdo_sqlsrv_82_ts_x64
extension=php_sqlsrv_82_ts_x64
```

Khởi động lại Apache (nếu dùng) và kiểm tra:

```powershell
php -m | findstr -i sqlsrv
```

phải thấy cả `pdo_sqlsrv` và `sqlsrv`.

> Nếu máy thiếu `msodbcsql18` (ODBC Driver 18 for SQL Server), driver sqlsrv sẽ
> không load được - cài thêm từ trang Microsoft "ODBC Driver for SQL Server".

### 0.3. Cài project

```powershell
cd D:\CODE\Code_Web\jx_kiemhiep\laravel
composer install
copy .env.example .env
php artisan key:generate
```

Sửa `.env`: điền `DB_*` (database `account`) và `DB_NEWS_*` (database
`jxm_news`) đúng với SQL Server thật (host, user, password).

```powershell
php artisan migrate --database=sqlite
```

### 0.4. Chạy thử

**Cách nhanh nhất** (không cần cấu hình Apache):

```powershell
php artisan serve
```

→ mở `http://127.0.0.1:8000`.

**Hoặc dùng Apache của XAMPP** (nếu muốn test giống môi trường thật): tạo
VirtualHost trong `C:\xampp\apache\conf\extra\httpd-vhosts.conf` trỏ
`DocumentRoot` vào `.../jx_kiemhiep/laravel/public` (KHÔNG trỏ vào thư mục
gốc project), bật `AllowOverride All` để `.htaccess` của Laravel hoạt động
(rewrite về `index.php`), rồi thêm domain vào `C:\Windows\System32\drivers\etc\hosts`.
`mod_rewrite` đã được bật sẵn trong XAMPP.

## 1. Deploy lên aaPanel (Linux VPS)

### 1.1. Yêu cầu phía SQL Server (Windows)

SQL Server phải cho phép kết nối TCP từ IP của VPS:

1. **SQL Server Configuration Manager** → Protocols for MSSQLSERVER → TCP/IP → Enable
2. TCP/IP Properties → IP Addresses → IPAll → TCP Port = `1433`
3. **Windows Firewall** → Inbound Rules → New Rule → Port `1433` → Allow
4. SQL Server → Security → Server Authentication: **SQL Server and Windows Authentication mode**
5. Restart SQL Server service

### 1.2. Cài PHP extensions trên VPS

aaPanel dùng Linux. Các extension sau cần bật trong **aaPanel → PHP 8.2 → Install extensions**:

| Extension | Trạng thái trong aaPanel |
|---|---|
| `pdo` | Có sẵn, bật trong PHP Manager |
| `mbstring` | Có sẵn, bật trong PHP Manager |
| `openssl` | Có sẵn, bật trong PHP Manager |
| `fileinfo` | Có sẵn, bật trong PHP Manager |
| `tokenizer` | Có sẵn, bật trong PHP Manager |
| `ctype` | Có sẵn, bật trong PHP Manager |
| `xml` | Có sẵn, bật trong PHP Manager |
| `sqlsrv` + `pdo_sqlsrv` | **Phải cài thủ công qua PECL** (xem bên dưới) |

**Cài `sqlsrv` + `pdo_sqlsrv` thủ công** (chạy trong Terminal của aaPanel):

```bash
# Bước 1: Cài ODBC Driver của Microsoft
curl https://packages.microsoft.com/keys/microsoft.asc | apt-key add -
curl https://packages.microsoft.com/config/ubuntu/$(lsb_release -rs)/prod.list \
     > /etc/apt/sources.list.d/mssql-release.list
apt-get update
ACCEPT_EULA=Y apt-get install -y msodbcsql18 unixodbc-dev

# Bước 2: Cài extension qua PECL của PHP 8.2
/www/server/php/82/bin/pecl install sqlsrv pdo_sqlsrv

# Bước 3: Bật trong php.ini
echo "extension=sqlsrv.so"     >> /www/server/php/82/etc/php.ini
echo "extension=pdo_sqlsrv.so" >> /www/server/php/82/etc/php.ini

# Bước 4: Restart PHP-FPM
/etc/init.d/php-fpm-82 restart

# Kiểm tra
/www/server/php/82/bin/php -m | grep sqlsrv
# Phải thấy cả "sqlsrv" và "pdo_sqlsrv"
```

> Extensions **không cần cài**: `gd`/`imagick` (upload ảnh chỉ move file, không xử lý),
> `redis` (session + cache dùng `file` driver), `curl` (không gọi API ngoài), `zip`, `sqlite3`.

### 1.3. Tạo website trên aaPanel

1. **aaPanel → Website → Add site**
   - Domain: tên miền thật
   - PHP version: `8.2`
2. **Running directory**: chọn `/public` (bắt buộc — không trỏ vào thư mục gốc)
3. **Rewrite rules**: chọn template **Laravel**

### 1.4. Upload code và cài dependencies

```bash
cd /www/wwwroot/yourdomain.com

# Cài Composer dependencies (không cần dev packages)
composer install --no-dev --optimize-autoloader
```

### 1.5. Cấu hình .env

```bash
cp .env.example .env
php artisan key:generate
```

Mở `.env` và điền các giá trị thật:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com

DB_HOST=<IP SQL Server>
DB_PASSWORD=<password>

DB_NEWS_HOST=<IP SQL Server>
DB_NEWS_PASSWORD=<password>
```

### 1.6. Cấp quyền và hoàn tất

```bash
# Quyền ghi cho storage và cache
chown -R www:www /www/wwwroot/yourdomain.com
chmod -R 775 storage bootstrap/cache

# Optimize cho production
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 1.7. Kiểm tra sau deploy

```bash
# Test kết nối SQL Server
php artisan tinker
>>> DB::connection('sqlsrv')->select('SELECT 1 AS ok')
>>> DB::connection('sqlsrv_news')->select('SELECT 1 AS ok')
# Cả 2 phải trả về [{"ok":1}]
```

Nếu kết nối thành công, mở trình duyệt truy cập domain — site đã hoạt động.

---

## 2. Cài đặt (tổng quan / các hệ điều hành khác)

### 2.1. Yêu cầu hệ thống

- PHP **8.2**
- Composer 2.x
- Extension **pdo_sqlsrv** và **sqlsrv** (Microsoft Drivers for PHP for SQL
  Server) - bắt buộc để kết nối 2 database SQL Server `account` và `jxm_news`
  - Windows: tải `php_pdo_sqlsrv_82_ts_x64.dll` và `php_sqlsrv_82_ts_x64.dll`
    từ Microsoft (microsoft/msphpsql trên GitHub), copy vào thư mục `ext` của
    PHP và bật trong `php.ini`:
    ```ini
    extension=php_pdo_sqlsrv_82_ts_x64
    extension=php_sqlsrv_82_ts_x64
    ```
  - Linux: cài `msodbcsql18` + `unixodbc-dev`, sau đó `pecl install sqlsrv pdo_sqlsrv`
- Extension PHP chuẩn khác mà Laravel 11 cần: `mbstring`, `openssl`, `tokenizer`,
  `ctype`, `fileinfo`, `pdo`, `xml`, `intl` (thường có sẵn trong PHP cài từ XAMPP/
  WAMP/Laragon bản mới)

### 2.2. Các bước

```bash
cd jx_kiemhiep/laravel

# 1. Cài dependencies (vendor/)
composer install

# 2. Tạo file .env từ mẫu
copy .env.example .env        # Windows
# cp .env.example .env        # Linux/macOS

# 3. Sinh APP_KEY
php artisan key:generate

# 4. Sửa .env: điền thông tin kết nối SQL Server thật cho 2 khối DB
#    - DB_* (database "account")
#    - DB_NEWS_* (database "jxm_news")

# 5. Tạo database sqlite rỗng (chỉ dùng cho cache/session/queue nội bộ
#    của Laravel, KHÔNG chứa dữ liệu game)
php artisan migrate --database=sqlite

# 6. Chạy thử
php artisan serve
```

Sau khi `composer install` chạy xong, kiểm tra lại các file đã hand-author bên
dưới vẫn còn nguyên vẹn (Composer không nên ghi đè `app/`, `routes/`,
`resources/`, `config/site.php`, `.env.example` - chỉ tạo `vendor/` và một số
file khung còn thiếu trong `bootstrap/cache/`, `storage/`).

## 3. Cấu trúc đã chuyển đổi

| # | Nhóm trang | Trạng thái |
|---|---|---|
| 1 | Khung project Laravel 11 (PHP 8.2) | ✅ |
| 2 | Kết nối SQL Server (`sqlsrv` = account, `sqlsrv_news` = jxm_news) | ✅ |
| 3 | `ClassHeader1.cs` → `app/Helpers/helpers.php` + `config/site.php` | ✅ |
| 4 | `Site.Master` → `resources/views/layouts/*.blade.php` | ✅ |
| 5 | Đăng nhập / Đăng ký / Quên mật khẩu | ✅ |
| 6 | Tài khoản (Thông tin / Đổi mật khẩu / Đổi SĐT) | ✅ |
| 7 | Nạp thẻ / Đại lý | ✅ |
| 8 | Tin tức + Trang chủ + Tải game | ✅ |
| 9 | `routes/web.php` khớp với `Global.asax` | ✅ |
| 10 | Rà soát & tổng hợp | ✅ (tài liệu này) |

## 4. Bảng đối chiếu route (Global.asax → Laravel)

| Route name (Global.asax) | URL cũ | Trang .aspx | URL mới (Laravel) | Controller |
|---|---|---|---|---|
| *(không có)* | `/` | `Default.aspx` | `GET /` | `HomeController@index` |
| *(không có)* | `/tai-game` | `TaiGame.aspx` (redirect → bài id=3) | `GET /tai-game` | `HomeController@taiGame` |
| `alltin` | `/tin-tuc/all.aspx` | `DanhSachTin.aspx` | `GET /tin-tuc/all` | `NewsController@index` |
| `chitiettin` | `/tin-tuc/{link}.{Id}.aspx` | `ChiTietTinV2.aspx` | `GET /tin-tuc/{slug}.{id}` | `NewsController@show` |
| `dangnhap2` | `/dang-nhap` | `DangNhapV2.aspx` | `GET\|POST /dang-nhap` | `AuthController` |
| `daily` | `/dai-ly.{Id}.aspx` | `DangNhapV2.aspx` | `GET\|POST /dai-ly` | `AuthController` |
| `dangky2` | `/dang-ky` | `DangKyV2.aspx` | `GET\|POST /dang-ky` | `AuthController` |
| `quenmk2` | `/quen-mat-khau` | `QuenMatKhauV2.aspx` | `GET\|POST /quen-mat-khau` | `PasswordController` |
| `taikhoan2` | `/tai-khoan` | `ThongTinTaiKhoanV2.aspx` | `GET /tai-khoan` | `AccountController@show` |
| `doimk2` | `/doi-mat-khau` | `DoiMatKhauV2.aspx` | `GET\|POST /doi-mat-khau` | `AccountController` |
| `doisdt2` | `/cap-nhat-so-dien-thoai` | `DoiSoDienThoaiV2.aspx` | `GET\|POST /cap-nhat-so-dien-thoai` | `AccountController` |
| `napthe1` | `/nap-the` | `NapThe.aspx` | `GET\|POST /nap-the` | `NapTheController@show/submit` |
| `napthe` | `/nap-the.{Id}.{Sv}.aspx` | `NapThe.aspx` | `GET /nap-the/{id}/{sv}` | `NapTheController@show` |
| `napthe2` | `/nap-coin` | `NapTheV2.aspx` | `GET /nap-coin` | `NapTheController@showNapCoin` |
| *(không có)* | `/dai-ly-nap-the` | `DaiLyNapThe.aspx` | `GET /dai-ly-nap-the` (+ AJAX) | `DaiLyController@napThe` |
| *(không có)* | `/tong-dai-ly` | `TongDaiLy.aspx` | `GET\|POST /tong-dai-ly` | `DaiLyController@tongDaiLy` |

Lưu ý: `/tin-tuc/all` được đăng ký **trước** `/tin-tuc/{slug}.{id}` để tránh
xung đột route (mặc dù `{slug}.{id}` yêu cầu dấu `.` trong segment nên `all`
không khớp, việc đăng ký trước vẫn là cách an toàn nhất).

## 5. Các điểm cần lưu ý

- **`app/Http/Controllers/Controller.php`**: file base class này **bị thiếu**
  hoàn toàn trong project gốc (mọi controller khác đều `extends Controller`
  nhưng class này không tồn tại). Đã được tạo bổ sung
  (`abstract class Controller {}`) - đây là một phần bắt buộc của khung Laravel
  11, không phải lỗi phát sinh từ việc chuyển đổi.
- **Slug bài viết** (`/tin-tuc/{slug}.{id}`): vì không có mã nguồn của hàm SQL
  `dbo.fChuyenCoDauThanhKhongDau()`, slug được build lại bằng helper PHP
  `slugify_vn()` (bỏ dấu tiếng Việt + lowercase + thay ký tự đặc biệt bằng `-`).
  `{id}` mới là khóa tra cứu thật, `{slug}` chỉ phục vụ SEO/URL đẹp - đúng theo
  hành vi gốc (RouteData["link"] không được dùng để query).
- **Trang chủ**: `Global.asax` không có route khai báo cho `/`. Ban đầu
  `DefaultV2.aspx` được chọn làm trang chủ theo quy ước đặt tên "V2" áp dụng
  cho mọi trang khác đã chuyển đổi (DangNhapV2, DangKyV2, ThongTinTaiKhoanV2,
  ChiTietTinV2...), nhưng theo yêu cầu người dùng, `home/index.blade.php` đã
  được viết lại theo `Default.aspx` (theme jx1m skin-2020 đầy đủ: banner +
  slider nhân vật môn phái + carousel tính năng đặc sắc + 4 tab tin tức theo
  danh mục). `Load_List_Tin($categoryId)` được port thành
  `HomeController::loadListTin()` + partial `home/partials/list-tin.blade.php`.
- **Danh sách tin tức**: route `alltin` trỏ tới `DanhSachTin.aspx` (bản
  **không** có "V2", phân trang đơn giản 10 bài/trang bằng `paginate()`).
  Bản `DanhSachTinV2.aspx` (có tab AJAX theo danh mục) **không** được dùng vì
  không nằm trong route đang hoạt động.

## 6. Cập nhật SQL Server 2014 trên Windows Server 2016

Laravel yêu cầu driver **ODBC Driver 18 for SQL Server** — driver này chỉ tương
thích với SQL Server 2014 **SP3 + CU4 trở lên**. Nếu chưa cập nhật, kết nối từ
PHP sẽ bị lỗi `SSL Provider: The certificate chain was issued by an authority that is not trusted`.

### Bước 1 — Cài Service Pack 3 (SP3)

> Bắt buộc cài trước CU.

**SQL Server 2014 SP3** — [Tải về (~580 MB)](https://download.microsoft.com/download/7/9/f/79f4584a-a957-436b-8534-3397f33790a6/SQLServer2014SP3-KB4022619-x64-ENU.exe)

```
https://download.microsoft.com/download/7/9/f/79f4584a-a957-436b-8534-3397f33790a6/SQLServer2014SP3-KB4022619-x64-ENU.exe
```

1. Chạy file `.exe` với quyền Administrator.
2. Wizard sẽ phát hiện instance hiện tại — chọn **Upgrade** và làm theo hướng dẫn.
3. Sau khi hoàn tất, SQL Server service sẽ tự restart.
4. Kiểm tra: mở **SQL Server Management Studio (SSMS)** → chạy
   `SELECT @@VERSION` → phải thấy `12.0.6024` hoặc cao hơn.

### Bước 2 — Cài Cumulative Update 4 (CU4 for SP3)

**SQL Server 2014 SP3 CU4** — [Tải về (~60 MB)](https://download.microsoft.com/download/a/5/a/a5aacc94-29a5-4890-90bd-847320ee0e93/SQLServer2014-KB4500181-x64.exe)

```
https://download.microsoft.com/download/a/5/a/a5aacc94-29a5-4890-90bd-847320ee0e93/SQLServer2014-KB4500181-x64.exe
```

1. Chạy file `.exe` sau khi đã cài SP3 xong.
2. Làm theo wizard — chọn đúng instance cần cập nhật.
3. SQL Server service restart lại sau khi hoàn tất.
4. Kiểm tra: `SELECT @@VERSION` → phải thấy `12.0.6329` hoặc cao hơn.

### Bước 3 — Khởi động lại máy chủ

```powershell
Restart-Computer
```

Sau khi restart, thử kết nối lại từ PHP:

```bash
php artisan tinker
>>> DB::connection('sqlsrv')->select('SELECT 1 AS ok')
```

> **Lưu ý:** Nếu vẫn lỗi SSL sau khi cập nhật, thêm `TrustServerCertificate=true`
> vào connection string trong `.env`:
> ```env
> DB_SQLSRV_TRUST_SERVER_CERTIFICATE=true
> ```
> hoặc thêm vào `config/database.php` ở block `sqlsrv`:
> ```php
> 'options' => [PDO::SQLSRV_ATTR_ENCODING => PDO::SQLSRV_ENCODING_UTF8],
> 'trust_server_certificate' => true,
> ```

---

## 7. Phạm vi KHÔNG chuyển đổi (out of scope)

- **Admin6** (toàn bộ khu vực quản trị) - theo yêu cầu ban đầu.
- Các endpoint AJAX `Home/api/post/subPostList` và `Home/api/post/homePostList`
  được tham chiếu trong JS của `DefaultV2.aspx`/`DanhSachTinV2.aspx`: đã tìm
  trong toàn bộ source `WebDaiChienTongKim` nhưng **không tồn tại** - có thể
  thuộc một ứng dụng/API riêng biệt không nằm trong source được cung cấp. Vì
  trang đang hoạt động thực tế (`DanhSachTin` non-V2 + `DefaultV2` server-render
  `GetTin1()`) không phụ thuộc các endpoint này, chức năng tab AJAX không được
  triển khai lại.
- `ChiTietTin.aspx` (bản non-V2, không có route trong `Global.asax`) không
  được chuyển đổi. `Default.aspx` (bản non-V2) **đã** được chuyển đổi và dùng
  làm trang chủ "/" (xem mục ghi chú "Trang chủ" ở trên).
