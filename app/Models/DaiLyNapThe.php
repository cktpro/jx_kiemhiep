<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bảng dbo.DaiLyNapThe - lịch sử đăng ký nạp KNB qua Đại lý.
 * Port từ DaiLyNapThe (DataClasses1.designer.cs).
 */
class DaiLyNapThe extends Model
{
    protected $connection = 'mysql';

    protected $table = 'dailynapthe';

    protected $primaryKey = 'ID';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['ID'];

    protected $casts = [
        'DateNap' => 'datetime',
        'TrangThai' => 'integer',
        'SoKNB' => 'integer',
        'SoKNBKM' => 'integer',
        'SoTien' => 'integer',
        'KNBTruoc' => 'integer',
        'KNBSau' => 'integer',
    ];
}
