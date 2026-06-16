<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cột slug vào bảng FK_News (connection: sqlsrv_news).
 *
 * Slug được sinh từ title qua slugify_vn() - giống logic hiện tại trong
 * News::getSlugAttribute(). Để tránh trùng (2 bài có title giống nhau),
 * thêm hậu tố "-{id}" khi phát hiện duplicate.
 */
return new class extends Migration
{
    protected $connection = 'sqlsrv_news';

    public function up(): void
    {
        Schema::connection('sqlsrv_news')->table('FK_News', function (Blueprint $table) {
            $table->string('slug', 500)->nullable()->after('title');
        });

        // Populate slug cho tất cả bài viết hiện có
        $rows = DB::connection('sqlsrv_news')
            ->table('FK_News')
            ->orderBy('id')
            ->get(['id', 'title']);

        $seen = [];

        foreach ($rows as $row) {
            $base = slugify_vn((string) $row->title);
            $slug = $base;

            if (isset($seen[$slug])) {
                $slug = $base . '-' . $row->id;
            }

            $seen[$slug] = true;

            DB::connection('sqlsrv_news')
                ->table('FK_News')
                ->where('id', $row->id)
                ->update(['slug' => $slug]);
        }
    }

    public function down(): void
    {
        Schema::connection('sqlsrv_news')->table('FK_News', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
