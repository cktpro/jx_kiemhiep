<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng dbo.DaiLyKNB (database "account", SQL Server)
 * - port từ DataClasses1.designer.cs (DaiLyKNB)
 *
 * Bảng tài khoản đại lý nạp thẻ (đăng nhập riêng qua route /dai-ly.{Id}).
 */
class DaiLyKnb extends Model
{
    protected $connection = 'sqlsrv';

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
