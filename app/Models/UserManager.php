<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng dbo.UserManager (database "account", SQL Server)
 * - port từ DataClasses1.designer.cs (UserManager).
 *
 * Lưu tài khoản đăng nhập khu vực Admin6 (trước đây Default.aspx.cs dùng
 * tài khoản cứng trong code, nay lấy từ bảng này).
 *
 * Cột: iid (PK identity), cUserCode (tên đăng nhập), cPassWord (mật khẩu),
 * cUserName (tên hiển thị), iRole (phân quyền), iFlag (bật/tắt tài khoản),
 * cEmail.
 */
class UserManager extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'UserManager';

    protected $primaryKey = 'iid';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['iid'];

    protected function casts(): array
    {
        return [
            'iRole' => 'integer',
            'iFlag' => 'boolean',
        ];
    }
}
