<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Syllable extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'syllables';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'syllable',
        'audio_url',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
        ];
    }

    /**
     * Palavras que contêm esta sílaba.
     */
    public function words(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'word_syllables', 'syllable_id', 'word_id')
            ->withPivot('position')
            ->orderByPivot('position');
    }
}
