<?php

namespace App\Policies;

use App\Models\Client;
use App\Models\User;

class ClientePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('clientes.ver');
    }

    public function view(User $user, Client $client): bool
    {
        return $user->can('clientes.ver');
    }

    public function create(User $user): bool
    {
        return $user->can('clientes.crear');
    }

    public function update(User $user, Client $client): bool
    {
        return $user->can('clientes.editar');
    }

    public function delete(User $user, Client $client): bool
    {
        return $user->can('clientes.eliminar');
    }

    public function restore(User $user, Client $client): bool
    {
        return $user->can('clientes.eliminar');
    }
}
