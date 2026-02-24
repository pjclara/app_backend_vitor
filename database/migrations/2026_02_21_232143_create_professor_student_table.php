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
        Schema::create('professor_student', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('professor_id');
            $table->uuid('student_id');
            $table->timestamps();
            
            $table->foreign('professor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            
            // Evitar duplicatas para a mesma associação
            $table->unique(['professor_id', 'student_id']);
            
            // Índices para melhor performance
            $table->index('professor_id');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('professor_student');
    }
};
