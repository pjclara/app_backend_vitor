<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Word extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'words';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'word',
        'syllables',
        'audio_url',
        'difficulty'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'difficulty' => 'integer'
        ];
    }

    /**
     * Sílabas desta palavra (junction).
     */
    public function wordSyllables(): HasMany
    {
        return $this->hasMany(WordSyllable::class, 'word_id')->orderBy('position');
    }

    /**
     * Sílabas desta palavra (many-to-many).
     */
    public function syllables(): BelongsToMany
    {
        return $this->belongsToMany(Syllable::class, 'word_syllables', 'word_id', 'syllable_id')
            ->withPivot('position')
            ->orderByPivot('position');
    }

    /**
     * Exercícios que contêm esta palavra.
     */
    public function exercises(): BelongsToMany
    {
        return $this->belongsToMany(Exercise::class, 'exercise_words', 'word_id', 'exercise_id')
            ->withPivot('word_order');
    }
}
