<?php

namespace Database\Seeders;

use App\Models\Asistencia;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Database\Seeder;

class AsistenciasDesarrolladorSeeder extends Seeder
{
    // ── Configuración ──────────────────────────────────────────────────
    const FECHA_INICIO = '2026-06-01';
    const ROL          = 'desarrollador';

    // Rango entrada: 08:30 – 08:45
    const ENTRADA_HORA = 8;
    const ENTRADA_MIN_DESDE = 30;
    const ENTRADA_MIN_HASTA = 45;

    // Rango salida: 18:00 – 18:30
    const SALIDA_HORA = 18;
    const SALIDA_MIN_DESDE = 0;
    const SALIDA_MIN_HASTA = 30;

    // Usuarios que NO tienen registro hoy (nombre exacto)
    const EXCLUIR_HOY  = ['Carlo Mayta'];
    // ───────────────────────────────────────────────────────────────────

    public function run(): void
    {
        $usuarios = User::role(self::ROL)->get();

        if ($usuarios->isEmpty()) {
            $this->command->warn('No se encontraron usuarios con el rol "' . self::ROL . '".');
            return;
        }

        $hoy    = Carbon::today();
        $inicio = Carbon::parse(self::FECHA_INICIO);
        $period = CarbonPeriod::create($inicio, $hoy);

        $creados = 0;

        foreach ($usuarios as $usuario) {
            $excluirHoy = in_array($usuario->name, self::EXCLUIR_HOY);
            $this->command->info("Procesando: {$usuario->name}" . ($excluirHoy ? ' (sin registro hoy)' : ''));

            foreach ($period as $fecha) {
                if ($fecha->dayOfWeek === Carbon::SUNDAY) {
                    continue;
                }

                if ($excluirHoy && $fecha->isToday()) {
                    $this->command->line("  · {$fecha->format('d/m/Y')} omitido (excluido hoy)");
                    continue;
                }

                $fechaStr = $fecha->format('Y-m-d');

                // Generar horas aleatorias únicas por usuario+día
                // Usar seed reproducible para que no cambie si se re-ejecuta en debug
                mt_srand(crc32($usuario->id . $fechaStr));
                $minEntrada = mt_rand(self::ENTRADA_MIN_DESDE, self::ENTRADA_MIN_HASTA);
                $segEntrada = mt_rand(0, 59);
                $minSalida  = mt_rand(self::SALIDA_MIN_DESDE, self::SALIDA_MIN_HASTA);
                $segSalida  = mt_rand(0, 59);

                $horaEntrada = sprintf('%02d:%02d:%02d', self::ENTRADA_HORA, $minEntrada, $segEntrada);
                $horaSalida  = sprintf('%02d:%02d:%02d', self::SALIDA_HORA,  $minSalida,  $segSalida);

                foreach (['entrada' => $horaEntrada, 'salida' => $horaSalida] as $tipo => $hora) {
                    Asistencia::updateOrCreate(
                        [
                            'user_id' => $usuario->id,
                            'fecha'   => $fechaStr,
                            'tipo'    => $tipo,
                        ],
                        [
                            'hora'              => $hora,
                            'latitud'           => 0,
                            'longitud'          => 0,
                            'distancia_metros'  => 0,
                            'radio_configurado' => 0,
                            'justificada'       => false,
                            'observaciones'     => 'Registro manual administrativo',
                        ]
                    );

                    $creados++;
                }

                $this->command->line("  · {$fechaStr} → entrada {$horaEntrada}  salida {$horaSalida}");
            }
        }

        $this->command->info("Listo: {$creados} registros insertados/actualizados.");
    }
}
