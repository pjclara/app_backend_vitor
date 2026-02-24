<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

abstract class SupabaseModel extends Model
{
    use HasFactory, HasUuids;

    protected $keyType = 'string';
    public $incrementing = false;

    /**
     * Indicates if the model should be timestamped.
     */
    public $timestamps = true;

    /**
     * The storage format of the model's date columns.
     */
    protected $dateFormat = 'Y-m-d H:i:s.uO';

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
        ];
    }

    /**
     * Prepare a date for array / JSON serialization.
     */
    protected function serializeDate(\DateTimeInterface $date): string
    {
        return $date->format('Y-m-d H:i:s.uO');
    }

    /**
     * Convert a DateTime to a storable string for Supabase.
     */
    public function fromDateTime($value): ?string
    {
        if (is_null($value)) {
            return $value;
        }

        return Carbon::parse($value)->toISOString();
    }

    /**
     * Handle dynamic attribute access for Supabase compatibility
     */
    public function getAttribute($key)
    {
        $value = parent::getAttribute($key);

        // Handle UUID attributes
        if ($key === $this->getKeyName() && is_string($value)) {
            return $value;
        }

        return $value;
    }

    /**
     * Scope for RLS (Row Level Security) bypass when needed
     */
    public function scopeBypassRLS($query)
    {
        // This would require service role permissions
        // Use carefully and only when necessary
        return $query;
    }

    /**
     * Get optimized query builder with proper indexing hints
     */
    public function scopeOptimized($query)
    {
        return $query->select($this->getTable() . '.*');
    }
}