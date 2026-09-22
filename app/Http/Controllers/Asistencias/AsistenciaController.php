<?php

namespace App\Http\Controllers\Asistencias;

use App\Http\Controllers\Controller;
use App\Models\Asistencia;
use App\Models\EmpresaConfig;
use App\Models\Justificacion;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class AsistenciaController extends Controller
{
    // ── Vista para marcar entrada / salida ────────────────────────────
    public function marcar()
    {
        $hoy        = today();
        $userId     = auth()->id();
        $registros  = Asistencia::where('user_id', $userId)->whereDate('fecha', $hoy)->orderBy('hora')->get();
        $entrada    = $registros->firstWhere('tipo', 'entrada');
        $salida     = $registros->firstWhere('tipo', 'salida');
        $config     = EmpresaConfig::config();

        $justificaciones = Justificacion::where('user_id', $userId)
            ->whereDate('fecha', $hoy)
            ->where('estado', '!=', 'rechazado')
            ->get();
        $justEntrada = $justificaciones->firstWhere('tipo', 'entrada');
        $justSalida  = $justificaciones->firstWhere('tipo', 'salida');

        return view('asistencias.marcar', compact('entrada', 'salida', 'config', 'justEntrada', 'justSalida'));
    }

    // ── Procesar registro (POST desde marcar) ─────────────────────────
    public function registrar(Request $request)
    {
        $request->validate([
            'latitud'  => ['required', 'numeric', 'between:-90,90'],
            'longitud' => ['required', 'numeric', 'between:-180,180'],
        ]);

        $config = EmpresaConfig::config();

        if (!$config->tieneGeoConfigurado()) {
            return back()->with('error', 'El administrador aún no configuró la ubicación de la oficina.');
        }

        $distancia = Asistencia::haversine(
            (float) $config->asistencia_latitud,
            (float) $config->asistencia_longitud,
            (float) $request->latitud,
            (float) $request->longitud,
        );

        $radio = $config->radioMetros();

        if ($distancia > $radio) {
            return back()->with('error',
                'Estás a ' . round($distancia) . ' metros de la oficina. Debes estar dentro de los ' . $radio . ' metros permitidos.'
            );
        }

        $hoy    = today();
        $userId = auth()->id();

        $justificadas = Justificacion::where('user_id', $userId)
            ->whereDate('fecha', $hoy)
            ->where('estado', '!=', 'rechazado')
            ->pluck('tipo');

        $tieneEntrada = $justificadas->contains('entrada')
            || Asistencia::where('user_id', $userId)->whereDate('fecha', $hoy)->where('tipo', 'entrada')->exists();
        $tieneSalida  = $justificadas->contains('salida')
            || Asistencia::where('user_id', $userId)->whereDate('fecha', $hoy)->where('tipo', 'salida')->exists();

        if ($tieneSalida) {
            return back()->with('error', 'Ya completaste tu registro de asistencia de hoy.');
        }

        $tipo = $tieneEntrada ? 'salida' : 'entrada';

        Asistencia::create([
            'user_id'           => $userId,
            'tipo'              => $tipo,
            'fecha'             => $hoy,
            'hora'              => now()->format('H:i:s'),
            'latitud'           => $request->latitud,
            'longitud'          => $request->longitud,
            'distancia_metros'  => round($distancia, 1),
            'radio_configurado' => $radio,
            'ip_address'        => $request->ip(),
        ]);

        return back()->with('success', ucfirst($tipo) . ' registrada correctamente a las ' . now()->format('H:i') . '.');
    }

    // ── Reporte mensual ───────────────────────────────────────────────
    public function index(Request $request)
    {
        $authUser  = auth()->user();
        $esGestor  = $authUser->hasPermissionTo('asistencias.gestionar');

        $anio = (int) $request->input('anio', now()->year);
        $mes  = (int) $request->input('mes',  now()->month);

        $usuarioIdRaw  = $esGestor ? $request->input('usuario_id', '0') : (string) $authUser->id;
        $vistaGeneral  = $esGestor && $usuarioIdRaw === '0';
        $usuarioId     = $vistaGeneral ? $authUser->id : (int) $usuarioIdRaw;
        $usuarioFiltro = $vistaGeneral ? $authUser : User::findOrFail($usuarioId);

        $usuarios = $esGestor
            ? User::orderBy('name')->get(['id', 'name'])
            : collect();

        // Días laborables del mes (lunes a sábado)
        $primerDia     = Carbon::create($anio, $mes, 1);
        $diasDelMes    = $primerDia->daysInMonth;
        $diasLaborales = collect(range(1, $diasDelMes))->filter(
            fn($d) => Carbon::create($anio, $mes, $d)->dayOfWeek !== Carbon::SUNDAY
        )->count();

        // Vista general: todos los empleados en una sola tabla (solo gestor)
        $registrosTodos = collect();
        $justTodosPendientes = collect();
        if ($vistaGeneral) {
            $registrosTodos = Asistencia::with('empleado')
                ->delMes($anio, $mes)
                ->orderBy('hora')
                ->get()
                ->groupBy([
                    fn($r) => $r->fecha->format('Y-m-d'),
                    fn($r) => $r->user_id,
                ]);

            $justTodosPendientes = Justificacion::whereYear('fecha', $anio)->whereMonth('fecha', $mes)
                ->where('estado', '!=', 'rechazado')
                ->get()
                ->groupBy([
                    fn($j) => $j->fecha->format('Y-m-d'),
                    fn($j) => $j->user_id,
                ]);
        }

        // Registros del mes agrupados por fecha (vista individual)
        $registros = Asistencia::where('user_id', $usuarioId)
            ->delMes($anio, $mes)
            ->orderBy('hora')
            ->get()
            ->groupBy(fn($r) => $r->fecha->format('Y-m-d'));

        // Estadísticas (solo para vista individual)
        $diasPresentes  = $registros->count();
        $diasCompletos  = $registros->filter(
            fn($g) => $g->firstWhere('tipo', 'entrada') && $g->firstWhere('tipo', 'salida')
        )->count();

        $minutosTotal = $registros->sum(function ($grupo) {
            $ent = $grupo->firstWhere('tipo', 'entrada');
            $sal = $grupo->firstWhere('tipo', 'salida');
            if ($ent && $sal) {
                return Carbon::parse($ent->hora)->diffInMinutes(Carbon::parse($sal->hora));
            }
            return 0;
        });

        $horasTotal = intdiv($minutosTotal, 60);
        $minsExtra  = $minutosTotal % 60;

        // Hoy: resumen del equipo (solo gestor)
        $hoyEquipo   = null;
        $justHoyPorUsuario = collect();
        if ($esGestor) {
            $hoyEquipo = User::with(['asistenciasHoy' => fn($q) => $q->orderBy('hora')])
                ->whereHas('roles')
                ->orderBy('name')
                ->get();

            $justHoyPorUsuario = Justificacion::whereDate('fecha', today())
                ->where('estado', '!=', 'rechazado')
                ->get()
                ->groupBy('user_id');
        }

        // Justificaciones del mes para el usuario filtrado (vista individual)
        $justificaciones = Justificacion::where('user_id', $usuarioId)
            ->whereYear('fecha', $anio)->whereMonth('fecha', $mes)
            ->get()->keyBy(fn($j) => $j->fecha->format('Y-m-d') . '_' . $j->tipo);

        // Pendientes para el gestor
        $justPendientes = $esGestor
            ? Justificacion::with('empleado')->where('estado', 'pendiente')->orderBy('fecha')->get()
            : collect();

        return view('asistencias.index', compact(
            'registros', 'registrosTodos', 'vistaGeneral',
            'esGestor', 'anio', 'mes', 'primerDia', 'diasDelMes',
            'usuarioFiltro', 'usuarios',
            'diasPresentes', 'diasCompletos', 'diasLaborales',
            'horasTotal', 'minsExtra', 'hoyEquipo', 'justHoyPorUsuario',
            'justificaciones', 'justPendientes', 'justTodosPendientes'
        ));
    }

    // ── Justificaciones ───────────────────────────────────────────────

    public function crearJustificacion(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('asistencias.registrar'), 403);

        $fecha = $request->input('fecha', today()->format('Y-m-d'));
        $tipo  = $request->input('tipo', 'entrada');

        // No justificar si ya hay asistencia registrada para ese día/tipo
        $yaExiste = Asistencia::where('user_id', auth()->id())
            ->whereDate('fecha', $fecha)->where('tipo', $tipo)->exists();

        $yaEnviada = Justificacion::where('user_id', auth()->id())
            ->whereDate('fecha', $fecha)->where('tipo', $tipo)->exists();

        return view('asistencias.justificar', compact('fecha', 'tipo', 'yaExiste', 'yaEnviada'));
    }

    public function guardarJustificacion(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('asistencias.registrar'), 403);

        $data = $request->validate([
            'fecha'            => ['required', 'date', 'before_or_equal:today'],
            'tipo'             => ['required', 'in:entrada,salida'],
            'hora_justificada' => ['required', 'date_format:H:i'],
            'motivo'           => ['required', 'string', 'max:600'],
        ]);

        $yaExiste = Asistencia::where('user_id', auth()->id())
            ->whereDate('fecha', $data['fecha'])->where('tipo', $data['tipo'])->exists();

        if ($yaExiste) {
            return back()->with('error', 'Ya tienes una asistencia registrada para ese día y tipo.');
        }

        Justificacion::updateOrCreate(
            ['user_id' => auth()->id(), 'fecha' => $data['fecha'], 'tipo' => $data['tipo']],
            [
                'hora_justificada' => $data['hora_justificada'] . ':00',
                'motivo'           => $data['motivo'],
                'estado'           => 'pendiente',
                'respuesta_admin'  => null,
                'atendido_por'     => null,
                'atendido_at'      => null,
                'asistencia_id'    => null,
            ]
        );

        return redirect()->route('asistencias.index')
            ->with('success', 'Justificación enviada. El administrador la revisará pronto.');
    }

    public function aprobarJustificacion(Request $request, Justificacion $justificacion)
    {
        abort_unless(auth()->user()->hasPermissionTo('asistencias.gestionar'), 403);

        $request->validate(['respuesta_admin' => ['nullable', 'string', 'max:400']]);

        // Crear el registro de asistencia marcado como justificado
        $asistencia = Asistencia::firstOrCreate(
            [
                'user_id' => $justificacion->user_id,
                'fecha'   => $justificacion->fecha->format('Y-m-d'),
                'tipo'    => $justificacion->tipo,
            ],
            [
                'hora'              => $justificacion->hora_justificada,
                'latitud'           => 0,
                'longitud'          => 0,
                'distancia_metros'  => 0,
                'radio_configurado' => 0,
                'justificada'       => true,
                'observaciones'     => $justificacion->motivo,
            ]
        );

        // Si ya existía el registro, marcarlo como justificado
        if (!$asistencia->wasRecentlyCreated) {
            $asistencia->update(['justificada' => true]);
        }

        $justificacion->update([
            'estado'          => 'aprobado',
            'respuesta_admin' => $request->respuesta_admin,
            'atendido_por'    => auth()->id(),
            'atendido_at'     => now(),
            'asistencia_id'   => $asistencia->id,
        ]);

        return redirect()->route('asistencias.index', [
            'usuario_id' => $justificacion->user_id,
            'mes'        => $justificacion->fecha->month,
            'anio'       => $justificacion->fecha->year,
        ])->with('success', 'Justificación aprobada — asistencia registrada para ' . $justificacion->empleado->name . '.');
    }

    public function rechazarJustificacion(Request $request, Justificacion $justificacion)
    {
        abort_unless(auth()->user()->hasPermissionTo('asistencias.gestionar'), 403);

        $request->validate(['respuesta_admin' => ['required', 'string', 'max:400']]);

        $justificacion->update([
            'estado'          => 'rechazado',
            'respuesta_admin' => $request->respuesta_admin,
            'atendido_por'    => auth()->id(),
            'atendido_at'     => now(),
        ]);

        return redirect()->route('asistencias.index', [
            'usuario_id' => $justificacion->user_id,
            'mes'        => $justificacion->fecha->month,
            'anio'       => $justificacion->fecha->year,
        ])->with('success', 'Justificación rechazada.');
    }

    // ── Config geo (admin) ────────────────────────────────────────────
    public function configGeo()
    {
        abort_unless(auth()->user()->hasPermissionTo('asistencias.gestionar'), 403);
        $config = EmpresaConfig::config();
        return view('asistencias.config', compact('config'));
    }

    public function guardarConfigGeo(Request $request)
    {
        abort_unless(auth()->user()->hasPermissionTo('asistencias.gestionar'), 403);

        $request->validate([
            'asistencia_latitud'      => ['required', 'numeric', 'between:-90,90'],
            'asistencia_longitud'     => ['required', 'numeric', 'between:-180,180'],
            'asistencia_radio_metros' => ['required', 'integer', 'min:5', 'max:500'],
        ]);

        $config = EmpresaConfig::config();
        $config->fill($request->only(['asistencia_latitud', 'asistencia_longitud', 'asistencia_radio_metros']));
        $config->save();

        return back()->with('success', 'Configuración de geolocalización guardada.');
    }
}
