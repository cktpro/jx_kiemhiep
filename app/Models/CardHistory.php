<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Bảng dbo.Card_History - lịch sử Tổng Đại lý nạp KNB cho Đại lý con.
 * Port từ Card_History (DataClasses1.designer.cs).
 */
class CardHistory extends Model
{
    protected $connection = 'mysql';

    protected $table = 'Card_History';

    protected $primaryKey = 'iid';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['iid'];

    protected $casts = [
        'dDate' => 'datetime',
        'iFlag' => 'integer',
        'Money' => 'integer',
        'KNBTruoc' => 'integer',
        'KNBSau' => 'integer',
    ];
}
