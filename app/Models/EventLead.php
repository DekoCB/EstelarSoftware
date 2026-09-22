<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EventLead extends Model
{
    protected $fillable = [
        'event_id',
        'tipo_documento',
        'numero_documento',
        'nombres',
        'empresa',
        'rubro',
        'email',
        'telefono',
        'direccion',
        'latitud',
        'longitud',
        'precision_metros',
        'interes',
        'estado',
        'client_id',
        'created_by',
    ];

    protected $casts = [
        'latitud'          => 'decimal:7',
        'longitud'         => 'decimal:7',
        'precision_metros' => 'decimal:2',
    ];

    const TIPOS_DOCUMENTO = ['DNI', 'RUC', 'CE', 'PASAPORTE'];
    const ESTADOS         = ['nuevo', 'contactado', 'convertido', 'descartado'];

    const RUBROS = [
        'Comercio',
        'Servicios',
        'Tecnología / Software',
        'Salud',
        'Educación',
        'Alimentos y Bebidas',
        'Construcción',
        'Manufactura / Industria',
        'Turismo y Hotelería',
        'Agropecuario',
        'Finanzas y Seguros',
        'Transporte y Logística',
        'Textil y Confecciones',
    ];

    public function event(): BelongsTo { return $this->belongsTo(Event::class); }
    public function client(): BelongsTo { return $this->belongsTo(Client::class); }
    public function createdBy(): BelongsTo { return $this->belongsTo(User::class, 'created_by'); }

    public function tieneUbicacion(): bool
    {
        return $this->latitud !== null && $this->longitud !== null;
    }

    public function convertido(): bool
    {
        return $this->client_id !== null;
    }

    public function estadoBadgeClass(): string
    {
        return match ($this->estado) {
            'nuevo'       => 'bg-sky-500/10 text-sky-400 ring-1 ring-sky-500/20',
            'contactado'  => 'bg-amber-500/10 text-amber-400 ring-1 ring-amber-500/20',
            'convertido'  => 'bg-emerald-500/10 text-emerald-400 ring-1 ring-emerald-500/20',
            'descartado'  => 'bg-slate-700 text-slate-400',
            default       => 'bg-slate-700 text-slate-400',
        };
    }

    public function estadoLabel(): string
    {
        return match ($this->estado) {
            'nuevo'      => 'Nuevo',
            'contactado' => 'Contactado',
            'convertido' => 'Convertido',
            'descartado' => 'Descartado',
            default      => ucfirst($this->estado),
        };
    }
}
