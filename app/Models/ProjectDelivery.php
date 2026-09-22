<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProjectDelivery extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'project_id', 'client_id', 'created_by',
        'titulo', 'descripcion', 'fecha_entrega', 'tipo', 'estado',
        'items_entregados', 'observaciones',
        'firma_cliente', 'dni_firmante', 'cargo_firmante',
        'firmado_at', 'acta_path',
    ];

    protected $casts = [
        'fecha_entrega'  => 'date',
        'firmado_at'     => 'datetime',
        'items_entregados' => 'array',
    ];

    const ESTADOS = [
        'borrador'  => 'Borrador',
        'firmado'   => 'Firmado',
        'observado' => 'Observado',
    ];

    const TIPOS = [
        'parcial' => 'Entrega parcial',
        'final'   => 'Entrega final',
    ];

    public function estadoLabel(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function tipoLabel(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function estadoBadgeClass(): string
    {
        return match ($this->estado) {
            'firmado'   => 'bg-emerald-500/15 text-emerald-400',
            'observado' => 'bg-amber-500/15 text-amber-400',
            default     => 'bg-slate-500/15 text-slate-400',
        };
    }

    public function estaFirmado(): bool
    {
        return $this->estado === 'firmado' && !is_null($this->firmado_at);
    }

    // ── Relaciones ────────────────────────────────────────────────────
    public function project(): BelongsTo  { return $this->belongsTo(Project::class); }
    public function client(): BelongsTo   { return $this->belongsTo(Client::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }
}
