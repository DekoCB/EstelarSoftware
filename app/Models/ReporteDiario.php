<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReporteDiario extends Model
{
    protected $table = 'reportes_diarios';

    protected $fillable = [
        'user_id',
        'area',
        'fecha',
        'proyectos_asignados',
        'sprint_iteracion',
        'modulo_componente',
        'horas_trabajadas',
        'tareas',
        'logros_destacados',
        'impedimentos',
        'plan_siguiente_dia',
        'archivo_adjunto',
    ];

    protected $casts = [
        'fecha'       => 'date',
        'tareas'      => 'array',
        'impedimentos'=> 'array',
        'horas_trabajadas' => 'decimal:1',
    ];

    const AREAS = [
        'desarrollo' => 'Desarrollo',
        'ventas'     => 'Ventas',
    ];

    const TIPOS_TAREA = ['Desarrollo', 'Diseño', 'Testing', 'Reunión', 'Documentación', 'Soporte', 'Otro'];

    const ESTADOS_TAREA = ['Completado', 'Incompleto', 'En Progreso'];

    const IMPACTOS = ['Alto', 'Medio', 'Bajo'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function areaLabel(): string
    {
        return self::AREAS[$this->area] ?? $this->area;
    }

    public function areaColor(): string
    {
        return match($this->area) {
            'desarrollo' => 'sky',
            'ventas'     => 'emerald',
            default      => 'slate',
        };
    }

    public function tareasCompletadas(): int
    {
        return collect($this->tareas)->where('estado', 'Completado')->count();
    }

    public function tareasEnProgreso(): int
    {
        return collect($this->tareas)->where('estado', 'En Progreso')->count();
    }

    public function tareasIncompletas(): int
    {
        return collect($this->tareas)->where('estado', 'Incompleto')->count();
    }

    public function tieneImpedimentos(): bool
    {
        return !empty($this->impedimentos);
    }
}
