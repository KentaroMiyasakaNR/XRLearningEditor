<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('level')->default('S'); // S, A, B, C などの難易度
            $table->timestamps();
        });

        Schema::create('questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->onDelete('cascade');
            $table->text('question_text');
            $table->integer('points')->default(1);
            $table->string('media_name')->nullable(); // 動画や画像のファイル名
            $table->text('explanation_text')->nullable(); // 解説テキスト
            $table->string('explanation_image_name')->nullable(); // 解説用画像のファイル名
            $table->timestamps();
        });

        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id')->constrained()->onDelete('cascade');
            $table->text('option_text');
            $table->boolean('is_correct')->default(false);
            $table->foreignId('next_quiz_id')->nullable()->constrained('quizzes')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('options');
        Schema::dropIfExists('questions');
        Schema::dropIfExists('quizzes');
    }
}; 