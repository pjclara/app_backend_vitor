<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Only run if word_syllables table exists and has the 'syllable' column
        if (!Schema::hasTable('word_syllables') || !Schema::hasColumn('word_syllables', 'syllable')) {
            return;
        }

        // Only run if syllables table exists
        if (!Schema::hasTable('syllables')) {
            return;
        }

        // Passo 1: Criar registos únicos de sílabas na tabela syllables a partir de word_syllables
        $syllables = DB::table('word_syllables')
            ->select('syllable', 'audio_url')
            ->distinct()
            ->get();

        foreach ($syllables as $syl) {
            DB::table('syllables')->insertOrIgnore([
                'id' => \Illuminate\Support\Str::uuid(),
                'syllable' => $syl->syllable,
                'audio_url' => $syl->audio_url,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Passo 2: Adicionar coluna syllable_id a word_syllables se não existir
        if (!Schema::hasColumn('word_syllables', 'syllable_id')) {
            Schema::table('word_syllables', function (Blueprint $table) {
                $table->uuid('syllable_id')->nullable()->after('word_id');
            });
        }

        // Passo 3: Preencher syllable_id a partir da coluna syllable
        $wordSyllables = DB::table('word_syllables')
            ->whereNull('syllable_id')
            ->get();

        foreach ($wordSyllables as $ws) {
            $syllableId = DB::table('syllables')
                ->where('syllable', $ws->syllable)
                ->value('id');

            if ($syllableId) {
                DB::table('word_syllables')
                    ->where('id', $ws->id)
                    ->update(['syllable_id' => $syllableId]);
            }
        }

        // Passo 4: Fazer syllable_id NOT NULL
        Schema::table('word_syllables', function (Blueprint $table) {
            $table->uuid('syllable_id')->nullable(false)->change();
        });

        // Passo 5: Adicionar foreign key para syllable_id
        Schema::table('word_syllables', function (Blueprint $table) {
            $table->foreign('syllable_id')
                ->references('id')
                ->on('syllables')
                ->onDelete('cascade');
        });

        // Passo 6: Remover a coluna syllable original
        Schema::table('word_syllables', function (Blueprint $table) {
            $table->dropColumn('syllable');
        });
    }

    public function down(): void
    {
        // Reverter: adicionar syllable de volta, remover syllable_id
        if (!Schema::hasTable('word_syllables')) {
            return;
        }

        if (!Schema::hasColumn('word_syllables', 'syllable_id')) {
            return;
        }

        // Restaurar texto de sílabas a partir da tabela syllables
        Schema::table('word_syllables', function (Blueprint $table) {
            $table->string('syllable')->nullable()->after('word_id');
        });

        $wordSyllables = DB::table('word_syllables')->get();

        foreach ($wordSyllables as $ws) {
            $syllableText = DB::table('syllables')
                ->where('id', $ws->syllable_id)
                ->value('syllable');

            if ($syllableText) {
                DB::table('word_syllables')
                    ->where('id', $ws->id)
                    ->update(['syllable' => $syllableText]);
            }
        }

        // Remover foreign key e coluna syllable_id
        Schema::table('word_syllables', function (Blueprint $table) {
            $table->dropForeignIfExists('word_syllables_syllable_id_foreign');
            $table->dropColumn('syllable_id');
        });
    }
};
