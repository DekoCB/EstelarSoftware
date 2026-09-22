<?php

namespace App\Support;

class PermissionGroups
{
    // Agrupación de permisos por módulo para la UI de roles/usuarios
    public static function grupos(): array
    {
        return [
            'Clientes'       => ['clientes.ver', 'clientes.crear', 'clientes.editar', 'clientes.eliminar'],
            'Leads'          => ['leads.ver', 'leads.crear', 'leads.editar', 'leads.eliminar'],
            'Proyectos'      => ['proyectos.ver', 'proyectos.ver_asignados', 'proyectos.crear', 'proyectos.editar', 'proyectos.eliminar'],
            'Requerimientos' => ['requerimientos.ver', 'requerimientos.crear', 'requerimientos.editar'],
            'Sprints'        => ['sprints.ver', 'sprints.gestionar', 'sprints.daily'],
            'Cotizaciones'   => ['cotizaciones.ver', 'cotizaciones.crear', 'cotizaciones.editar', 'cotizaciones.eliminar', 'cotizaciones.aprobar', 'cotizaciones.pdf'],
            'Facturación'    => ['facturacion.ver', 'facturacion.emitir', 'facturacion.anular'],
            'Caja'           => ['caja.ver', 'caja.crear', 'caja.editar', 'caja.eliminar'],
            'Entregas'       => ['entregas.ver', 'entregas.crear', 'entregas.editar', 'entregas.eliminar'],
            'Planilla'       => ['planilla.ver', 'planilla.crear', 'planilla.pagar', 'planilla.eliminar'],
            'Eventos'        => ['eventos.ver', 'eventos.crear', 'eventos.editar', 'eventos.eliminar', 'eventos.ver_todos', 'eventos.checkin'],
            'Asistencias'    => ['asistencias.ver', 'asistencias.registrar', 'asistencias.gestionar'],
            'Solicitudes'    => ['solicitudes.ver', 'solicitudes.crear', 'solicitudes.gestionar'],
            'Reportes'       => ['reportes.ver', 'reportes.exportar'],
            'Configuración'  => ['configuracion.ver', 'configuracion.editar'],
            'Usuarios'       => ['usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar'],
        ];
    }

    // Etiqueta legible de cada permiso
    public static function etiquetas(): array
    {
        return [
            'ver'           => 'Ver',
            'crear'         => 'Crear',
            'editar'        => 'Editar',
            'eliminar'      => 'Eliminar',
            'ver_asignados' => 'Ver asignados',
            'aprobar'       => 'Aprobar',
            'pdf'           => 'Descargar PDF',
            'emitir'        => 'Emitir',
            'anular'        => 'Anular',
            'registrar'     => 'Registrar',
            'exportar'      => 'Exportar',
            'gestionar'     => 'Gestionar',
            'daily'         => 'Daily report',
            'pagar'         => 'Pagar/revertir',
            'ver_todos'     => 'Ver todos',
            'checkin'       => 'Check-in (escanear QR)',
        ];
    }

    public static function etiqueta(string $permiso): string
    {
        $accion = last(explode('.', $permiso));
        return self::etiquetas()[$accion] ?? ucfirst($accion);
    }
}
