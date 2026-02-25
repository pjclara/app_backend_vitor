<?php

namespace App\Models;

use App\Enums\DictationDifficulty;
use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exercise extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'exercises';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sentence',
        'words_json',
        'difficulty',
        'content',
        'number'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'difficulty' => DictationDifficulty::class,
            'words_json' => 'array',
        ];
    }

    /**
     * Palavras deste exercício (pivot).
     */
    public function exerciseWords(): HasMany
    {
        return $this->hasMany(ExerciseWord::class, 'exercise_id')->orderBy('word_order');
    }

    /**
     * Palavras associadas via many-to-many.
     */
    public function words(): BelongsToMany
    {
        return $this->belongsToMany(Word::class, 'exercise_words', 'exercise_id', 'word_id')
            ->withPivot('word_order')
            ->orderByPivot('word_order');
    }
}
