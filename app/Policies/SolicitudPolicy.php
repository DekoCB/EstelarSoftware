<?php

namespace App\Policies;

use App\Models\Solicitud;
use App\Models\User;

class SolicitudPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('solicitudes.ver') || $user->hasPermissionTo('solicitudes.gestionar');
    }

    public function view(User $user, Solicitud $solicitud): bool
    {
        if ($user->hasPermissionTo('solicitudes.gestionar')) return true;
        return $solicitud->user_id === $user->id;
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('solicitudes.crear');
    }

    public function gestionar(User $user): bool
    {
        return $user->hasPermissionTo('solicitudes.gestionar');
    }
}
