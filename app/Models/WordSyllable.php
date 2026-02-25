<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WordSyllable extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'word_syllables';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'word_id',
        'syllable_id',
        'position',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'word_id' => 'string',
            'syllable_id' => 'string',
            'position' => 'integer'
        ];
    }

    /**
     * Get the word that owns the WordSyllable.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }

    /**
     * Get the syllable that owns the WordSyllable.
     */
    public function syllable(): BelongsTo
    {
        return $this->belongsTo(Syllable::class, 'syllable_id');
    }
}
