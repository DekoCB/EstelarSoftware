<?php

namespace App\Exports;

use App\Models\Lead;
use App\Support\LeadExcelColors;
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

class LeadsExport implements FromCollection, WithHeadings, WithMapping, WithColumnWidths, WithEvents
{
    private const ULTIMA_COLUMNA = 'K';

    // Ancho fijo por columna, basado en el largo del encabezado (celda de la fila 1).
    // El contenido más largo no ensancha la columna: salta de línea (wrapText).
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

    public function __construct(
        private readonly ?string $desde = null,
        private readonly ?string $hasta = null,
    ) {
    }

    public function collection(): Collection
    {
        return Lead::query()
            ->when($this->desde, fn ($q) => $q->whereDate('created_at', '>=', $this->desde))
            ->when($this->hasta, fn ($q) => $q->whereDate('created_at', '<=', $this->hasta))
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->get();
    }

    public function headings(): array
    {
        return [
            'Nombre', 'Empresa', 'Lugar', 'Teléfono', 'Email',
            'Respuesta', 'Comentario', 'Fecha reunión', 'Hora reunión', 'Sistema interés',
            'Fecha de registro',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->nombre,
            $lead->empresa,
            $lead->lugar,
            $lead->telefono,
            $lead->email,
            $lead->respuestaLabel(),
            $lead->respuesta_comentario,
            $lead->respuesta_fecha?->format('d/m/Y'),
            $lead->respuesta_hora ? substr($lead->respuesta_hora, 0, 5) : null,
            $lead->sistema_interes,
            $lead->created_at->format('d/m/Y H:i'),
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
                $leads = $this->collection();

                if ($leads->isEmpty()) {
                    return;
                }

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

                // Agrupa por fecha de registro (igual que la lista en pantalla) y
                // escribe una fila separadora por día antes de sus leads.
                $grupos = $leads->groupBy(fn (Lead $lead) => $lead->created_at->toDateString());

                $fila = 2;
                $celdasColor = [];

                foreach ($grupos as $leadsDelDia) {
                    $sheet->mergeCells("A{$fila}:" . self::ULTIMA_COLUMNA . $fila);
                    $sheet->setCellValue(
                        "A{$fila}",
                        $leadsDelDia->first()->created_at->locale('es')->isoFormat('D [de] MMMM, YYYY')
                    );
                    $sheet->getStyle("A{$fila}")->applyFromArray([
                        'font'      => ['bold' => true, 'color' => ['rgb' => '38BDF8']],
                        'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                        'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
                    ]);
                    $sheet->getRowDimension($fila)->setRowHeight(20);
                    $fila++;

                    foreach ($leadsDelDia as $lead) {
                        $columna = 'A';
                        foreach ($this->map($lead) as $valor) {
                            $sheet->setCellValue("{$columna}{$fila}", $valor);
                            $columna++;
                        }

                        $hex = LeadExcelColors::HEX[$lead->respuestaColor()] ?? null;
                        if ($lead->respuesta_rapida && $hex) {
                            $celdasColor[$fila] = $hex;
                        }

                        $fila++;
                    }
                }

                $ultimaFila = $fila - 1;

                // Bordes en todo el rango de datos.
                $sheet->getStyle('A1:' . self::ULTIMA_COLUMNA . $ultimaFila)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color'       => ['rgb' => '9CA3AF'],
                        ],
                    ],
                ]);

                // El texto que no cabe en el ancho fijo de la columna salta de
                // línea en vez de ensancharla (Excel autoajusta el alto de fila).
                $sheet->getStyle('A2:' . self::ULTIMA_COLUMNA . $ultimaFila)->applyFromArray([
                    'alignment' => [
                        'wrapText'   => true,
                        'vertical'   => Alignment::VERTICAL_TOP,
                    ],
                ]);

                // Relleno de la celda "Respuesta" (columna F) según el color
                // asignado a cada tipo de respuesta (ej. Reunión pendiente = amarillo).
                foreach ($celdasColor as $fila => $hex) {
                    $sheet->getStyle("F{$fila}")->applyFromArray([
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
