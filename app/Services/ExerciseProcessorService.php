<?php

namespace App\Services;

use App\Models\Exercise;
use App\Models\ExerciseWord;
use App\Models\Syllable;
use App\Models\Word;
use App\Models\WordSyllable;
use Illuminate\Support\Facades\DB;

class ExerciseProcessorService
{
    protected PortugueseSyllableSplitter $splitter;

    public function __construct()
    {
        $this->splitter = new PortugueseSyllableSplitter();
    }

    /**
     * Processa um exercício: divide em palavras e sílabas, guarda tudo nas tabelas.
     *
     * @param Exercise $exercise
     * @return void
     */
    public function process(Exercise $exercise): void
    {
        $text = $exercise->sentence;

        // Extrair palavras (remover pontuação, manter apenas palavras)
        $rawWords = $this->extractWords($text);

        if (empty($rawWords)) {
            return;
        }

        // Convert enum difficulty to integer for Word model (1=easy, 2=medium, 3=hard)
        $wordDifficulty = match ($exercise->difficulty?->value ?? 'easy') {
            'easy' => 1,
            'medium' => 2,
            'hard' => 3,
            default => 1,
        };

        DB::transaction(function () use ($exercise, $rawWords, $wordDifficulty) {
            // Limpar relações existentes (para caso de edição)
            ExerciseWord::where('exercise_id', $exercise->id)->delete();

            $wordsData = [];

            foreach ($rawWords as $order => $rawWord) {
                $normalizedWord = mb_strtolower(trim($rawWord));

                if (empty($normalizedWord)) {
                    continue;
                }

                // Procurar ou criar a palavra na tabela words
                $word = Word::where('word', $normalizedWord)->first();

                if (!$word) {
                    $word = $this->createWordWithSyllables($normalizedWord, $wordDifficulty);
                }

                // Criar a relação exercise_words
                ExerciseWord::create([
                    'exercise_id' => $exercise->id,
                    'word_id' => $word->id,
                    'word_order' => $order + 1,
                ]);

                $syllables = $word->wordSyllables->map(fn ($ws) => $ws->syllable->syllable)->implode('-');

                $wordsData[] = [
                    'word' => $normalizedWord,
                    'order' => $order + 1,
                    'syllables' => $syllables,
                    'word_id' => $word->id,
                ];
            }

            // Atualizar o campo words_json do exercício
            $exercise->update([
                'words_json' => json_encode($wordsData),
            ]);
        });
    }

    /**
     * Cria uma palavra e as suas sílabas.
     */
    protected function createWordWithSyllables(string $word, int $difficulty = 1): Word
    {
        // Dividir em sílabas
        $syllables = $this->splitter->split($word);
        $syllablesText = implode('-', $syllables);

        // Criar a palavra
        $wordModel = Word::create([
            'word' => $word,
            'syllables' => $syllablesText,
            'difficulty' => $difficulty,
        ]);

        // Criar as sílabas e as relações
        foreach ($syllables as $position => $syllableText) {
            // Procurar ou criar a sílaba
            $syllableModel = Syllable::firstOrCreate([
                'syllable' => $syllableText,
            ]);

            // Criar a relação word_syllables
            WordSyllable::create([
                'word_id' => $wordModel->id,
                'syllable_id' => $syllableModel->id,
                'position' => $position + 1,
            ]);
        }

        return $wordModel;
    }

    /**
     * Extrai palavras de um exercício (remove pontuação).
     *
     * @return array<string>
     */
    protected function extractWords(string $text): array
    {
        // Remover pontuação mas manter acentos/caracteres portugueses
        $text = preg_replace('/[.,;:!?¿¡()\[\]{}"\'«»\-–—…\/\\\\]/', ' ', $text);

        // Dividir por espaços e filtrar vazios
        $words = preg_split('/\s+/', trim($text), -1, PREG_SPLIT_NO_EMPTY);

        return $words ?: [];
    }
}
