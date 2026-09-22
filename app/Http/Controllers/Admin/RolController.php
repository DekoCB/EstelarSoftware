<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\PermissionGroups;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    public function index()
    {
        $roles = Role::with(['permissions', 'users'])->orderBy('name')->get();
        return view('admin.roles.index', compact('roles'));
    }

    public function create()
    {
        $grupos      = PermissionGroups::grupos();
        $existentes  = Permission::pluck('name')->toArray();

        return view('admin.roles.create', compact('grupos', 'existentes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => ['required', 'string', 'max:60', 'unique:roles,name'],
            'permisos'    => ['nullable', 'array'],
            'permisos.*'  => ['string', 'exists:permissions,name'],
        ]);

        $rol = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
        $rol->syncPermissions($data['permisos'] ?? []);

        return redirect()
            ->route('roles.index')
            ->with('success', "Rol \"{$rol->name}\" creado con " . count($data['permisos'] ?? []) . " permisos.");
    }

    public function edit(Role $rol)
    {
        $grupos     = PermissionGroups::grupos();
        $existentes = Permission::pluck('name')->toArray();
        $activos    = $rol->permissions->pluck('name')->toArray();

        return view('admin.roles.edit', compact('rol', 'grupos', 'existentes', 'activos'));
    }

    public function update(Request $request, Role $rol)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:60', "unique:roles,name,{$rol->id}"],
            'permisos'   => ['nullable', 'array'],
            'permisos.*' => ['string', 'exists:permissions,name'],
        ]);

        $rol->update(['name' => $data['name']]);
        $rol->syncPermissions($data['permisos'] ?? []);

        // Refrescar caché de Spatie
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()
            ->route('roles.index')
            ->with('success', "Rol \"{$rol->name}\" actualizado.");
    }

    public function destroy(Role $rol)
    {
        if ($rol->users()->count() > 0) {
            return back()->with('error', "No se puede eliminar: el rol \"{$rol->name}\" tiene {$rol->users()->count()} usuario(s) asignado(s).");
        }

        $nombre = $rol->name;
        $rol->delete();

        return redirect()->route('roles.index')->with('success', "Rol \"{$nombre}\" eliminado.");
    }
}
