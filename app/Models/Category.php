<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng Category (database "jxm_news", MariaDB)
 */
class Category extends Model
{
    protected $connection = 'mysql_news';

    protected $table = 'Category';

    protected $primaryKey = 'Id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = false;

    protected $guarded = ['Id'];

    public function news()
    {
        return $this->hasMany(News::class, 'categoryId', 'Id');
    }
}
