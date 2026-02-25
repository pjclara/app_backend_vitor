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
        Schema::create('profissional_student', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('profissional_id');
            $table->uuid('student_id');
            $table->timestamps();
            
            $table->foreign('profissional_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('users')->onDelete('cascade');
            
            // Evitar duplicatas para a mesma associação
            $table->unique(['profissional_id', 'student_id']);
            
            // Índices para melhor performance
            $table->index('profissional_id');
            $table->index('student_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profissional_student');
    }
};
