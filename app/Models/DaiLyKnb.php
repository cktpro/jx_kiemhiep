<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng DaiLyKNB (database "account", MariaDB)
 *
 * Bảng tài khoản đại lý nạp thẻ (đăng nhập riêng qua route /dai-ly.{Id}).
 */
class DaiLyKnb extends Model
{
    protected $connection = 'mysql';

    protected $table = 'DaiLyKNB';

    protected $primaryKey = 'ID';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['ID'];

    protected function casts(): array
    {
        return [
            'iYuanBao' => 'integer',
            'IsAdmin' => 'integer',
            'ChietKhau' => 'integer',
            'KichHoat' => 'boolean',
        ];
    }
}
