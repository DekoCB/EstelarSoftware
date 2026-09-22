<?php

namespace App\Policies;

use App\Models\ReporteDiario;
use App\Models\User;

class ReporteDiarioPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyPermission(['reportes_diarios.ver', 'reportes_diarios.gestionar']);
    }

    public function view(User $user, ReporteDiario $reporte): bool
    {
        if ($user->hasPermissionTo('reportes_diarios.gestionar')) {
            return true;
        }
        return $user->hasPermissionTo('reportes_diarios.ver') && $reporte->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('reportes_diarios.crear');
    }

    public function delete(User $user, ReporteDiario $reporte): bool
    {
        if ($user->hasPermissionTo('reportes_diarios.gestionar')) {
            return true;
        }
        return $reporte->user_id === $user->id;
    }
}
