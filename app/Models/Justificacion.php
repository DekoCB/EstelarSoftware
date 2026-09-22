<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justificacion extends Model
{
    protected $table = 'justificaciones';

    protected $fillable = [
        'user_id', 'tipo', 'fecha', 'hora_justificada', 'motivo',
        'estado', 'respuesta_admin', 'atendido_por', 'atendido_at', 'asistencia_id',
    ];

    protected $casts = [
        'fecha'       => 'date',
        'atendido_at' => 'datetime',
    ];

    const ESTADO_COLORS = [
        'pendiente' => 'amber',
        'aprobado'  => 'emerald',
        'rechazado' => 'red',
    ];

    public function estadoColor(): string
    {
        return self::ESTADO_COLORS[$this->estado] ?? 'slate';
    }

    public function empleado(): BelongsTo { return $this->belongsTo(User::class, 'user_id'); }
    public function atendioPor(): BelongsTo { return $this->belongsTo(User::class, 'atendido_por'); }
    public function asistencia(): BelongsTo { return $this->belongsTo(Asistencia::class); }
}
