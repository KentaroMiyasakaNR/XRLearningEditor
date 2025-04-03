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
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->string('filename')->comment('メディアファイル名');
            $table->enum('type', ['videos', 'images'])->comment('メディアタイプ');
            $table->string('title')->nullable()->comment('表示用タイトル');
            $table->text('description')->nullable()->comment('説明');
            $table->string('url')->nullable()->comment('動画URL');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null')->comment('登録ユーザーID');
            $table->timestamps();
            
            // filename + type の組み合わせでユニーク制約
            $table->unique(['filename', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('media');
    }
}; 