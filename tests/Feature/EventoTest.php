<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EventoTest extends TestCase
{
    use RefreshDatabase;

    private function usuarioConPermisos(array $permisos): User
    {
        foreach ($permisos as $permiso) {
            Permission::firstOrCreate(['name' => $permiso, 'guard_name' => 'web']);
        }

        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $user = User::factory()->create();
        $user->givePermissionTo($permisos);

        return $user;
    }

    public function test_usuario_autorizado_puede_crear_un_evento(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);

        $response = $this->actingAs($user)->post('/eventos', [
            'nombre'       => 'Feria Tecnológica Huancayo 2026',
            'fecha_inicio' => '2026-09-01',
            'estado'       => 'planificado',
            'lugar'        => 'Cámara de Comercio',
            'latitud'      => -12.0664,
            'longitud'     => -75.2049,
        ]);

        $evento = Event::first();

        $response->assertRedirect(route('eventos.show', $evento));
        $this->assertDatabaseHas('events', [
            'nombre' => 'Feria Tecnológica Huancayo 2026',
        ]);
    }

    public function test_usuario_sin_permiso_no_puede_crear_evento(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver']);

        $response = $this->actingAs($user)->post('/eventos', [
            'nombre'       => 'Evento no autorizado',
            'fecha_inicio' => '2026-09-01',
            'estado'       => 'planificado',
        ]);

        $response->assertForbidden();
        $this->assertDatabaseMissing('events', ['nombre' => 'Evento no autorizado']);
    }

    public function test_se_puede_registrar_un_lead_con_ubicacion_precisa_en_un_evento(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);
        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $user->id,
        ]);

        $response = $this->actingAs($user)->post("/eventos/{$evento->id}/leads", [
            'tipo_documento'   => 'DNI',
            'numero_documento' => '74093841',
            'nombres'          => 'Jose Miguel Llacza Isidro',
            'rubro'            => 'Comercio',
            'estado'           => 'nuevo',
            'latitud'          => -12.0664,
            'longitud'         => -75.2049,
            'precision_metros' => 12.5,
        ]);

        $response->assertRedirect(route('eventos.show', $evento));
        $this->assertDatabaseHas('event_leads', [
            'event_id'         => $evento->id,
            'numero_documento' => '74093841',
            'nombres'          => 'Jose Miguel Llacza Isidro',
            'rubro'            => 'Comercio',
        ]);
    }

    public function test_se_puede_registrar_un_rubro_libre_cuando_no_esta_en_la_lista_precargada(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);
        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $user->id,
        ]);

        $response = $this->actingAs($user)->post("/eventos/{$evento->id}/leads", [
            'tipo_documento' => 'DNI',
            'nombres'        => 'Rosa Quispe',
            'rubro'          => 'Artesanía textil andina',
            'estado'         => 'nuevo',
        ]);

        $response->assertRedirect(route('eventos.show', $evento));
        $this->assertDatabaseHas('event_leads', [
            'nombres' => 'Rosa Quispe',
            'rubro'   => 'Artesanía textil andina',
        ]);
    }

    public function test_un_lead_con_documento_se_puede_convertir_en_cliente(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear', 'eventos.editar']);
        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $user->id,
        ]);

        $lead = $evento->leads()->create([
            'tipo_documento'   => 'DNI',
            'numero_documento' => '74093841',
            'nombres'          => 'Jose Miguel Llacza Isidro',
            'estado'           => 'nuevo',
            'created_by'       => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('eventos.leads.convertir', [$evento, $lead]));

        $response->assertRedirect();
        $lead->refresh();

        $this->assertTrue($lead->convertido());
        $this->assertDatabaseHas('clients', [
            'numero_documento' => '74093841',
            'razon_social'     => 'Jose Miguel Llacza Isidro',
            'estado'           => 'prospecto',
        ]);
    }

    public function test_la_pagina_de_detalle_del_evento_renderiza_con_leads_y_modal(): void
    {
        $this->seed(\Database\Seeders\RolesPermissionsSeeder::class);

        $user = User::factory()->create();
        $user->assignRole('administrativo');

        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $user->id,
        ]);

        $evento->leads()->create([
            'tipo_documento'   => 'DNI',
            'numero_documento' => '74093841',
            'nombres'          => 'Jose Miguel Llacza Isidro',
            'estado'           => 'nuevo',
            'latitud'          => -12.0664,
            'longitud'         => -75.2049,
            'created_by'       => $user->id,
        ]);

        $response = $this->actingAs($user)->get(route('eventos.show', $evento));

        $response->assertOk();
        $response->assertSee('Jose Miguel Llacza Isidro');
    }

    public function test_usuario_sin_ver_todos_solo_ve_sus_propios_leads_en_el_evento(): void
    {
        $this->seed(\Database\Seeders\RolesPermissionsSeeder::class);

        $vendedorA = User::factory()->create();
        $vendedorA->assignRole('ventas');
        $vendedorB = User::factory()->create();
        $vendedorB->assignRole('ventas');

        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $vendedorA->id,
        ]);

        $evento->leads()->create([
            'tipo_documento' => 'DNI', 'nombres' => 'Lead de A', 'estado' => 'nuevo', 'created_by' => $vendedorA->id,
        ]);
        $evento->leads()->create([
            'tipo_documento' => 'DNI', 'nombres' => 'Lead de B', 'estado' => 'nuevo', 'created_by' => $vendedorB->id,
        ]);

        $response = $this->actingAs($vendedorA)->get(route('eventos.show', $evento));

        $response->assertOk();
        $response->assertSee('Lead de A');
        $response->assertDontSee('Lead de B');
    }

    public function test_usuario_con_ver_todos_ve_los_leads_de_todos_los_usuarios(): void
    {
        $this->seed(\Database\Seeders\RolesPermissionsSeeder::class);

        $supervisor = User::factory()->create();
        $supervisor->assignRole('administrativo');
        $vendedor = User::factory()->create();
        $vendedor->assignRole('ventas');

        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $supervisor->id,
        ]);

        $evento->leads()->create([
            'tipo_documento' => 'DNI', 'nombres' => 'Lead del vendedor', 'estado' => 'nuevo', 'created_by' => $vendedor->id,
        ]);

        $response = $this->actingAs($supervisor)->get(route('eventos.show', $evento));

        $response->assertOk();
        $response->assertSee('Lead del vendedor');
    }

    public function test_usuario_sin_ver_todos_no_puede_editar_lead_de_otro_usuario(): void
    {
        $vendedorA = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear', 'eventos.editar']);
        $vendedorB = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear', 'eventos.editar']);

        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $vendedorA->id,
        ]);

        $lead = $evento->leads()->create([
            'tipo_documento' => 'DNI', 'nombres' => 'Lead de B', 'estado' => 'nuevo', 'created_by' => $vendedorB->id,
        ]);

        $this->actingAs($vendedorA)->get(route('eventos.leads.edit', [$evento, $lead]))->assertForbidden();

        $this->actingAs($vendedorA)->put(route('eventos.leads.update', [$evento, $lead]), [
            'tipo_documento' => 'DNI', 'nombres' => 'Intento de edición', 'estado' => 'nuevo',
        ])->assertForbidden();

        $this->actingAs($vendedorA)->delete(route('eventos.leads.destroy', [$evento, $lead]))->assertForbidden();

        $this->assertDatabaseHas('event_leads', ['id' => $lead->id, 'nombres' => 'Lead de B']);
    }

    public function test_convertir_lead_sin_numero_documento_falla_con_mensaje(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear', 'eventos.editar']);
        $evento = Event::create([
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
            'created_by'   => $user->id,
        ]);

        $lead = $evento->leads()->create([
            'tipo_documento' => 'DNI',
            'nombres'        => 'Sin Documento',
            'estado'         => 'nuevo',
            'created_by'     => $user->id,
        ]);

        $response = $this->actingAs($user)->post(route('eventos.leads.convertir', [$evento, $lead]));

        $response->assertSessionHas('error');
        $this->assertFalse($lead->fresh()->convertido());
    }
}
