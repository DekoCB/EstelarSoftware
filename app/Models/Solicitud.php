<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Solicitud extends Model
{
    use SoftDeletes;

    protected $table = 'solicitudes';

    protected $fillable = [
        'user_id', 'tipo', 'titulo', 'descripcion', 'monto',
        'estado', 'respuesta_admin', 'atendido_por', 'atendido_at',
    ];

    protected $casts = [
        'monto'       => 'decimal:2',
        'atendido_at' => 'datetime',
    ];

    const ESTADOS = [
        'pendiente' => 'Pendiente',
        'aprobado'  => 'Aprobado',
        'rechazado' => 'Rechazado',
        'entregado' => 'Entregado',
    ];

    const ESTADO_COLORS = [
        'pendiente' => 'amber',
        'aprobado'  => 'sky',
        'rechazado' => 'red',
        'entregado' => 'emerald',
    ];

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function estadoColor(): string
    {
        return self::ESTADO_COLORS[$this->estado] ?? 'slate';
    }

    public function solicitante(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function atendioPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'atendido_por');
    }

    public function scopePendientes($q)  { return $q->where('estado', 'pendiente'); }
    public function scopeAprobadas($q)   { return $q->where('estado', 'aprobado'); }
    public function scopePropias($q, int $userId) { return $q->where('user_id', $userId); }
}
