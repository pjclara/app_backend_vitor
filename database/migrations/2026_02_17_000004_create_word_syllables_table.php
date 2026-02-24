<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('word_syllables', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('word_id');
            $table->string('syllable');
            $table->unsignedInteger('position');
            $table->string('audio_url')->nullable();
            $table->timestamps();

            $table->foreign('word_id')
                ->references('id')
                ->on('words')
                ->onDelete('cascade');

            $table->unique(['word_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('word_syllables');
    }
};
