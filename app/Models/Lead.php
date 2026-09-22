<?php

namespace App\Models;

use App\Support\LeadResponseOptions;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Lead extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'nombre', 'empresa', 'lugar', 'telefono', 'email', 'created_by',
        'respuesta_rapida', 'respuesta_comentario', 'respuesta_fecha', 'respuesta_hora',
        'sistema_interes', 'quiere_cotizacion', 'ruc_dni', 'reunion_realizada', 'cotizacion_enviada',
        'mensaje', 'origen',
    ];

    public const ORIGEN_MANUAL = 'manual';
    public const ORIGEN_WEB    = 'web';

    protected $casts = [
        'respuesta_fecha'     => 'date',
        'quiere_cotizacion'   => 'boolean',
        'reunion_realizada'   => 'boolean',
        'cotizacion_enviada'  => 'boolean',
    ];

    public const RESPUESTAS_CON_COTIZACION = ['reunion_pendiente', 'volver_a_llamar', 'escribir_whatsapp'];

    protected function nombre(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => trim((string) $value) !== '' ? mb_strtoupper(trim($value), 'UTF-8') : '-',
        );
    }

    protected function lugar(): Attribute
    {
        return Attribute::make(
            set: fn (?string $value) => trim((string) $value) !== '' ? trim($value) : '-',
        );
    }

    protected static function booted(): void
    {
        static::saved(fn (Lead $lead) => $lead->sincronizarReunionAutomatica());
    }

    /**
     * Refleja en el calendario únicamente las respuestas rápidas que implican
     * reunión o llamada (las que requieren fecha y hora). El resto de respuestas
     * no agenda nada. No toca reuniones creadas manualmente (origen "manual").
     */
    public function sincronizarReunionAutomatica(): void
    {
        $esReunionOLlamada = LeadResponseOptions::requiere($this->respuesta_rapida, 'fecha')
            && LeadResponseOptions::requiere($this->respuesta_rapida, 'hora');

        if ($esReunionOLlamada && $this->respuesta_fecha && $this->respuesta_hora) {
            $fechaHora = $this->respuesta_fecha->format('Y-m-d') . ' ' . substr($this->respuesta_hora, 0, 5);

            $this->meetings()->updateOrCreate(
                ['origen' => LeadMeeting::ORIGEN_RESPUESTA_RAPIDA],
                [
                    'fecha_hora' => $fechaHora,
                    'nota'       => Str::limit(trim($this->respuestaLabel() . ($this->respuesta_comentario ? ' — ' . $this->respuesta_comentario : '')), 250),
                    'created_by' => $this->created_by,
                ]
            );
        } else {
            $this->meetings()->where('origen', LeadMeeting::ORIGEN_RESPUESTA_RAPIDA)->delete();
        }
    }

    public function respuestaLabel(): ?string
    {
        return LeadResponseOptions::label($this->respuesta_rapida);
    }

    public function respuestaColor(): string
    {
        return LeadResponseOptions::color($this->respuesta_rapida);
    }

    public function meetings(): HasMany
    {
        return $this->hasMany(LeadMeeting::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function proximaReunion(): ?LeadMeeting
    {
        return $this->meetings()->proximas()->first();
    }

    public function scopeSearch($query, string $term)
    {
        // Los dígitos del término buscado se comparan contra el teléfono sin
        // espacios ni "+", para que "960837532" o "+51 960 837 532" encuentren
        // el mismo lead sin importar cómo esté formateado lo guardado.
        $digitos = preg_replace('/\D/', '', $term);

        return $query->where(function ($q) use ($term, $digitos) {
            $q->where('nombre', 'like', "%{$term}%")
                ->orWhere('empresa', 'like', "%{$term}%")
                ->orWhere('telefono', 'like', "%{$term}%")
                ->orWhere('email', 'like', "%{$term}%");

            if ($digitos !== '') {
                $q->orWhereRaw(
                    "REPLACE(REPLACE(telefono, ' ', ''), '+', '') LIKE ?",
                    ["%{$digitos}%"]
                );
            }
        });
    }
}
