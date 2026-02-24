<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sentence extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'sentences';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sentence',
        'words_json',
        'difficulty'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'difficulty' => 'integer',
            'words_json' => 'array',
        ];
    }

    /**
     * Palavras desta frase (pivot).
     */
    public function sentenceWords(): HasMany
    {
        return $this->hasMany(SentenceWord::class, 'sentence_id')->orderBy('word_order');
    }

    /**
     * Palavras associadas via many-to-many.
     */
    public function words(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'sentence_words', 'sentence_id', 'word_id')
            ->withPivot('word_order')
            ->orderByPivot('word_order');
    }
}
