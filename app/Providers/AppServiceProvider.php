<?php

namespace App\Providers;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\LeadMeeting;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuotePayment;
use App\Models\Requirement;
use App\Models\ReporteDiario;
use App\Models\Solicitud;
use App\Models\Sprint;
use App\Policies\AsistenciaPolicy;
use App\Policies\ClientePolicy;
use App\Policies\ProyectoPolicy;
use App\Policies\ReporteDiarioPolicy;
use App\Policies\RequerimientoPolicy;
use App\Policies\SolicitudPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // Policies
        Gate::policy(Client::class,        ClientePolicy::class);
        Gate::policy(Project::class,       ProyectoPolicy::class);
        Gate::policy(Requirement::class,   RequerimientoPolicy::class);
        Gate::policy(Solicitud::class,     SolicitudPolicy::class);
        Gate::policy(ReporteDiario::class, ReporteDiarioPolicy::class);
        Gate::define('asistencias.ver',       [AsistenciaPolicy::class, 'ver']);
        Gate::define('asistencias.registrar', [AsistenciaPolicy::class, 'registrar']);
        Gate::define('asistencias.gestionar', [AsistenciaPolicy::class, 'gestionar']);

        // Route model bindings en español
        Route::model('cliente',       Client::class);
        Route::model('proyecto',      Project::class);
        Route::model('requerimiento', Requirement::class);
        Route::model('sprint',        Sprint::class);
        Route::model('cotizacion',    Quote::class);
        Route::bind('factura', fn($value) => Invoice::withTrashed()->findOrFail($value));
        Route::model('pago',          QuotePayment::class);
        Route::model('reunion',       LeadMeeting::class);
    }
}
