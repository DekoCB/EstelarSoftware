<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'tipo_documento',
        'numero_documento',
        'razon_social',
        'nombre_comercial',
        'email',
        'telefono',
        'direccion',
        'ubigeo',
        'estado',
        'created_by',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];

    // ── Constantes ──────────────────────────────────────────────────

    const TIPOS_DOCUMENTO = ['RUC', 'DNI', 'CE', 'PASAPORTE'];
    const ESTADOS         = ['prospecto', 'activo', 'inactivo', 'bloqueado'];

    // ── Relaciones ───────────────────────────────────────────────────

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    public function quotes(): HasMany
    {
        return $this->hasMany(Quote::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function cashMovements(): HasMany
    {
        return $this->hasMany(CashMovement::class);
    }

    public function deliveries(): HasMany
    {
        return $this->hasMany(ProjectDelivery::class);
    }

    public function eventLeads(): HasMany
    {
        return $this->hasMany(EventLead::class);
    }

    // ── KPI Helpers ──────────────────────────────────────────────────

    public function totalCotizado(): float
    {
        return (float) $this->quotes->whereNotIn('status', ['rechazado'])->sum('total');
    }

    public function totalFacturado(): float
    {
        return (float) $this->invoices->whereNotIn('estado_sunat', ['anulado', 'borrador'])->sum('total');
    }

    public function totalCobrado(): float
    {
        return (float) $this->cashMovements->where('tipo', 'ingreso')->sum('monto');
    }

    public function saldoPendiente(): float
    {
        return max(0, $this->totalFacturado() - $this->totalCobrado());
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('razon_social', 'like', "%{$term}%")
              ->orWhere('numero_documento', 'like', "%{$term}%")
              ->orWhere('email', 'like', "%{$term}%")
              ->orWhere('nombre_comercial', 'like', "%{$term}%");
        });
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function estadoBadgeClass(): string
    {
        return match($this->estado) {
            'activo'    => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'prospecto' => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
            'inactivo'  => 'bg-slate-700 text-slate-400',
            'bloqueado' => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            default     => 'bg-slate-700 text-slate-400',
        };
    }

    public function esRUC(): bool
    {
        return $this->tipo_documento === 'RUC';
    }
}
