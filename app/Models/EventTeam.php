<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Equipo inscrito a un evento tipo torneo (hoy: Go Left!!). Cada
 * integrante sigue siendo un EventAttendee con su propio codigo/QR
 * -el check-in es por persona-; el equipo solo los agrupa y guarda el
 * nombre, el logo (opcional) y el pago de la inscripcion.
 *
 * La inscripcion no queda concretada hasta que el pago esta confirmado:
 * pendiente -> en_revision (subio comprobante) -> pagado | rechazado.
 * El comprobante va al disco privado (`local`), nunca a `public`.
 */
class EventTeam extends Model
{
    const MAX_INTEGRANTES = 4;

    const ESTADOS_PAGO = ['pendiente', 'en_revision', 'pagado', 'rechazado'];

    protected $fillable = [
        'event_id',
        'token',
        'nombre',
        'logo',
        'estado_pago',
        'comprobante_pago',
        'comprobante_subido_at',
        'pago_revisado_at',
        'pago_revisado_by',
    ];

    protected $attributes = [
        'estado_pago' => 'pendiente',
    ];

    protected $casts = [
        'comprobante_subido_at' => 'datetime',
        'pago_revisado_at'      => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (EventTeam $equipo) {
            $equipo->token ??= (string) Str::uuid();
        });
    }

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function integrantes(): HasMany { return $this->hasMany(EventAttendee::class); }
    public function pagoRevisadoBy(): BelongsTo { return $this->belongsTo(User::class, 'pago_revisado_by'); }

    public function logoUrl(): ?string
    {
        return $this->logo
            ? Storage::disk('public')->url($this->logo)
            : null;
    }

    public function pagado(): bool
    {
        return $this->estado_pago === 'pagado';
    }

    /** El equipo puede (re)subir comprobante mientras no este confirmado. */
    public function puedeSubirComprobante(): bool
    {
        return $this->estado_pago !== 'pagado';
    }

    public function estadoPagoLabel(): string
    {
        return match ($this->estado_pago) {
            'pendiente'   => 'Pago pendiente',
            'en_revision' => 'Pago en revisión',
            'pagado'      => 'Pagado',
            'rechazado'   => 'Pago rechazado',
            default       => ucfirst((string) $this->estado_pago),
        };
    }

    public function estadoPagoBadgeClass(): string
    {
        return match ($this->estado_pago) {
            'pendiente'   => 'bg-slate-700 text-slate-300',
            'en_revision' => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
            'pagado'      => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'rechazado'   => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            default       => 'bg-slate-700 text-slate-400',
        };
    }
}
