<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProyectoPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->canAny(['proyectos.ver', 'proyectos.ver_asignados']);
    }

    public function view(User $user, Project $project): bool
    {
        if ($user->can('proyectos.ver')) return true;
        // Practicante: solo ve proyectos donde es responsable
        return $user->can('proyectos.ver_asignados')
            && $project->responsible_user_id === $user->id;
    }

    public function create(User $user): bool   { return $user->can('proyectos.crear'); }
    public function update(User $user, Project $project): bool { return $user->can('proyectos.editar'); }
    public function delete(User $user, Project $project): bool { return $user->can('proyectos.eliminar'); }
}
