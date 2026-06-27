<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bảng dbo.LichSuDoiSDT - log đổi SĐT bảo vệ qua Đại lý.
 * Port từ LichSuDoiSDT (DataClasses1.designer.cs).
 */
class LichSuDoiSdt extends Model
{
    protected $connection = 'mysql';

    protected $table = 'LichSuDoiSDT';

    protected $primaryKey = 'Id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['Id'];

    protected $casts = [
        'Date' => 'datetime',
        'PhiKNB' => 'integer',
        'KNBTruoc' => 'integer',
        'KNBSau' => 'integer',
    ];
}
