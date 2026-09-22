<?php

namespace App\Support;

class LeadExcelColors
{
    // Mismos colores que App\Support\LeadResponseOptions::COLORES_DISPONIBLES,
    // en su tono claro (200) para usar como relleno de celda legible en Excel.
    public const HEX = [
        'red'     => 'FECACA',
        'slate'   => 'E2E8F0',
        'orange'  => 'FED7AA',
        'purple'  => 'E9D5FF',
        'teal'    => '99F6E4',
        'sky'     => 'BAE6FD',
        'rose'    => 'FECDD3',
        'amber'   => 'FDE68A',
        'violet'  => 'DDD6FE',
        'yellow'  => 'FDE68A',
        'emerald' => 'A7F3D0',
    ];

    public static function paraClave(?string $clave): ?string
    {
        return self::HEX[LeadResponseOptions::color($clave)] ?? null;
    }
}
