<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class EventAttendeesTemplateExport implements FromArray, WithHeadings, WithColumnWidths, WithEvents
{
    private const ULTIMA_COLUMNA = 'G';

    private const ANCHO_COLUMNAS = [
        'A' => 24, // Nombres
        'B' => 20, // Empresa
        'C' => 14, // Tipo documento
        'D' => 16, // Numero documento
        'E' => 28, // Direccion
        'F' => 22, // Email
        'G' => 14, // Celular
    ];

    public function array(): array
    {
        return [
            ['Juan Pérez', 'Acme SAC', 'DNI', '74093841', 'Av. Ejemplo 123', 'juan.perez@acme.com', '987654321'],
        ];
    }

    public function headings(): array
    {
        return ['Nombres', 'Empresa', 'Tipo documento', 'Numero documento', 'Direccion', 'Email', 'Celular'];
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
            },
        ];
    }
}
