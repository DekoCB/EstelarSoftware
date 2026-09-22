<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DailyReport extends Model
{
    protected $fillable = [
        'project_id', 'sprint_id', 'user_id',
        'date', 'yesterday', 'today', 'blockers',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public function project(): BelongsTo { return $this->belongsTo(Project::class); }
    public function sprint(): BelongsTo  { return $this->belongsTo(Sprint::class); }
    public function user(): BelongsTo    { return $this->belongsTo(User::class); }

    public function tieneBlockers(): bool
    {
        return !empty($this->blockers);
    }
}
