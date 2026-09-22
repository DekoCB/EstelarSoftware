<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProjectPhase extends Model
{
    protected $fillable = [
        'project_id', 'name', 'description',
        'order', 'progress', 'status',
    ];

    const ESTADOS = ['pendiente', 'en_curso', 'completada', 'cancelada'];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public function requirements(): HasMany
    {
        return $this->hasMany(Requirement::class, 'phase_id');
    }

    public function statusBadgeClass(): string
    {
        return match($this->status) {
            'en_curso'   => 'bg-sky-500/10 text-sky-400',
            'completada' => 'bg-emerald-500/10 text-emerald-400',
            'cancelada'  => 'bg-red-500/10 text-red-400',
            default      => 'bg-slate-700 text-slate-400',
        };
    }
}
