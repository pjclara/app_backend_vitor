<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('user_progress', function (Blueprint $table) {

            // UUID como primary key e foreign key
            $table->uuid('user_id')->primary();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            // Estrelas (pontos acumulativos)
            $table->integer('stars_total')
                ->default(0);

            // Nível atual
            $table->enum('level', [
                'explorador',
                'leitor',
                'escritor',
                'mestre'
            ])->default('explorador');

            // Dias ativos (array de datas YYYY-MM-DD)
            $table->json('active_days')->default(json_encode([]));

            // Contador de evoluções detectadas
            $table->integer('evolution_count')
                ->default(0);

            // Data do último bónus diário
            $table->date('last_daily_bonus_date')->nullable();

            // Histórico de accuracy
            $table->json('accuracy_history')
                ->default(json_encode([]));

            // Timestamps
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_progress');
    }
};
