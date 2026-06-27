<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng Account_Info (database "account", MariaDB)
 *
 * Bảng lưu thông tin tài khoản người chơi (đăng nhập game).
 * KHÔNG dùng cho Auth mặc định của Laravel (xem App\Models\User).
 */
class AccountInfo extends Model
{
    protected $connection = 'mysql';

    protected $table = 'Account_Info';

    protected $primaryKey = 'iid';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['iid'];

    protected function casts(): array
    {
        return [
            'dBirthDay' => 'datetime',
            'dRegDate' => 'datetime',
            'iClientID' => 'integer',
            'iTimeCount' => 'integer',
            'iMoney' => 'integer',
            'iYuanbao' => 'integer',
        ];
    }
}
