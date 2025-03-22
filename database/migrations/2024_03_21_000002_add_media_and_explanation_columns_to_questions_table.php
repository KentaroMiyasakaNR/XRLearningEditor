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
            // カラムが存在しない場合のみ追加
            if (!Schema::hasColumn('questions', 'media_name')) {
                $table->string('media_name')->nullable()->after('points');
            }
            
            if (!Schema::hasColumn('questions', 'explanation_text')) {
                $table->text('explanation_text')->nullable()->after('media_name');
            }
            
            if (!Schema::hasColumn('questions', 'explanation_image_name')) {
                $table->string('explanation_image_name')->nullable()->after('explanation_text');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('questions', function (Blueprint $table) {
            // カラムが存在する場合のみ削除
            $columns = [
                'media_name',
                'explanation_text',
                'explanation_image_name',
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('questions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}; 