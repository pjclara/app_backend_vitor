<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SentenceWord extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'sentence_words';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sentence_id',
        'word_id',
        'word_order'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'sentence_id' => 'string',
            'word_id' => 'string',
            'word_order' => 'integer'
        ];
    }

    /**
     * Get the sentence that owns the SentenceWord.
     */
    public function sentence(): BelongsTo
    {
        return $this->belongsTo(Sentence::class, 'sentence_id');
    }

    /**
     * Get the word that owns the SentenceWord.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

}
