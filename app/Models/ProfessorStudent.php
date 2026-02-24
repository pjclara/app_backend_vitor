<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfessorStudent extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'professor_student';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'professor_id',
        'student_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'professor_id' => 'string',
            'student_id' => 'string'
        ];
    }

    /**
     * Get the professor that owns the ProfessorStudent.
     */
    public function professor(): BelongsTo
    {
        return $this->belongsTo(Professor::class, 'professor_id');
    }

    /**
     * Get the student that owns the ProfessorStudent.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

}
