<?php

namespace App\Imports;

use App\Http\Requests\Leads\LeadRules;
use App\Models\Lead;
use App\Support\LeadResponseOptions;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class LeadsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    public function model(array $row): ?Lead
    {
        // Sin teléfono no hay lead válido: descarta filas vacías o separadoras
        // (ej. las filas de fecha agrupadas de un export re-importado por error).
        if (trim((string) ($row['telefono'] ?? '')) === '') {
            return null;
        }

        $lead = new Lead([
            'nombre'               => $row['nombre'] ?? null,
            'empresa'              => $row['empresa'] ?? null,
            'lugar'                => $row['lugar'] ?? null,
            'telefono'             => $row['telefono'] ?? null,
            'email'                => $row['email'] ?? null,
            'created_by'           => auth()->id(),
            'respuesta_rapida'     => $this->clavePorLabel($row['respuesta'] ?? null),
            'respuesta_comentario' => $row['comentario'] ?? null,
            'respuesta_fecha'      => $this->parsearFecha($row['fecha_reunion'] ?? null),
            'respuesta_hora'       => $this->parsearHora($row['hora_reunion'] ?? null),
            'sistema_interes'      => $row['sistema_interes'] ?? null,
        ]);

        // Si el archivo trae "Fecha de registro" (ej. reimportar un export previo),
        // se respeta esa fecha en vez de usar el momento de la importación.
        $fechaRegistro = $this->parsearFechaHora($row['fecha_de_registro'] ?? null);

        if ($fechaRegistro) {
            $lead->created_at = $fechaRegistro;
            $lead->updated_at = $fechaRegistro;
        }

        return $lead;
    }

    public function rules(): array
    {
        return LeadRules::rules();
    }

    private function clavePorLabel(?string $valor): ?string
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        foreach (LeadResponseOptions::opciones() as $clave => $opcion) {
            if (mb_strtolower($opcion['label']) === mb_strtolower($valor)) {
                return $clave;
            }
        }

        return null;
    }

    private function parsearFecha(null|string|int $valor): ?string
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        try {
            return Carbon::createFromFormat('d/m/Y', $valor)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function parsearHora(?string $valor): ?string
    {
        $valor = trim((string) $valor);

        return $valor !== '' ? $valor : null;
    }

    private function parsearFechaHora(null|string|int $valor): ?Carbon
    {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return null;
        }

        foreach (['d/m/Y H:i', 'd/m/Y H:i:s', 'd/m/Y'] as $formato) {
            try {
                return Carbon::createFromFormat($formato, $valor);
            } catch (\Throwable) {
                continue;
            }
        }

        return null;
    }
}
