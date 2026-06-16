<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bảng dbo.DaiLyNgayVang - cấu hình "ngày vàng" tăng % chiết khấu nạp.
 * Port từ DaiLyNgayVang (DataClasses1.designer.cs). Bảng này không có khóa
 * chính riêng (chỉ dùng để đọc cấu hình theo CfName, ví dụ "NgayVangNap").
 */
class DaiLyNgayVang extends Model
{
    protected $connection = 'sqlsrv';

    protected $table = 'DaiLyNgayVang';

    protected $primaryKey = 'CfName';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $guarded = [];

    protected $casts = [
        'DateTime' => 'datetime',
        'EndDate' => 'datetime',
    ];
}
