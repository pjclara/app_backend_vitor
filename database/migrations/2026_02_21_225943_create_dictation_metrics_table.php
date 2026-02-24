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
        Schema::create('dictation_metrics', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('student_id');
            $table->uuid('exercise_id');
            $table->string('difficulty');
            $table->integer('correct_count')->default(0);
            $table->integer('error_count')->default(0);
            $table->integer('missing_count')->default(0);
            $table->integer('extra_count')->default(0);
            $table->decimal('accuracy_percent', 5, 2)->default(0.00);
            $table->integer('letter_omission_count')->default(0);
            $table->integer('letter_insertion_count')->default(0);
            $table->integer('letter_substitution_count')->default(0);
            $table->decimal('transposition_count', 8, 2)->default(0.00);
            $table->integer('split_join_count')->default(0);
            $table->integer('punctuation_error_count')->default(0);
            $table->integer('capitalization_error_count')->default(0);
            $table->json('error_words')->nullable();
            $table->text('resolution')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            
            // Index for better performance
            $table->index(['student_id', 'created_at']);
            $table->index(['exercise_id', 'difficulty']);
            $table->index('accuracy_percent');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dictation_metrics');
    }
};
