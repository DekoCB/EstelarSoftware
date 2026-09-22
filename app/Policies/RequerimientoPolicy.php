<?php

namespace App\Policies;

use App\Models\Requirement;
use App\Models\User;

class RequerimientoPolicy
{
    public function viewAny(User $user): bool  { return $user->can('requerimientos.ver'); }
    public function view(User $user, Requirement $req): bool { return $user->can('requerimientos.ver'); }
    public function create(User $user): bool   { return $user->can('requerimientos.crear'); }
    public function update(User $user, Requirement $req): bool { return $user->can('requerimientos.editar'); }
    public function delete(User $user, Requirement $req): bool { return $user->can('requerimientos.editar'); }
}
