<?php

namespace App\Services;

use App\Models\Sentence;
use App\Models\SentenceWord;
use App\Models\Word;
use App\Models\WordSyllable;
use Illuminate\Support\Facades\DB;

class SentenceProcessorService
{
    protected PortugueseSyllableSplitter $splitter;

    public function __construct()
    {
        $this->splitter = new PortugueseSyllableSplitter();
    }

    /**
     * Processa uma frase: divide em palavras e sílabas, guarda tudo nas tabelas.
     *
     * @param Sentence $sentence
     * @return void
     */
    public function process(Sentence $sentence): void
    {
        $text = $sentence->sentence;

        // Extrair palavras (remover pontuação, manter apenas palavras)
        $rawWords = $this->extractWords($text);

        if (empty($rawWords)) {
            return;
        }

        DB::transaction(function () use ($sentence, $rawWords) {
            // Limpar relações existentes (para caso de edição)
            SentenceWord::where('sentence_id', $sentence->id)->delete();

            $wordsData = [];

            foreach ($rawWords as $order => $rawWord) {
                $normalizedWord = mb_strtolower(trim($rawWord));

                if (empty($normalizedWord)) {
                    continue;
                }

                // Procurar ou criar a palavra na tabela words
                $word = Word::where('word', $normalizedWord)->first();

                if (!$word) {
                    $word = $this->createWordWithSyllables($normalizedWord, $sentence->difficulty ?? 1);
                }

                // Criar a relação sentence_words
                SentenceWord::create([
                    'sentence_id' => $sentence->id,
                    'word_id' => $word->id,
                    'word_order' => $order + 1,
                ]);

                $syllables = $word->syllables ?? implode('-', $this->splitter->split($normalizedWord));

                $wordsData[] = [
                    'word' => $normalizedWord,
                    'order' => $order + 1,
                    'syllables' => $syllables,
                    'word_id' => $word->id,
                ];
            }

            // Atualizar o campo words_json da frase
            $sentence->update([
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

        // Criar as sílabas na tabela word_syllables
        foreach ($syllables as $position => $syllable) {
            WordSyllable::create([
                'word_id' => $wordModel->id,
                'syllable' => $syllable,
                'position' => $position + 1,
            ]);
        }

        return $wordModel;
    }

    /**
     * Extrai palavras de uma frase (remove pontuação).
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
