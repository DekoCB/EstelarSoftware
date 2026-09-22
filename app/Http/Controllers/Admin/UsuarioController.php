<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreUsuarioRequest;
use App\Http\Requests\Admin\UpdateUsuarioRequest;
use App\Models\User;
use App\Support\PermissionGroups;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index()
    {
        $usuarios = User::with('roles')
            ->orderByDesc('activo')
            ->orderBy('name')
            ->get();

        return view('admin.usuarios.index', compact('usuarios'));
    }

    public function create()
    {
        $roles = Role::orderBy('name')->get();
        return view('admin.usuarios.create', compact('roles'));
    }

    public function store(StoreUsuarioRequest $request)
    {
        $data = $request->validated();
        $rol  = $data['rol'];
        unset($data['rol'], $data['password_confirmation']);

        $data['password']          = Hash::make($data['password']);
        $data['email_verified_at'] = now();
        $data['activo']            = true;

        $usuario = User::create($data);
        $usuario->assignRole($rol);

        return redirect()
            ->route('usuarios.index')
            ->with('success', "Usuario {$usuario->name} creado correctamente.");
    }

    public function edit(User $usuario)
    {
        $roles      = Role::orderBy('name')->get();
        $grupos     = PermissionGroups::grupos();
        $existentes = Permission::pluck('name')->toArray();

        // Permisos del rol actual
        $permisosRol = $usuario->roles->first()?->permissions->pluck('name')->toArray() ?? [];

        // Permisos directos (asignados al usuario, no via rol)
        $permisosDirect = $usuario->getDirectPermissions()->pluck('name')->toArray();

        return view('admin.usuarios.edit', compact(
            'usuario', 'roles', 'grupos', 'existentes', 'permisosRol', 'permisosDirect'
        ));
    }

    public function update(UpdateUsuarioRequest $request, User $usuario)
    {
        $data = $request->validated();
        $rol  = $data['rol'];
        unset($data['rol'], $data['password_confirmation']);

        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        // Permisos directos enviados (no incluye los del rol)
        $permisosDirect = $request->input('permisos_directos', []);

        $usuario->update($data);
        $usuario->syncRoles([$rol]);
        $usuario->syncPermissions($permisosDirect);  // permisos adicionales directos

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('usuarios.index')
            ->with('success', "Usuario {$usuario->name} actualizado.");
    }

    public function destroy(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes eliminar tu propio usuario.');
        }

        $usuario->update(['activo' => false]);
        return back()->with('success', "Usuario {$usuario->name} desactivado.");
    }

    public function toggleActivo(User $usuario)
    {
        if ($usuario->id === auth()->id()) {
            return back()->with('error', 'No puedes desactivarte a ti mismo.');
        }

        $usuario->update(['activo' => !$usuario->activo]);
        $estado = $usuario->activo ? 'activado' : 'desactivado';

        return back()->with('success', "Usuario {$usuario->name} {$estado}.");
    }

    public function resetPassword(User $usuario)
    {
        $nueva = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 10);
        $usuario->update(['password' => Hash::make($nueva)]);

        return back()->with('success', "Nueva contraseña temporal: <strong class=\"font-mono\">{$nueva}</strong> — entregala al usuario.");
    }
}
