<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class EmpresaConfig extends Model
{
    protected $table = 'empresa_config';

    protected $fillable = [
        'ruc', 'razon_social', 'nombre_comercial',
        'direccion', 'ubigeo', 'email', 'telefono', 'web',
        'logo_sidebar', 'logo_login', 'logo_documentos',
        'igv_porcentaje', 'moneda', 'serie_boleta', 'serie_factura',
        'sunat_modo', 'nubefact_url', 'nubefact_token',
        'certificado_pfx_path', 'certificado_pfx_clave',
        'asistencia_latitud', 'asistencia_longitud', 'asistencia_radio_metros',
        'recordatorio_reunion_minutos', 'alerta_despues_minutos',
    ];

    protected $casts = [
        'igv_porcentaje'          => 'decimal:2',
        'certificado_pfx_clave'   => 'encrypted',
        'asistencia_latitud'      => 'decimal:7',
        'asistencia_longitud'     => 'decimal:7',
        'asistencia_radio_metros' => 'integer',
        'recordatorio_reunion_minutos' => 'integer',
        'alerta_despues_minutos'       => 'integer',
    ];

    // ── Singleton ────────────────────────────────────────────────────

    public static function config(): static
    {
        return static::firstOrNew(['id' => 1]);
    }

    // ── Accessors de URL para logos ──────────────────────────────────

    public function logoSidebarUrl(): ?string
    {
        return $this->logo_sidebar
            ? Storage::disk('public')->url($this->logo_sidebar)
            : null;
    }

    public function logoLoginUrl(): ?string
    {
        return $this->logo_login
            ? Storage::disk('public')->url($this->logo_login)
            : null;
    }

    public function logoDocumentosUrl(): ?string
    {
        return $this->logo_documentos
            ? Storage::disk('public')->url($this->logo_documentos)
            : null;
    }

    // ── Helper IGV ───────────────────────────────────────────────────

    public function igvDecimal(): float
    {
        return (float) $this->igv_porcentaje / 100;
    }

    // ── Asistencia ───────────────────────────────────────────────────

    public function tieneGeoConfigurado(): bool
    {
        return !is_null($this->asistencia_latitud) && !is_null($this->asistencia_longitud);
    }

    public function radioMetros(): int
    {
        return $this->asistencia_radio_metros ?? 15;
    }

    // ── Reuniones ────────────────────────────────────────────────────

    public function recordatorioReunionMinutos(): int
    {
        return $this->recordatorio_reunion_minutos ?? 15;
    }

    public function alertaDespuesMinutos(): int
    {
        return $this->alerta_despues_minutos ?? 5;
    }
}
