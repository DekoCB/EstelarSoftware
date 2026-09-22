<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventAttendee extends Model
{
    protected $fillable = [
        'event_id',
        'codigo',
        'qr_token',
        'nombres',
        'empresa',
        'tipo_documento',
        'numero_documento',
        'direccion',
        'email',
        'telefono',
        'estado',
        'checked_in_at',
        'checked_in_by',
        'created_by',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    const ESTADOS = ['registrado', 'asistio', 'cancelado'];

    const TIPOS_DOCUMENTO = ['DNI', 'RUC', 'CE', 'PASAPORTE'];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function checkedInBy(): BelongsTo { return $this->belongsTo(User::class, 'checked_in_by'); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function asistio(): bool
    {
        return $this->estado === 'asistio';
    }

    public function estadoBadgeClass(): string
    {
        return match ($this->estado) {
            'registrado' => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
            'asistio'    => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'cancelado'  => 'bg-slate-700 text-slate-400',
            default      => 'bg-slate-700 text-slate-400',
        };
    }

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            'registrado' => 'Registrado',
            'asistio'    => 'Asistió',
            'cancelado'  => 'Cancelado',
            default      => ucfirst($this->estado),
        };
    }
}
