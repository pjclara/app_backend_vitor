<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfissionalStudent extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'profissional_student';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'profissional_id',
        'student_id'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string',
            'profissional_id' => 'string',
            'student_id' => 'string'
        ];
    }

    /**
     * Get the profissional that owns the ProfissionalStudent.
     */
    public function profissional(): BelongsTo
    {
        return $this->belongsTo(User::class, 'profissional_id')->where('role', 'profissional');
    }

    /**
     * Get the student that owns the ProfissionalStudent.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id')->where('role', 'aluno');
    }

}
