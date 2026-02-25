<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pedido extends Model
{
    use HasFactory;

    protected $table = 'pedidos';

    // UUID
    protected $keyType = 'string';
    public $incrementing = false;

    // Não usamos created_at / updated_at padrão
    public $timestamps = false;

    protected $fillable = [
        'id',
        'solicitante_id',
        'solicitante_tipo',
        'destinatario_id',
        'destinatario_tipo',
        'status',
        'criado_em',
        'respondido_em',
    ];

    protected $casts = [
        'criado_em'     => 'datetime',
        'respondido_em' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | RELACIONAMENTOS
    |--------------------------------------------------------------------------
    */

    public function solicitante()
    {
        return $this->belongsTo(User::class, 'solicitante_id');
    }

    public function destinatario()
    {
        return $this->belongsTo(User::class, 'destinatario_id');
    }

    /*
    |--------------------------------------------------------------------------
    | SCOPES ÚTEIS
    |--------------------------------------------------------------------------
    */

    public function scopePendentes($query)
    {
        return $query->where('status', 'pendente');
    }

    public function scopeEntre($query, $userA, $userB)
    {
        return $query->where(function ($q) use ($userA, $userB) {
            $q->where('solicitante_id', $userA)
              ->where('destinatario_id', $userB);
        })->orWhere(function ($q) use ($userA, $userB) {
            $q->where('solicitante_id', $userB)
              ->where('destinatario_id', $userA);
        });
    }
}