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
        Schema::table('questions', function (Blueprint $table) {
            // メディアファイル関連のカラム
            $table->string('media_name')->nullable()->after('points');
            
            // 解説関連のカラム
            $table->text('explanation_text')->nullable()->after('media_name');
            $table->string('explanation_image_name')->nullable()->after('explanation_text');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn([
                'media_name',
                'explanation_text',
                'explanation_image_name',
            ]);
        });
    }
}; 