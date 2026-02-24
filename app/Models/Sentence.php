<?php

namespace App\Models;

use App\Models\SupabaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Sentence extends SupabaseModel
{
    /**
     * The table associated with the model.
     */
    protected $table = 'sentences';

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'sentence',
        'words_json',
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
