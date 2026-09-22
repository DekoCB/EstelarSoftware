<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Asistencia extends Model
{
    protected $fillable = [
        'user_id', 'tipo', 'fecha', 'hora',
        'latitud', 'longitud', 'distancia_metros',
        'radio_configurado', 'ip_address', 'justificada', 'observaciones',
    ];

    protected $casts = [
        'fecha'            => 'date',
        'distancia_metros' => 'decimal:2',
        'justificada'      => 'boolean',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeDelMes($q, int $anio, int $mes)
    {
        return $q->whereYear('fecha', $anio)->whereMonth('fecha', $mes);
    }

    public function scopeDeHoy($q)
    {
        return $q->whereDate('fecha', today());
    }

    public static function haversine(float $lat1, float $lon1, float $lat2, float $lon2): float
    {
        $R = 6371000;
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) ** 2
           + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * sin($dLon / 2) ** 2;
        return $R * 2 * atan2(sqrt($a), sqrt(1 - $a));
    }
}
