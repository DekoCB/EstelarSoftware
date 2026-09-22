<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Project extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'client_id', 'quote_id', 'name', 'description',
        'checklist', 'notas_reunion',
        'status', 'progress', 'start_date', 'end_date',
        'responsible_user_id', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
        'checklist'  => 'array',
        'deleted_at' => 'datetime',
    ];

    const ESTADOS = [
        'planificado', 'en_curso', 'pausado',
        'en_revision', 'entregado', 'cancelado',
    ];

    const PARTES_DEFAULT = [
        'Levantamiento de requerimientos',
        'Diseño de base de datos',
        'Diseño de interfaces (mockups)',
        'Desarrollo backend',
        'Desarrollo frontend',
        'Pruebas funcionales',
        'Revisión con el cliente',
        'Despliegue en producción',
        'Capacitación al cliente',
        'Documentación técnica',
    ];

    // ── Relaciones ───────────────────────────────────────────────────

    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_user_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function phases(): HasMany
    {
        return $this->hasMany(ProjectPhase::class)->orderBy('order');
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class);
    }

    public function sprints(): HasMany
    {
        return $this->hasMany(Sprint::class)->orderByDesc('created_at');
    }

    // ── Scopes ───────────────────────────────────────────────────────

    public function scopeSearch($query, string $term)
    {
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhereHas('client', fn($c) => $c->where('razon_social', 'like', "%{$term}%"));
        });
    }

    public function scopeAsignadoA($query, int $userId)
    {
        return $query->where('responsible_user_id', $userId);
    }

    // ── Helpers ──────────────────────────────────────────────────────

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'en_curso'    => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
            'entregado'   => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'planificado' => 'bg-slate-700 text-slate-400',
            'pausado'     => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
            'en_revision' => 'bg-violet-500/10 text-violet-400 ring-1 ring-violet-500/20',
            'cancelado'   => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
            default       => 'bg-slate-700 text-slate-400',
        };
    }

    public function statusLabel(): string
    {
        return match($this->status) {
            'planificado' => 'Planificado',
            'en_curso'    => 'En curso',
            'pausado'     => 'Pausado',
            'en_revision' => 'En revisión',
            'entregado'   => 'Entregado',
            'cancelado'   => 'Cancelado',
            default       => $this->status,
        };
    }

    public function checklistCompletados(): int
    {
        return collect($this->checklist ?? [])->where('completado', true)->count();
    }

    public function checklistTotal(): int
    {
        return count($this->checklist ?? []);
    }

    public function recalcularProgreso(): void
    {
        $fases = $this->phases;
        if ($fases->isEmpty()) return;
        $this->progress = (int) round($fases->avg('progress'));
        $this->saveQuietly();
    }
}
