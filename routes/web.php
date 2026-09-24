<?php

use App\Http\Controllers\Admin\ConfigController;
use App\Http\Controllers\Equipo\ReporteDiarioController;
use App\Http\Controllers\Reportes\ReporteController;
use App\Http\Controllers\Solicitudes\SolicitudController;
use App\Http\Controllers\Asistencias\AsistenciaController;
use App\Http\Controllers\DocumentoLookupController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\RolController;
use App\Http\Controllers\Planilla\PlanillaController;
use App\Http\Controllers\Admin\UsuarioController;
use App\Http\Controllers\Caja\CajaController;
use App\Http\Controllers\Clientes\ClienteController;
use App\Http\Controllers\Entregas\EntregaController;
use App\Http\Controllers\Eventos\EventAttendeeController;
use App\Http\Controllers\Eventos\EventAttendeeImportController;
use App\Http\Controllers\Eventos\EventCheckinController;
use App\Http\Controllers\Eventos\EventoController;
use App\Http\Controllers\Eventos\EventLeadController;
use App\Http\Controllers\Eventos\EventTeamPagoController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Proyectos\DailyReportController;
use App\Http\Controllers\Proyectos\ProyectoController;
use App\Http\Controllers\Proyectos\RequerimientoController;
use App\Http\Controllers\Proyectos\SprintController;
use App\Http\Controllers\Facturacion\FacturaController;
use App\Http\Controllers\Leads\LeadController;
use App\Http\Controllers\Leads\LeadImportController;
use App\Http\Controllers\Leads\ReunionController;
use App\Http\Controllers\Ventas\CotizacionController;
use App\Http\Controllers\Ventas\QuotePaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('dashboard');
    }
    // El evento "Go Left!!" de la landing reutiliza el modulo de Eventos
    // (inscripcion publica ya existente en /eventos/{evento}/inscripcion)
    // en vez de un formulario aparte. Se busca por nombre -no por ID fijo-
    // porque el ID real difiere entre el entorno local y produccion.
    $goLeftEvent = \App\Models\Event::where('nombre', 'Go Left!!')->first();
    return view('welcome', compact('goLeftEvent'));
})->name('landing');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// ── Clientes ────────────────────────────────────────────────────────
// IMPORTANTE: rutas con segmento fijo (create) ANTES de wildcards ({cliente})
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/clientes', [ClienteController::class, 'index'])
        ->middleware('permission:clientes.ver')
        ->name('clientes.index');

    // create ANTES de {cliente} para que no lo capture el wildcard
    Route::get('/clientes/create', [ClienteController::class, 'create'])
        ->middleware('permission:clientes.crear')
        ->name('clientes.create');

    Route::post('/clientes', [ClienteController::class, 'store'])
        ->middleware('permission:clientes.crear')
        ->name('clientes.store');

    Route::get('/clientes/{cliente}', [ClienteController::class, 'show'])
        ->middleware('permission:clientes.ver')
        ->name('clientes.show');

    Route::get('/clientes/{cliente}/edit', [ClienteController::class, 'edit'])
        ->middleware('permission:clientes.editar')
        ->name('clientes.edit');

    Route::put('/clientes/{cliente}', [ClienteController::class, 'update'])
        ->middleware('permission:clientes.editar')
        ->name('clientes.update');

    Route::patch('/clientes/{cliente}', [ClienteController::class, 'update'])
        ->middleware('permission:clientes.editar');

    Route::delete('/clientes/{cliente}', [ClienteController::class, 'destroy'])
        ->middleware('permission:clientes.eliminar')
        ->name('clientes.destroy');

});

// ── Leads ───────────────────────────────────────────────────────────
// IMPORTANTE: rutas con segmento fijo ANTES de wildcards ({lead})
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/leads',           [LeadController::class, 'index'])->middleware('permission:leads.ver')->name('leads.index');
    Route::get('/leads/create',    [LeadController::class, 'create'])->middleware('permission:leads.crear')->name('leads.create');
    Route::post('/leads',          [LeadController::class, 'store'])->middleware('permission:leads.crear')->name('leads.store');

    Route::get('/leads/plantilla', [LeadImportController::class, 'plantilla'])->middleware('permission:leads.crear')->name('leads.plantilla');
    Route::get('/leads/importar',  [LeadImportController::class, 'crear'])->middleware('permission:leads.crear')->name('leads.importar');
    Route::post('/leads/importar', [LeadImportController::class, 'importar'])->middleware('permission:leads.crear')->name('leads.importar.store');

    Route::get('/leads/calendario',         [LeadController::class, 'calendario'])->middleware('permission:leads.ver')->name('leads.calendario');
    Route::get('/leads/calendario/eventos', [LeadController::class, 'eventos'])->middleware('permission:leads.ver')->name('leads.calendario.eventos');

    Route::get('/leads/exportar', [LeadController::class, 'exportar'])->middleware('permission:leads.ver')->name('leads.exportar');

    Route::get('/leads/verificar-telefono', [LeadController::class, 'verificarTelefono'])->middleware('permission:leads.crear|leads.editar')->name('leads.verificar-telefono');

    Route::get('/leads/{lead}',      [LeadController::class, 'show'])->middleware('permission:leads.ver')->name('leads.show');
    Route::get('/leads/{lead}/edit', [LeadController::class, 'edit'])->middleware('permission:leads.editar')->name('leads.edit');
    Route::put('/leads/{lead}',      [LeadController::class, 'update'])->middleware('permission:leads.editar')->name('leads.update');
    Route::patch('/leads/{lead}',    [LeadController::class, 'update'])->middleware('permission:leads.editar');
    Route::delete('/leads/{lead}',   [LeadController::class, 'destroy'])->middleware('permission:leads.eliminar')->name('leads.destroy');

    Route::post('/leads/{lead}/reuniones', [ReunionController::class, 'store'])->middleware('permission:leads.editar')->name('leads.reuniones.store');
    Route::put('/leads/{lead}/reuniones/{reunion}', [ReunionController::class, 'update'])->middleware('permission:leads.editar')->name('leads.reuniones.update');
    Route::delete('/leads/{lead}/reuniones/{reunion}', [ReunionController::class, 'destroy'])->middleware('permission:leads.editar')->name('leads.reuniones.destroy');

    Route::patch('/leads/{lead}/cotizacion', [LeadController::class, 'actualizarCotizacion'])->middleware('permission:leads.editar')->name('leads.cotizacion.update');
});

// ── Módulos en construcción (placeholders por sprint) ───────────────
Route::middleware(['auth', 'verified'])->group(function () {
    $proximamente = fn(string $modulo, int $sprint) =>
        view('proximamente', compact('modulo', 'sprint'));

    // Proyectos
    Route::get('/proyectos', [ProyectoController::class, 'index'])
        ->middleware('permission:proyectos.ver|proyectos.ver_asignados')
        ->name('proyectos.index');
    Route::get('/proyectos/create', [ProyectoController::class, 'create'])
        ->middleware('permission:proyectos.crear')->name('proyectos.create');
    Route::post('/proyectos', [ProyectoController::class, 'store'])
        ->middleware('permission:proyectos.crear')->name('proyectos.store');
    Route::get('/proyectos/{proyecto}', [ProyectoController::class, 'show'])
        ->middleware('permission:proyectos.ver|proyectos.ver_asignados')->name('proyectos.show');
    Route::get('/proyectos/{proyecto}/edit', [ProyectoController::class, 'edit'])
        ->middleware('permission:proyectos.editar')->name('proyectos.edit');
    Route::put('/proyectos/{proyecto}', [ProyectoController::class, 'update'])
        ->middleware('permission:proyectos.editar')->name('proyectos.update');
    Route::patch('/proyectos/{proyecto}', [ProyectoController::class, 'update'])
        ->middleware('permission:proyectos.editar');
    Route::delete('/proyectos/{proyecto}', [ProyectoController::class, 'destroy'])
        ->middleware('permission:proyectos.eliminar')->name('proyectos.destroy');
    Route::patch('/proyectos/{proyecto}/fases/{fase}', [ProyectoController::class, 'updatePhase'])
        ->middleware('permission:proyectos.editar')->name('proyectos.fases.update');
    Route::patch('/proyectos/{proyecto}/checklist/{index}', [ProyectoController::class, 'toggleChecklist'])
        ->middleware('permission:proyectos.editar')->name('proyectos.checklist.toggle');
    Route::patch('/proyectos/{proyecto}/notas', [ProyectoController::class, 'updateNotas'])
        ->middleware('permission:proyectos.editar')->name('proyectos.notas.update');

    // Requerimientos (anidados bajo proyecto)
    Route::get('/proyectos/{proyecto}/requerimientos', [RequerimientoController::class, 'index'])
        ->middleware('permission:requerimientos.ver')->name('requerimientos.index');
    Route::post('/proyectos/{proyecto}/requerimientos', [RequerimientoController::class, 'store'])
        ->middleware('permission:requerimientos.crear')->name('requerimientos.store');
    Route::patch('/proyectos/{proyecto}/requerimientos/{requerimiento}', [RequerimientoController::class, 'update'])
        ->middleware('permission:requerimientos.editar')->name('requerimientos.update');
    Route::delete('/proyectos/{proyecto}/requerimientos/{requerimiento}', [RequerimientoController::class, 'destroy'])
        ->middleware('permission:requerimientos.editar')->name('requerimientos.destroy');

    Route::get('/requerimientos', fn() => redirect()->route('proyectos.index'))->name('requerimientos.global');

    // Sprints (anidados bajo proyecto)
    Route::get('/proyectos/{proyecto}/sprints', [SprintController::class, 'index'])
        ->middleware('permission:sprints.ver')->name('sprints.index');
    Route::post('/proyectos/{proyecto}/sprints', [SprintController::class, 'store'])
        ->middleware('permission:sprints.gestionar')->name('sprints.store');
    Route::get('/proyectos/{proyecto}/sprints/{sprint}', [SprintController::class, 'show'])
        ->middleware('permission:sprints.ver')->name('sprints.show');
    Route::patch('/proyectos/{proyecto}/sprints/{sprint}', [SprintController::class, 'update'])
        ->middleware('permission:sprints.gestionar')->name('sprints.update');

    // Daily reports (anidados bajo sprint)
    Route::post('/proyectos/{proyecto}/sprints/{sprint}/daily', [DailyReportController::class, 'store'])
        ->middleware('permission:sprints.daily')->name('sprints.daily.store');

    // Asignar requirement a sprint
    Route::patch('/proyectos/{proyecto}/requerimientos/{requerimiento}/sprint', function (
        \App\Models\Project $proyecto,
        \App\Models\Requirement $requerimiento,
        Request $request
    ) {
        $requerimiento->update(['sprint_id' => $request->input('sprint_id') ?: null]);
        return back()->with('success', 'Tarea actualizada.');
    })->middleware('permission:sprints.gestionar')->name('requerimientos.asignar-sprint');
    // ── Cotizaciones ────────────────────────────────────────────────
    Route::get('/ventas',                    [CotizacionController::class, 'index'])->middleware('permission:cotizaciones.ver')->name('cotizaciones.index');
    Route::get('/ventas/create',             [CotizacionController::class, 'create'])->middleware('permission:cotizaciones.crear')->name('cotizaciones.create');
    Route::post('/ventas',                   [CotizacionController::class, 'store'])->middleware('permission:cotizaciones.crear')->name('cotizaciones.store');
    Route::get('/ventas/{cotizacion}',       [CotizacionController::class, 'show'])->middleware('permission:cotizaciones.ver')->name('cotizaciones.show');
    Route::get('/ventas/{cotizacion}/edit',  [CotizacionController::class, 'edit'])->middleware('permission:cotizaciones.editar')->name('cotizaciones.edit');
    Route::put('/ventas/{cotizacion}',       [CotizacionController::class, 'update'])->middleware('permission:cotizaciones.editar')->name('cotizaciones.update');
    Route::delete('/ventas/{cotizacion}',    [CotizacionController::class, 'destroy'])->middleware('permission:cotizaciones.eliminar')->name('cotizaciones.destroy');
    Route::get('/ventas/{cotizacion}/pdf',   [CotizacionController::class, 'pdf'])->middleware('permission:cotizaciones.pdf')->name('cotizaciones.pdf');
    Route::patch('/ventas/{cotizacion}/estado', [CotizacionController::class, 'cambiarEstado'])->middleware('permission:cotizaciones.aprobar')->name('cotizaciones.estado');

    // ── Plan de cobros (cuotas) ──────────────────────────────────────
    Route::middleware('permission:cotizaciones.editar')->group(function () {
        Route::post('/ventas/{cotizacion}/pagos/plan',          [QuotePaymentController::class, 'generarPlan'])->name('cotizaciones.pagos.plan');
        Route::post('/ventas/{cotizacion}/pagos',               [QuotePaymentController::class, 'store'])->name('cotizaciones.pagos.store');
        Route::patch('/ventas/{cotizacion}/pagos/{pago}',       [QuotePaymentController::class, 'update'])->name('cotizaciones.pagos.update');
        Route::patch('/ventas/{cotizacion}/pagos/{pago}/pagar', [QuotePaymentController::class, 'marcarPagado'])->name('cotizaciones.pagos.pagar');
        Route::patch('/ventas/{cotizacion}/pagos/{pago}/revertir', [QuotePaymentController::class, 'desmarcarPagado'])->name('cotizaciones.pagos.revertir');
        Route::delete('/ventas/{cotizacion}/pagos/{pago}',      [QuotePaymentController::class, 'destroy'])->name('cotizaciones.pagos.destroy');
    });
    // ── Facturación SUNAT ────────────────────────────────────────────
    Route::get('/facturacion',                       [FacturaController::class, 'index'])->middleware('permission:facturacion.ver')->name('facturacion.index');
    Route::get('/facturacion/create',                [FacturaController::class, 'create'])->middleware('permission:facturacion.emitir')->name('facturacion.create');
    Route::post('/facturacion',                      [FacturaController::class, 'store'])->middleware('permission:facturacion.emitir')->name('facturacion.store');
    Route::get('/facturacion/{factura}',             [FacturaController::class, 'show'])->middleware('permission:facturacion.ver')->name('facturacion.show');
    Route::delete('/facturacion/{factura}',          [FacturaController::class, 'destroy'])->middleware('permission:facturacion.anular')->name('facturacion.destroy');
    Route::post('/facturacion/{factura}/enviar',     [FacturaController::class, 'enviar'])->middleware('permission:facturacion.emitir')->name('facturacion.enviar');
    Route::get('/facturacion/{factura}/pdf',         [FacturaController::class, 'descargarPdf'])->middleware('permission:facturacion.ver')->name('facturacion.pdf');
    Route::get('/facturacion/{factura}/xml',         [FacturaController::class, 'descargarXml'])->middleware('permission:facturacion.ver')->name('facturacion.xml');
    Route::get('/facturacion/{factura}/cdr',         [FacturaController::class, 'descargarCdr'])->middleware('permission:facturacion.ver')->name('facturacion.cdr');
    // ── Caja ────────────────────────────────────────────────────────────
    Route::get('/caja',                    [CajaController::class, 'index'])->middleware('permission:caja.ver')->name('caja.index');
    Route::get('/caja/create',             [CajaController::class, 'create'])->middleware('permission:caja.crear')->name('caja.create');
    Route::post('/caja',                   [CajaController::class, 'store'])->middleware('permission:caja.crear')->name('caja.store');
    Route::get('/caja/{movimiento}',       [CajaController::class, 'show'])->middleware('permission:caja.ver')->name('caja.show');
    Route::get('/caja/{movimiento}/edit',  [CajaController::class, 'edit'])->middleware('permission:caja.editar')->name('caja.edit');
    Route::put('/caja/{movimiento}',       [CajaController::class, 'update'])->middleware('permission:caja.editar')->name('caja.update');
    Route::delete('/caja/{movimiento}',    [CajaController::class, 'destroy'])->middleware('permission:caja.eliminar')->name('caja.destroy');

    // ── Entregas ─────────────────────────────────────────────────────────
    Route::get('/entregas',                  [EntregaController::class, 'index'])->middleware('permission:entregas.ver')->name('entregas.index');
    Route::get('/entregas/create',           [EntregaController::class, 'create'])->middleware('permission:entregas.crear')->name('entregas.create');
    Route::post('/entregas',                 [EntregaController::class, 'store'])->middleware('permission:entregas.crear')->name('entregas.store');
    Route::get('/entregas/{entrega}',        [EntregaController::class, 'show'])->middleware('permission:entregas.ver')->name('entregas.show');
    Route::get('/entregas/{entrega}/edit',   [EntregaController::class, 'edit'])->middleware('permission:entregas.editar')->name('entregas.edit');
    Route::put('/entregas/{entrega}',        [EntregaController::class, 'update'])->middleware('permission:entregas.editar')->name('entregas.update');
    Route::delete('/entregas/{entrega}',     [EntregaController::class, 'destroy'])->middleware('permission:entregas.eliminar')->name('entregas.destroy');
    Route::get('/entregas/{entrega}/acta',   [EntregaController::class, 'acta'])->middleware('permission:entregas.ver')->name('entregas.acta');
    Route::get('/reportes', [ReporteController::class, 'index'])
        ->middleware('permission:reportes.ver')
        ->name('reportes.index');

    // ── Planilla ─────────────────────────────────────────────────────────
    Route::get('/planilla',                    [PlanillaController::class, 'index'])->middleware('permission:planilla.ver')->name('planilla.index');
    Route::get('/planilla/create',             [PlanillaController::class, 'create'])->middleware('permission:planilla.crear')->name('planilla.create');
    Route::post('/planilla',                   [PlanillaController::class, 'store'])->middleware('permission:planilla.crear')->name('planilla.store');
    Route::get('/planilla/{planilla}',         [PlanillaController::class, 'show'])->middleware('permission:planilla.ver')->name('planilla.show');
    Route::patch('/planilla/{planilla}/pagar', [PlanillaController::class, 'pagar'])->middleware('permission:planilla.pagar')->name('planilla.pagar');
    Route::patch('/planilla/{planilla}/revertir', [PlanillaController::class, 'revertir'])->middleware('permission:planilla.pagar')->name('planilla.revertir');
    Route::delete('/planilla/{planilla}',      [PlanillaController::class, 'destroy'])->middleware('permission:planilla.eliminar')->name('planilla.destroy');
    Route::get('/configuracion', [ConfigController::class, 'index'])
        ->middleware('permission:configuracion.ver')
        ->name('configuracion.index');
    Route::put('/configuracion', [ConfigController::class, 'update'])
        ->middleware('permission:configuracion.editar')
        ->name('configuracion.update');
    // ── Usuarios (admin) ────────────────────────────────────────────────
    Route::get('/admin/usuarios',              [UsuarioController::class, 'index'])->middleware('permission:usuarios.ver')->name('usuarios.index');
    Route::get('/admin/usuarios/create',       [UsuarioController::class, 'create'])->middleware('permission:usuarios.crear')->name('usuarios.create');
    Route::post('/admin/usuarios',             [UsuarioController::class, 'store'])->middleware('permission:usuarios.crear')->name('usuarios.store');
    Route::get('/admin/usuarios/{usuario}/edit', [UsuarioController::class, 'edit'])->middleware('permission:usuarios.editar')->name('usuarios.edit');
    Route::put('/admin/usuarios/{usuario}',    [UsuarioController::class, 'update'])->middleware('permission:usuarios.editar')->name('usuarios.update');
    Route::delete('/admin/usuarios/{usuario}', [UsuarioController::class, 'destroy'])->middleware('permission:usuarios.eliminar')->name('usuarios.destroy');
    Route::patch('/admin/usuarios/{usuario}/toggle', [UsuarioController::class, 'toggleActivo'])->middleware('permission:usuarios.editar')->name('usuarios.toggle');
    Route::patch('/admin/usuarios/{usuario}/reset-password', [UsuarioController::class, 'resetPassword'])->middleware('permission:usuarios.editar')->name('usuarios.reset-password');

    // ── Asistencias ─────────────────────────────────────────────────────
    Route::get('/asistencias',         [AsistenciaController::class, 'index'])->middleware('permission:asistencias.ver|asistencias.gestionar')->name('asistencias.index');
    Route::get('/asistencias/marcar',  [AsistenciaController::class, 'marcar'])->middleware('permission:asistencias.registrar')->name('asistencias.marcar');
    Route::post('/asistencias/marcar', [AsistenciaController::class, 'registrar'])->middleware('permission:asistencias.registrar')->name('asistencias.registrar');
    Route::get('/asistencias/justificar',                              [AsistenciaController::class, 'crearJustificacion'])->middleware('permission:asistencias.registrar')->name('asistencias.justificar');
    Route::post('/asistencias/justificar',                             [AsistenciaController::class, 'guardarJustificacion'])->middleware('permission:asistencias.registrar')->name('asistencias.justificar.store');
    Route::patch('/asistencias/justificaciones/{justificacion}/aprobar', [AsistenciaController::class, 'aprobarJustificacion'])->middleware('permission:asistencias.gestionar')->name('asistencias.justificacion.aprobar');
    Route::patch('/asistencias/justificaciones/{justificacion}/rechazar', [AsistenciaController::class, 'rechazarJustificacion'])->middleware('permission:asistencias.gestionar')->name('asistencias.justificacion.rechazar');
    Route::get('/asistencias/config',  [AsistenciaController::class, 'configGeo'])->middleware('permission:asistencias.gestionar')->name('asistencias.config');
    Route::post('/asistencias/config', [AsistenciaController::class, 'guardarConfigGeo'])->middleware('permission:asistencias.gestionar')->name('asistencias.config.save');

    // ── Solicitudes internas ────────────────────────────────────────────
    Route::get('/solicitudes',                       [SolicitudController::class, 'index'])->middleware('permission:solicitudes.ver|solicitudes.gestionar')->name('solicitudes.index');
    Route::get('/solicitudes/create',                [SolicitudController::class, 'create'])->middleware('permission:solicitudes.crear')->name('solicitudes.create');
    Route::post('/solicitudes',                      [SolicitudController::class, 'store'])->middleware('permission:solicitudes.crear')->name('solicitudes.store');
    Route::get('/solicitudes/{solicitude}',          [SolicitudController::class, 'show'])->middleware('permission:solicitudes.ver|solicitudes.gestionar')->name('solicitudes.show');
    Route::patch('/solicitudes/{solicitude}/aprobar',[SolicitudController::class, 'aprobar'])->middleware('permission:solicitudes.gestionar')->name('solicitudes.aprobar');
    Route::patch('/solicitudes/{solicitude}/rechazar',[SolicitudController::class, 'rechazar'])->middleware('permission:solicitudes.gestionar')->name('solicitudes.rechazar');
    Route::patch('/solicitudes/{solicitude}/entregar',[SolicitudController::class, 'entregar'])->middleware('permission:solicitudes.gestionar')->name('solicitudes.entregar');
    Route::delete('/solicitudes/{solicitude}',       [SolicitudController::class, 'destroy'])->middleware('permission:solicitudes.ver|solicitudes.gestionar')->name('solicitudes.destroy');

    // ── Reportes Diarios ─────────────────────────────────────────────────
    Route::get('/reportes-diarios',                    [ReporteDiarioController::class, 'index'])->middleware('permission:reportes_diarios.ver|reportes_diarios.gestionar')->name('reportes_diarios.index');
    Route::get('/reportes-diarios/create',             [ReporteDiarioController::class, 'create'])->middleware('permission:reportes_diarios.crear')->name('reportes_diarios.create');
    Route::post('/reportes-diarios',                   [ReporteDiarioController::class, 'store'])->middleware('permission:reportes_diarios.crear')->name('reportes_diarios.store');
    Route::get('/reportes-diarios/{reportes_diario}',  [ReporteDiarioController::class, 'show'])->middleware('permission:reportes_diarios.ver|reportes_diarios.gestionar')->name('reportes_diarios.show');
    Route::get('/reportes-diarios/{reportes_diario}/adjunto', [ReporteDiarioController::class, 'descargarAdjunto'])->middleware('permission:reportes_diarios.ver|reportes_diarios.gestionar')->name('reportes_diarios.adjunto');
    Route::delete('/reportes-diarios/{reportes_diario}',[ReporteDiarioController::class, 'destroy'])->middleware('permission:reportes_diarios.ver|reportes_diarios.gestionar')->name('reportes_diarios.destroy');

    // ── Roles ────────────────────────────────────────────────────────────
    Route::get('/admin/roles',               [RolController::class, 'index'])->middleware('permission:usuarios.ver')->name('roles.index');
    Route::get('/admin/roles/create',        [RolController::class, 'create'])->middleware('permission:usuarios.crear')->name('roles.create');
    Route::post('/admin/roles',              [RolController::class, 'store'])->middleware('permission:usuarios.crear')->name('roles.store');
    Route::get('/admin/roles/{rol}/edit',    [RolController::class, 'edit'])->middleware('permission:usuarios.editar')->name('roles.edit');
    Route::put('/admin/roles/{rol}',         [RolController::class, 'update'])->middleware('permission:usuarios.editar')->name('roles.update');
    Route::delete('/admin/roles/{rol}',      [RolController::class, 'destroy'])->middleware('permission:usuarios.eliminar')->name('roles.destroy');
});

// ── Estado de la API SUNAT externa ──────────────────────────────────
Route::middleware(['auth'])->get('/api/sunat-api-status', function () {
    $service = app(\App\Services\SunatService::class);
    if (!$service->estaConfigurada()) {
        return response()->json(['connected' => false, 'error' => 'SUNAT_API_TOKEN no configurado en .env']);
    }
    $ok = $service->ping();
    return response()->json([
        'connected'  => $ok,
        'company_id' => config('services.sunat_api.company_id'),
        'branch_id'  => config('services.sunat_api.branch_id'),
        'error'      => $ok ? null : 'La API no responde. Verifica que esté corriendo en ' . config('services.sunat_api.url'),
    ]);
});

// ── Proxy consulta DNI / RUC (token queda server-side) ──────────────
Route::middleware(['auth'])->get('/api/consulta-documento', DocumentoLookupController::class)
    ->name('api.consulta-documento');

// Misma consulta, accesible sin login (formulario público de inscripción a eventos),
// limitada por IP para no exponer la cuota de la API externa a cualquiera.
Route::middleware(['throttle:15,1'])->get('/api/consulta-documento-publico', DocumentoLookupController::class)
    ->name('api.consulta-documento-publico');

// ── Eventos (ferias / campañas) y sus leads ──────────────────────────
// IMPORTANTE: rutas con segmento fijo (create) ANTES de wildcards ({evento})
Route::middleware(['auth', 'verified'])->group(function () {

    Route::get('/eventos', [EventoController::class, 'index'])
        ->middleware('permission:eventos.ver')
        ->name('eventos.index');

    Route::get('/eventos/create', [EventoController::class, 'create'])
        ->middleware('permission:eventos.crear')
        ->name('eventos.create');

    Route::post('/eventos', [EventoController::class, 'store'])
        ->middleware('permission:eventos.crear')
        ->name('eventos.store');

    Route::get('/eventos/{evento}', [EventoController::class, 'show'])
        ->middleware('permission:eventos.ver')
        ->name('eventos.show');

    Route::get('/eventos/{evento}/edit', [EventoController::class, 'edit'])
        ->middleware('permission:eventos.editar')
        ->name('eventos.edit');

    Route::put('/eventos/{evento}', [EventoController::class, 'update'])
        ->middleware('permission:eventos.editar')
        ->name('eventos.update');

    Route::delete('/eventos/{evento}', [EventoController::class, 'destroy'])
        ->middleware('permission:eventos.eliminar')
        ->name('eventos.destroy');

    // ── Leads capturados en el evento ────────────────────────────────
    Route::post('/eventos/{evento}/leads', [EventLeadController::class, 'store'])
        ->middleware('permission:eventos.crear')
        ->name('eventos.leads.store');

    Route::get('/eventos/{evento}/leads/{lead}/edit', [EventLeadController::class, 'edit'])
        ->middleware('permission:eventos.editar')
        ->name('eventos.leads.edit');

    Route::put('/eventos/{evento}/leads/{lead}', [EventLeadController::class, 'update'])
        ->middleware('permission:eventos.editar')
        ->name('eventos.leads.update');

    Route::delete('/eventos/{evento}/leads/{lead}', [EventLeadController::class, 'destroy'])
        ->middleware('permission:eventos.eliminar')
        ->name('eventos.leads.destroy');

    Route::post('/eventos/{evento}/leads/{lead}/convertir', [EventLeadController::class, 'convertir'])
        ->middleware('permission:eventos.editar')
        ->name('eventos.leads.convertir');

    // ── Asistentes del evento (registro manual + check-in) ───────────
    Route::post('/eventos/{evento}/asistentes', [EventAttendeeController::class, 'storeManual'])
        ->middleware('permission:eventos.crear')
        ->name('eventos.asistentes.store');

    Route::get('/eventos/{evento}/asistentes/plantilla', [EventAttendeeImportController::class, 'plantilla'])
        ->middleware('permission:eventos.crear')
        ->name('eventos.asistentes.plantilla');

    Route::get('/eventos/{evento}/asistentes/importar', [EventAttendeeImportController::class, 'crear'])
        ->middleware('permission:eventos.crear')
        ->name('eventos.asistentes.importar');

    Route::post('/eventos/{evento}/asistentes/importar', [EventAttendeeImportController::class, 'importar'])
        ->middleware('permission:eventos.crear')
        ->name('eventos.asistentes.importar.store');

    Route::get('/eventos/{evento}/asistentes/exportar', [EventAttendeeController::class, 'exportar'])
        ->middleware('permission:eventos.ver')
        ->name('eventos.asistentes.exportar');

    Route::delete('/eventos/{evento}/asistentes/{asistente}', [EventAttendeeController::class, 'destroy'])
        ->middleware('permission:eventos.eliminar')
        ->name('eventos.asistentes.destroy');

    Route::get('/eventos/{evento}/checkin', [EventCheckinController::class, 'show'])
        ->middleware('permission:eventos.checkin')
        ->name('eventos.checkin');

    Route::post('/eventos/{evento}/checkin/scan', [EventCheckinController::class, 'scan'])
        ->middleware('permission:eventos.checkin')
        ->name('eventos.checkin.scan');

    // ── Pago de la inscripcion por equipo (revision del staff) ───────
    Route::get('/eventos/{evento}/equipos/{equipo}/comprobante', [EventTeamPagoController::class, 'verComprobante'])
        ->middleware('permission:eventos.ver')
        ->name('eventos.equipos.comprobante');

    Route::patch('/eventos/{evento}/equipos/{equipo}/pago', [EventTeamPagoController::class, 'update'])
        ->middleware('permission:eventos.editar')
        ->name('eventos.equipos.pago');

});

// ── Inscripción pública a eventos (sin login) ────────────────────────
Route::get('/eventos/{evento}/vista', [EventAttendeeController::class, 'showcase'])
    ->name('eventos.showcase');

Route::get('/eventos/{evento}/inscripcion', [EventAttendeeController::class, 'create'])
    ->name('eventos.inscripcion.create');

Route::post('/eventos/{evento}/inscripcion', [EventAttendeeController::class, 'store'])
    ->name('eventos.inscripcion.store');

Route::middleware(['throttle:10,1'])
    ->post('/eventos/{evento}/inscripcion-equipo', [EventAttendeeController::class, 'storeEquipo'])
    ->name('eventos.inscripcion.equipo.store');

Route::middleware(['throttle:10,1'])
    ->post('/eventos/{evento}/equipos/{equipo:token}/comprobante', [EventTeamPagoController::class, 'storeComprobante'])
    ->name('eventos.inscripcion.equipo.comprobante');

Route::get('/eventos/{evento}/inscripcion/{asistente:qr_token}', [EventAttendeeController::class, 'ticket'])
    ->name('eventos.inscripcion.ticket');

// ── Solicitud de demo pública (formulario de la landing) ─────────────
Route::middleware(['throttle:10,1'])->post('/landing/solicitud-demo', [LeadController::class, 'storePublico'])
    ->name('landing.solicitud-demo');

require __DIR__.'/auth.php';
