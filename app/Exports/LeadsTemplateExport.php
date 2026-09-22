<?php

namespace App\Exports;

use App\Support\LeadExcelColors;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LeadsTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithEvents
{
    private const ULTIMA_COLUMNA = 'K';

    // Ancho fijo por columna, basado en el largo del encabezado (celda de la fila 1).
    private const ANCHO_COLUMNAS = [
        // 'A' (Nombre) se fija en píxeles en registerEvents(), no aquí.
        'B' => 12, // Empresa
        'C' => 10, // Lugar
        // 'D' (Teléfono) se fija en píxeles en registerEvents(), no aquí.
        'E' => 10, // Email
        'F' => 14, // Respuesta
        // 'G' (Comentario) se fija en píxeles en registerEvents(), no aquí.
        'H' => 16, // Fecha reunión
        'I' => 15, // Hora reunión
        'J' => 18, // Sistema interés
        'K' => 21, // Fecha de registro
    ];

    public function array(): array
    {
        return [
            [
                'Juan Pérez', 'Acme SAC', 'Huancayo', '+51 999 888 777', 'juan.perez@acme.com',
                'Reunión pendiente', 'Interesado en el plan anual', '25/08/2026', '10:00', 'SISTEMA PRE-UNIVERSITARIO',
                '25/08/2026 10:00',
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'Nombre', 'Empresa', 'Lugar', 'Teléfono', 'Email',
            'Respuesta', 'Comentario', 'Fecha reunión', 'Hora reunión', 'Sistema interés',
            'Fecha de registro',
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
                $lastRow = count($this->array()) + 1;

                // Columnas con ancho fijo en píxeles.
                $sheet->getColumnDimension('A')->setWidth(160, 'px');
                $sheet->getColumnDimension('D')->setWidth(140, 'px');
                $sheet->getColumnDimension('G')->setWidth(421, 'px');

                // Encabezados: negrita, texto blanco y relleno de color.
                $sheet->getStyle('A1:' . self::ULTIMA_COLUMNA . '1')->applyFromArray([
                    'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => [
                        'fillType'   => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '1E3A5F'],
                    ],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(22);

                // Bordes en todo el rango de datos.
                $sheet->getStyle('A1:' . self::ULTIMA_COLUMNA . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // El texto que no cabe en el ancho fijo de la columna salta de
                // línea en vez de ensancharla (Excel autoajusta el alto de fila).
                $sheet->getStyle('A2:' . self::ULTIMA_COLUMNA . $lastRow)->applyFromArray([
                    'alignment' => [
                        'wrapText' => true,
                        'vertical' => Alignment::VERTICAL_TOP,
                    ],
                ]);

                // Relleno de la celda "Respuesta" del ejemplo, según el color
                // asignado a esa respuesta en el sistema (Reunión pendiente = amarillo).
                $hex = LeadExcelColors::paraClave('reunion_pendiente');

                if ($hex) {
                    $sheet->getStyle('F2')->applyFromArray([
                        'fill' => [
                            'fillType'   => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $hex],
                        ],
                    ]);
                }
            },
        ];
    }
}
