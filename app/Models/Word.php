<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

}
