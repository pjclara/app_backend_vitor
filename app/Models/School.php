<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class School extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'schools';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'director_name'
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'id' => 'string'
        ];
    }

}
