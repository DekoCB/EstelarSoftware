<?php

namespace App\Support;

use App\Models\LeadResponseOption;

class LeadResponseOptions
{
    // Colores ya usados en el resto del sistema (con bg-{color}-500/15 + text-{color}-400)
    // para garantizar que el estilo exista sin depender de una recompilación de Tailwind.
    public const COLORES_DISPONIBLES = [
        'red', 'slate', 'orange', 'purple', 'teal', 'sky',
        'rose', 'amber', 'violet', 'yellow', 'emerald',
    ];

    private static ?array $cache = null;

    // clave => [label, color, requiere: ['fecha','hora','sistema'], comentario_auto]
    public static function opciones(): array
    {
        if (self::$cache !== null) {
            return self::$cache;
        }

        return self::$cache = LeadResponseOption::orderBy('orden')->get()
            ->mapWithKeys(fn (LeadResponseOption $o) => [
                $o->clave => [
                    'label'           => $o->label,
                    'color'           => $o->color,
                    'requiere'        => array_values(array_filter([
                        $o->requiere_fecha   ? 'fecha'   : null,
                        $o->requiere_hora    ? 'hora'    : null,
                        $o->requiere_sistema ? 'sistema' : null,
                    ])),
                    'comentario_auto' => $o->comentario_auto,
                ],
            ])->all();
    }

    public static function label(?string $clave): ?string
    {
        return $clave ? (self::opciones()[$clave]['label'] ?? $clave) : null;
    }

    public static function color(?string $clave): string
    {
        return $clave ? (self::opciones()[$clave]['color'] ?? 'slate') : 'slate';
    }

    public static function requiere(?string $clave, string $campo): bool
    {
        return in_array($campo, self::opciones()[$clave]['requiere'] ?? [], true);
    }

    public static function comentarioAuto(?string $clave): ?string
    {
        return $clave ? (self::opciones()[$clave]['comentario_auto'] ?? null) : null;
    }
}
