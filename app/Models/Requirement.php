<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Requirement extends Model
{
    protected $fillable = [
        'project_id', 'phase_id', 'sprint_id', 'story_points',
        'title', 'description', 'type', 'priority', 'status',
        'assigned_to', 'created_by',
    ];

    const TYPES     = ['funcional', 'tecnico', 'negocio', 'ux_ui'];
    const PRIORITIES = ['critica', 'alta', 'media', 'baja'];
    const STATUSES  = ['pendiente', 'en_progreso', 'en_revision', 'completado', 'rechazado'];

    const TYPE_LABELS = [
        'funcional' => 'Funcional',
        'tecnico'   => 'Técnico',
        'negocio'   => 'Negocio',
        'ux_ui'     => 'UX/UI',
    ];

    const PRIORITY_COLORS = [
        'critica' => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
        'alta'    => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
        'media'   => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
        'baja'    => 'bg-slate-700 text-slate-400',
    ];

    const STATUS_COLORS = [
        'pendiente'    => 'bg-slate-700 text-slate-400',
        'en_progreso'  => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
        'en_revision'  => 'bg-violet-500/10 text-violet-400 ring-1 ring-violet-500/20',
        'completado'   => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
        'rechazado'    => 'bg-red-500/10 text-red-400 ring-1 ring-red-500/20',
    ];

    const STATUS_LABELS = [
        'pendiente'   => 'Pendiente',
        'en_progreso' => 'En progreso',
        'en_revision' => 'En revisión',
        'completado'  => 'Completado',
        'rechazado'   => 'Rechazado',
    ];

    public function project(): BelongsTo  { return $this->belongsTo(Project::class); }
    public function phase(): BelongsTo    { return $this->belongsTo(ProjectPhase::class, 'phase_id'); }
    public function sprint(): BelongsTo   { return $this->belongsTo(Sprint::class); }
    public function assignedTo(): BelongsTo { return $this->belongsTo(User::class, 'assigned_to'); }
    public function createdBy(): BelongsTo  { return $this->belongsTo(User::class, 'created_by'); }

    public function enSprint(): bool  { return $this->sprint_id !== null; }
    public function enBacklog(): bool { return $this->sprint_id === null; }

    public function priorityBadge(): string { return self::PRIORITY_COLORS[$this->priority] ?? ''; }
    public function statusBadge(): string   { return self::STATUS_COLORS[$this->status] ?? ''; }
    public function statusLabel(): string   { return self::STATUS_LABELS[$this->status] ?? $this->status; }
    public function typeLabel(): string     { return self::TYPE_LABELS[$this->type] ?? $this->type; }
}
