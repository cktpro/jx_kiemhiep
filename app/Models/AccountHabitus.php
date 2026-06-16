<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng dbo.Account_Habitus (database "account", SQL Server)
 * - port từ DataClasses1.designer.cs (Account_Habitus)
 *
 * Bảng lưu thông tin "thói quen"/gói cước/điểm mở rộng đi kèm Account_Info,
 * được tạo cùng lúc với Account_Info khi đăng ký tài khoản mới.
 */
class AccountHabitus extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'Account_Habitus';

    protected $primaryKey = 'iid';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['iid'];

    protected function casts(): array
    {
        return [
            'dBeginDate' => 'datetime',
            'dEndDate' => 'datetime',
        ];
    }
}
