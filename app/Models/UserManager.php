<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng UserManager (database "account", MariaDB)
 *
 * Lưu tài khoản đăng nhập khu vực Admin.
 * Cột: iid (PK), cUserCode (tên đăng nhập), cPassWord, cUserName (tên hiển thị),
 * iRole (phân quyền), iFlag (bật/tắt), cEmail.
 */
class UserManager extends Model
{
    protected $connection = 'mysql';

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
