<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sprint extends Model
{
    protected $fillable = [
        'project_id', 'name', 'goal',
        'start_date', 'end_date', 'status', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    const STATUSES = ['planificacion', 'activo', 'completado', 'cancelado'];

    const FIBONACCI = [1, 2, 3, 5, 8, 13, 21];

    // ── Relaciones ───────────────────────────────────────────────────

    public function project(): BelongsTo     { return $this->belongsTo(Project::class); }
    public function createdBy(): BelongsTo   { return $this->belongsTo(User::class, 'created_by'); }

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    public function dailyReports(): HasMany
    {
        return $this->hasMany(DailyReport::class);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'activo'       => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
            'completado'   => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'cancelado'    => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            default        => 'bg-slate-700 text-slate-400',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'planificacion' => 'Planificación',
            'activo'        => 'Activo',
            'completado'    => 'Completado',
            'cancelado'     => 'Cancelado',
            default         => $this->status,
        };
    }

    public function porcentajeCompletado(): int
    {
        $total = $this->requirements()->count();
        if ($total === 0) return 0;
        $done = $this->requirements()->where('status', 'completado')->count();
        return (int) round(($done / $total) * 100);
    }

    public function velocityTotal(): int
    {
        return (int) $this->requirements()->whereNotNull('story_points')->sum('story_points');
    }

    public function velocityCompletado(): int
    {
        return (int) $this->requirements()
            ->where('status', 'completado')
            ->whereNotNull('story_points')
            ->sum('story_points');
    }
}
