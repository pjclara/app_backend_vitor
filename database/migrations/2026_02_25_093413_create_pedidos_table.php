<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pedidos', function (Blueprint $table) {

            // UUID com default gen_random_uuid() (PostgreSQL)
            $table->uuid('id')
                ->primary()
                ->default(DB::raw('gen_random_uuid()'));

            $table->uuid('solicitante_id');
            $table->enum('solicitante_tipo', ['aluno', 'profissional']);

            $table->uuid('destinatario_id');
            $table->enum('destinatario_tipo', ['aluno', 'profissional']);

            $table->enum('status', ['pendente', 'aceite', 'recusado'])
                ->default('pendente');

            $table->timestampTz('criado_em')
                ->default(DB::raw('NOW()'));

            $table->timestampTz('respondido_em')
                ->nullable();

            // Constraint UNIQUE (solicitante_id, destinatario_id, status)
            $table->unique(
                ['solicitante_id', 'destinatario_id', 'status'],
                'unique_pending_request'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};