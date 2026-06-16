<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Eloquent model cho bảng dbo.Category (database "jxm_news", SQL Server)
 * - port từ DataClasses2.designer.cs (Category, TableAttribute Name="dbo.Category")
 *
 * Lưu ý: tên bảng thật trong DB là "Category" (số ít), khác với tên
 * property "Categories" trong DataContext gốc.
 */
class Category extends Model
{
    protected $connection = 'sqlsrv_news';

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
