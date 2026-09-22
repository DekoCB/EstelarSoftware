<?php

namespace App\Exports;

use App\Models\Event;
use App\Models\EventAttendee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EventAttendeesExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    private const ULTIMA_COLUMNA = 'J';

    private const ANCHO_COLUMNAS = [
        'A' => 24, // Nombres
        'B' => 20, // Empresa
        'C' => 14, // Tipo documento
        'D' => 16, // Numero documento
        'E' => 28, // Direccion
        'F' => 22, // Email
        'G' => 14, // Celular
        'H' => 16, // Codigo
        'I' => 14, // Estado
        'J' => 18, // Fecha de registro
    ];

    public function __construct(private readonly Event $evento)
    {
    }

    public function collection(): Collection
    {
        return $this->evento->asistentes()->orderBy('created_at')->get();
    }

    public function headings(): array
    {
        return ['Nombres', 'Empresa', 'Tipo documento', 'Numero documento', 'Direccion', 'Email', 'Celular', 'Codigo', 'Estado', 'Fecha de registro'];
    }

    public function map($asistente): array
    {
        return [
            $asistente->nombres,
            $asistente->empresa,
            $asistente->tipo_documento,
            $asistente->numero_documento,
            $asistente->direccion,
            $asistente->email,
            $asistente->telefono,
            $asistente->codigo,
            $asistente->estadoLabel(),
            $asistente->created_at->format('d/m/Y H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return self::ANCHO_COLUMNAS;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $total = $this->collection()->count();

                if ($total === 0) {
                    return;
                }

                $lastRow = $total + 1;

                $sheet->getStyle('A1:' . self::ULTIMA_COLUMNA . '1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E3A5F'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                $sheet->getStyle('A1:' . self::ULTIMA_COLUMNA . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                $sheet->getStyle('A2:' . self::ULTIMA_COLUMNA . $lastRow)->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],
                ]);

                $coloresEstado = [
                    'Registrado' => 'BAE6FD',
                    'Asistió'    => 'BBF7D0',
                    'Cancelado'  => 'E2E8F0',
                ];

                foreach ($this->collection() as $i => $asistente) {
                    $fila = $i + 2;
                    $hex = $coloresEstado[$asistente->estadoLabel()] ?? null;
                    if ($hex) {
                        $sheet->getStyle("I{$fila}")->applyFromArray([
                            'fill' => [
                                'fillType'   => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => $hex],
                            ],
                        ]);
                    }
                }
            },
        ];
    }
}
