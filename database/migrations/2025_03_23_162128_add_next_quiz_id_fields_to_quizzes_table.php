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
        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('next_quiz_id_correct')->nullable()->constrained('quizzes')->nullOnDelete();
            $table->foreignId('next_quiz_id_incorrect')->nullable()->constrained('quizzes')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['next_quiz_id_correct']);
            $table->dropForeign(['next_quiz_id_incorrect']);
            $table->dropColumn(['next_quiz_id_correct', 'next_quiz_id_incorrect']);
        });
    }
};
