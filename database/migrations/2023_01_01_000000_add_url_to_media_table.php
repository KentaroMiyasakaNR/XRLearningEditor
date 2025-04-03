<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // カラムが存在しない場合のみ追加
            if (!Schema::hasColumn('media', 'url')) {
                $table->string('url')->nullable()->comment('動画URL')->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('media', function (Blueprint $table) {
            // カラムが存在する場合のみ削除
            if (Schema::hasColumn('media', 'url')) {
                $table->dropColumn('url');
            }
        });
    }
}; 