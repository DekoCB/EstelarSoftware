# CLAUDE.md — Estelar Software Empresarial ERP

Este archivo es leído automáticamente por Claude Code al abrir este repositorio.

## Proyecto
ERP para empresa de software en Huancayo, Perú. Laravel 11 + MySQL 8 + Livewire 3 + Tailwind CSS. Incluye facturación electrónica SUNAT (boletas y facturas vía Nubefact OSE).

## Stack
- **Backend**: Laravel 11, PHP 8.2+, MySQL 8, Eloquent ORM
- **Frontend**: Blade, Livewire 3, Alpine.js, Tailwind CSS 3, ApexCharts
- **Permisos**: spatie/laravel-permission (patrón `modulo.accion`)
- **SUNAT**: greenter/greenter + Nubefact API REST
- **PDFs**: barryvdh/laravel-dompdf
- **Excel**: maatwebsite/excel
- **Colas**: Laravel Queues (database driver)

## Convenciones de código

### Nombres
- Tablas: snake_case plural español → `clients`, `projects`, `cash_movements`
- Modelos: PascalCase singular inglés → `Client`, `Project`, `Invoice`
- Controllers: resource controllers, un controller por módulo
- Livewire: un componente por funcionalidad compleja
- Services: lógica de negocio compleja (especialmente SUNAT)
- Jobs: operaciones asíncronas (email, emisión SUNAT)

### Estructura de rutas
```
/admin/*          → AdminController, DashboardController
/clientes/*       → ClienteController (resource)
/proyectos/*      → ProyectoController (resource)
/requerimientos/* → RequerimientoController (resource)
/ventas/*         → CotizacionController (resource)
/facturacion/*    → FacturaController (resource)
/caja/*           → CajaController (resource)
/entregas/*       → EntregaController (resource)
/configuracion/*  → ConfigController
```

### Permisos en rutas
```php
// Siempre proteger con middleware
Route::middleware(['auth', 'verified', 'permission:modulo.accion'])->group(fn() => ...);
```

### Permisos en Blade
```blade
@can('modulo.accion') ... @endcan
@role('administrativo') ... @endrole
```

## Reglas importantes

1. **Spatie siempre activo**: Cada ruta nueva debe tener su middleware de permiso. Sin excepción.

2. **Transacciones en facturación**: Todo flujo SUNAT dentro de `DB::transaction()`.
   ```php
   DB::transaction(function() {
       $correlativo = SerieCorrelativo::lockForUpdate()->...;
       // generar XML, firmar, enviar OSE, guardar CDR
   });
   ```

3. **Soft deletes obligatorios** en: `Client`, `Project`, `Invoice`, `Quote`. Nunca borrar registros de facturación.

4. **Eager loading siempre**:
   ```php
   // MAL
   Project::all();
   // BIEN
   Project::with(['client', 'requirements', 'responsable'])->paginate(15);
   ```

5. **Caché de KPIs**:
   ```php
   $kpis = Cache::remember('dashboard_kpis', 300, fn() => [...]);
   ```

6. **Form Requests para validación**: Nunca validar en el controller directamente.

7. **Storage privado para archivos sensibles**: XMLs, CDRs, certificados PFX en `storage/app/` (nunca en `public/`).

8. **IGV desde config**: Nunca hardcodear `0.18` en el código. Usar `EmpresaConfig::first()->igv_porcentaje / 100`.

## Módulos del sistema
1. Administrativo (dashboard + config)
2. Clientes (CRM básico)
3. Proyectos (ciclo de vida)
4. Requerimientos (por proyecto)
5. Ventas / Cotizaciones
6. Facturación SUNAT (boletas + facturas electrónicas)
7. Caja (ingresos + egresos)
8. Entrega de proyectos
9. Roles y permisos

## Tablas principales
`users`, `roles`, `permissions`, `clients`, `projects`, `project_phases`, `requirements`,
`quotes`, `quote_items`, `invoices`, `invoice_items`, `serie_correlativos`,
`empresa_config`, `cash_movements`, `project_deliveries`, `audit_logs`

## Variables de entorno necesarias
```
NUBEFACT_TOKEN=
NUBEFACT_URL=https://api.nubefact.com/api/v1
SUNAT_RUC_EMPRESA=
CERTIFICADO_PFX_PATH=storage/app/certs/certificado.pfx
CERTIFICADO_PFX_CLAVE=
```

## Sprint actual
Ver `SOFTTECH_ERP_CONTEXT.md` para el plan de sprints completo.

## Al crear cualquier feature, seguir este orden:
1. Migración
2. Modelo (con `$fillable`, relaciones, `SoftDeletes` si aplica)
3. Policy
4. Form Request
5. Controller (resource)
6. Rutas con middleware de permisos
7. Vistas Blade (o componente Livewire si es interactivo)
8. Test básico Feature

---
*Estelar Software Empresarial ERP — Huancayo, Perú*

