<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('exercise_words', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('exercise_id');
            $table->uuid('word_id');
            $table->integer('word_order');
            $table->timestamps();

            $table->foreign('exercise_id')->references('id')->on('exercises')->onDelete('cascade');
            $table->foreign('word_id')->references('id')->on('words')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exercise_words');
    }
};
