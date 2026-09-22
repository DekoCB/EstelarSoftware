<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadMeeting extends Model
{
    protected $table = 'lead_meetings';

    public const ORIGEN_MANUAL = 'manual';
    public const ORIGEN_RESPUESTA_RAPIDA = 'respuesta_rapida';

    public const RECORDATORIO_GRACE_MINUTES = 15;

    public const MOMENTO_ANTES    = 'antes';
    public const MOMENTO_HORA     = 'hora';
    public const MOMENTO_DESPUES  = 'despues';

    protected $fillable = [
        'lead_id', 'fecha_hora', 'nota', 'origen', 'created_by',
        'recordatorio_minutos', 'recordatorio_en', 'recordatorio_enviado_en',
        'aviso_hora_enviado_en', 'aviso_despues_en', 'aviso_despues_enviado_en',
    ];

    protected $casts = [
        'fecha_hora'               => 'datetime',
        'recordatorio_en'          => 'datetime',
        'recordatorio_enviado_en'  => 'datetime',
        'aviso_hora_enviado_en'    => 'datetime',
        'aviso_despues_en'         => 'datetime',
        'aviso_despues_enviado_en' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Los 3 avisos (antes / a la hora / despues) son una configuracion global
        // (EmpresaConfig), no un valor por reunion: se recalculan aqui cada vez que
        // cambia la fecha_hora, y de forma masiva desde ConfigController::update
        // cuando el admin cambia el valor global.
        static::saving(function (LeadMeeting $meeting) {
            if ($meeting->isDirty('fecha_hora')) {
                $config = EmpresaConfig::config();

                $meeting->recordatorio_minutos = $config->recordatorioReunionMinutos();
                $meeting->recordatorio_en = $meeting->fecha_hora
                    ? $meeting->fecha_hora->copy()->subMinutes($meeting->recordatorio_minutos)
                    : null;
                $meeting->recordatorio_enviado_en = null;

                $meeting->aviso_hora_enviado_en = null;

                $meeting->aviso_despues_en = $meeting->fecha_hora
                    ? $meeting->fecha_hora->copy()->addMinutes($config->alertaDespuesMinutos())
                    : null;
                $meeting->aviso_despues_enviado_en = null;
            }
        });
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha_hora', '>=', now())->orderBy('fecha_hora');
    }

    public function scopeEnRango($query, $inicio, $fin)
    {
        return $query->whereBetween('fecha_hora', [$inicio, $fin]);
    }

    public function scopeProximas24h($query)
    {
        return $query->whereBetween('fecha_hora', [now(), now()->addDay()]);
    }

    public function scopeHoy($query)
    {
        return $query->whereDate('fecha_hora', now()->toDateString());
    }

    public function scopeRecordatoriosPendientes($query, int $userId, int $graceMinutes = self::RECORDATORIO_GRACE_MINUTES)
    {
        return $query->where('created_by', $userId)
            ->whereNotNull('recordatorio_en')
            ->whereNull('recordatorio_enviado_en')
            ->whereBetween('recordatorio_en', [now()->subMinutes($graceMinutes), now()])
            ->orderBy('recordatorio_en');
    }

    public function scopeAvisosHoraPendientes($query, int $userId, int $graceMinutes = self::RECORDATORIO_GRACE_MINUTES)
    {
        return $query->where('created_by', $userId)
            ->whereNull('aviso_hora_enviado_en')
            ->whereBetween('fecha_hora', [now()->subMinutes($graceMinutes), now()])
            ->orderBy('fecha_hora');
    }

    public function scopeAvisosDespuesPendientes($query, int $userId, int $graceMinutes = self::RECORDATORIO_GRACE_MINUTES)
    {
        return $query->where('created_by', $userId)
            ->whereNotNull('aviso_despues_en')
            ->whereNull('aviso_despues_enviado_en')
            ->whereBetween('aviso_despues_en', [now()->subMinutes($graceMinutes), now()])
            ->orderBy('aviso_despues_en');
    }

    /**
     * Marca uno de los 3 avisos como visto/reconocido por el usuario (botón
     * "Silenciar" o "Ver lead" en el modal de alarma). A propósito NO se marca
     * solo por leerlo: si se marcara en cada poll, la primera pestaña abierta del
     * usuario "ganaría" el aviso y las demás (incluida la que el usuario esté
     * realmente mirando) nunca lo recibirían. Así, el aviso sigue "pendiente" y
     * suena en todas las pestañas abiertas hasta que el usuario lo reconoce
     * explícitamente en alguna de ellas.
     */
    public function marcarAvisoVisto(string $momento): void
    {
        $columna = match ($momento) {
            self::MOMENTO_ANTES   => 'recordatorio_enviado_en',
            self::MOMENTO_HORA    => 'aviso_hora_enviado_en',
            self::MOMENTO_DESPUES => 'aviso_despues_enviado_en',
            default => null,
        };

        if ($columna && is_null($this->$columna)) {
            $this->update([$columna => now()]);
        }
    }
}
