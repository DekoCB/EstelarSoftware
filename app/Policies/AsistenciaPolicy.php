<?php

namespace App\Policies;

use App\Models\User;

class AsistenciaPolicy
{
    public function ver(User $user): bool
    {
        return $user->hasAnyPermission(['asistencias.ver', 'asistencias.gestionar']);
    }

    public function registrar(User $user): bool
    {
        return $user->hasPermissionTo('asistencias.registrar');
    }

    public function gestionar(User $user): bool
    {
        return $user->hasPermissionTo('asistencias.gestionar');
    }
}
