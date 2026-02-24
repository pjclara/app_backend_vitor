<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DictationMetric extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'dictation_metrics';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'student_id',
        'exercise_id',
        'difficulty',
        'correct_count',
        'error_count',
        'missing_count',
        'extra_count',
        'accuracy_percent',
        'letter_omission_count',
        'letter_insertion_count',
        'letter_substitution_count',
        'transposition_count',
        'split_join_count',
        'punctuation_error_count',
        'capitalization_error_count',
        'error_words',
        'resolution'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'student_id' => 'string',
            'exercise_id' => 'string',
            'correct_count' => 'integer',
            'error_count' => 'integer',
            'missing_count' => 'integer',
            'extra_count' => 'integer',
            'accuracy_percent' => 'decimal:2',
            'letter_omission_count' => 'integer',
            'letter_insertion_count' => 'integer',
            'letter_substitution_count' => 'integer',
            'transposition_count' => 'decimal:2',
            'split_join_count' => 'integer',
            'punctuation_error_count' => 'integer',
            'capitalization_error_count' => 'integer',
            'error_words' => 'array'
        ];
    }

    /**
     * Get the student that owns the DictationMetric.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    /**
     * Get the exercise that owns the DictationMetric.
     */
    public function exercise(): BelongsTo
    {
        return $this->belongsTo(Exercise::class, 'exercise_id');
    }

}
