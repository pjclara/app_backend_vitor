<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExerciseWord extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'exercise_words';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'exercise_id',
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
            'exercise_id' => 'string',
            'word_id' => 'string',
            'word_order' => 'integer'
        ];
    }

    /**
     * Get the exercise that owns the ExerciseWord.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

    /**
     * Get the word that owns the ExerciseWord.
     */
    public function word(): BelongsTo
    {
        return $this->belongsTo(Word::class, 'word_id');
    }
}
