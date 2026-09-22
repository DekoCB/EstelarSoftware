<?php

namespace App\Imports;

use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EventAttendeesImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure
{
    use SkipsFailures;

    private int $siguienteNumero;

    /** Documentos ya vistos en este mismo archivo, para detectar duplicados dentro del propio Excel. */
    private array $documentosVistos = [];

    public function __construct(private readonly Event $evento)
    {
        $bloqueado = Event::lockForUpdate()->find($evento->id);
        $this->siguienteNumero = $bloqueado->asistentes()->count() + 1;
    }

    /**
     * PhpSpreadsheet lee celdas con solo dígitos (documento, celular) como número,
     * no como texto — sin esto, la regla 'string' de rules() las rechaza.
     */
    public function prepareForValidation(array $data, int $index): array
    {
        foreach (['numero_documento', 'celular'] as $campo) {
            if (isset($data[$campo]) && $data[$campo] !== '') {
                $data[$campo] = (string) $data[$campo];
            }
        }

        return $data;
    }

    public function model(array $row): ?EventAttendee
    {
        if (trim((string) ($row['nombres'] ?? '')) === '') {
            return null;
        }

        $documento = $this->valorONulo($row['numero_documento'] ?? null);
        if ($documento) {
            $this->documentosVistos[] = $documento;
        }

        $numero = $this->siguienteNumero++;

        return new EventAttendee([
            'event_id'         => $this->evento->id,
            'codigo'           => sprintf('EV%d-%06d', $this->evento->id, $numero),
            'qr_token'         => (string) Str::uuid(),
            'nombres'          => $row['nombres'],
            'empresa'          => $row['empresa'] ?? null,
            'tipo_documento'   => $this->normalizarTipoDocumento($row['tipo_documento'] ?? null),
            'numero_documento' => $this->valorONulo($row['numero_documento'] ?? null),
            'direccion'        => $row['direccion'] ?? null,
            'email'            => $row['email'] ?? null,
            'telefono'         => $this->valorONulo($row['celular'] ?? null),
            'estado'           => 'registrado',
            'created_by'       => auth()->id(),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombres'          => ['required', 'string', 'max:200'],
            'numero_documento' => [
                'nullable', 'string', 'max:20',
                Rule::unique('event_attendees', 'numero_documento')->where('event_id', $this->evento->id),
                function (string $attribute, mixed $value, \Closure $fail) {
                    if ($value && in_array(trim((string) $value), $this->documentosVistos, true)) {
                        $fail('Este documento está duplicado en el archivo.');
                    }
                },
            ],
            'email' => ['nullable', 'email'],
        ];
    }

    private function normalizarTipoDocumento(null|string|int $valor): ?string
    {
        $valor = mb_strtoupper(trim((string) $valor));

        return in_array($valor, EventAttendee::TIPOS_DOCUMENTO, true) ? $valor : null;
    }

    private function valorONulo(null|string|int $valor): ?string
    {
        $valor = trim((string) $valor);

        return $valor !== '' ? $valor : null;
    }
}
