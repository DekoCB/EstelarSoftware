<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EventAttendeeTest extends TestCase
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

    private function crearEvento(): Event
    {
        return Event::create([
            'nombre'       => 'Empresarios del 2030',
            'fecha_inicio' => '2026-08-27',
            'estado'       => 'planificado',
            'lugar'        => 'Auditorio Mayor Cámara de Comercio',
        ]);
    }

    public function test_cualquier_persona_sin_login_puede_inscribirse_y_recibe_su_ticket(): void
    {
        $evento = $this->crearEvento();

        $response = $this->post(route('eventos.inscripcion.store', $evento), [
            'nombres'          => 'Carlos Mayta',
            'empresa'          => 'Estelar',
            'tipo_documento'   => 'DNI',
            'numero_documento' => '12345678',
        ]);

        $asistente = EventAttendee::first();

        $response->assertRedirect(route('eventos.inscripcion.ticket', [$evento, $asistente]));
        $this->assertDatabaseHas('event_attendees', [
            'event_id' => $evento->id,
            'nombres'  => 'Carlos Mayta',
            'estado'   => 'registrado',
        ]);
        $this->assertNotEmpty($asistente->qr_token);
        $this->assertNotEmpty($asistente->codigo);

        $this->get(route('eventos.inscripcion.ticket', [$evento, $asistente]))
            ->assertOk()
            ->assertSee('Carlos Mayta')
            ->assertSee($asistente->codigo);
    }

    public function test_se_guarda_la_direccion_cuando_se_inscribe_con_ruc(): void
    {
        $evento = $this->crearEvento();

        $response = $this->post(route('eventos.inscripcion.store', $evento), [
            'nombres'          => 'Contacto empresa',
            'empresa'          => 'Expreso Lobato SAC',
            'tipo_documento'   => 'RUC',
            'numero_documento' => '20132757187',
            'direccion'        => 'AV. 28 DE JULIO NRO 2101',
        ]);

        $asistente = EventAttendee::first();
        $response->assertRedirect(route('eventos.inscripcion.ticket', [$evento, $asistente]));
        $this->assertDatabaseHas('event_attendees', [
            'empresa'   => 'Expreso Lobato SAC',
            'direccion' => 'AV. 28 DE JULIO NRO 2101',
        ]);
    }

    public function test_no_se_puede_inscribir_dos_veces_con_el_mismo_documento_en_el_mismo_evento(): void
    {
        $evento = $this->crearEvento();

        $evento->asistentes()->create([
            'nombres'          => 'Primera inscripción',
            'numero_documento' => '12345678',
            'codigo'           => 'EV1-000001',
            'qr_token'         => (string) \Illuminate\Support\Str::uuid(),
            'estado'           => 'registrado',
        ]);

        $response = $this->post(route('eventos.inscripcion.store', $evento), [
            'nombres'          => 'Segundo intento',
            'numero_documento' => '12345678',
        ]);

        $response->assertSessionHasErrors('numero_documento');
        $this->assertDatabaseMissing('event_attendees', ['nombres' => 'Segundo intento']);
    }

    public function test_staff_con_permiso_puede_registrar_asistente_manualmente(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);
        $evento = $this->crearEvento();

        $response = $this->actingAs($user)->post(route('eventos.asistentes.store', $evento), [
            'nombres' => 'Registrado por staff',
        ]);

        $response->assertRedirect(route('eventos.show', $evento));
        $this->assertDatabaseHas('event_attendees', [
            'nombres'    => 'Registrado por staff',
            'created_by' => $user->id,
        ]);
    }

    public function test_hacer_checkin_con_qr_valido_marca_asistencia(): void
    {
        $user = $this->usuarioConPermisos(['eventos.checkin']);
        $evento = $this->crearEvento();

        $asistente = $evento->asistentes()->create([
            'nombres'  => 'Rosa Quispe',
            'codigo'   => 'EV1-000001',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'estado'   => 'registrado',
        ]);

        $response = $this->actingAs($user)->postJson(route('eventos.checkin.scan', $evento), [
            'qr_token' => $asistente->qr_token,
        ]);

        $response->assertOk()->assertJson(['ok' => true]);

        $asistente->refresh();
        $this->assertEquals('asistio', $asistente->estado);
        $this->assertNotNull($asistente->checked_in_at);
        $this->assertEquals($user->id, $asistente->checked_in_by);
    }

    public function test_escanear_el_mismo_qr_dos_veces_avisa_que_ya_asistio_sin_error(): void
    {
        $user = $this->usuarioConPermisos(['eventos.checkin']);
        $evento = $this->crearEvento();

        $asistente = $evento->asistentes()->create([
            'nombres'       => 'Rosa Quispe',
            'codigo'        => 'EV1-000001',
            'qr_token'      => (string) \Illuminate\Support\Str::uuid(),
            'estado'        => 'asistio',
            'checked_in_at' => now(),
        ]);

        $response = $this->actingAs($user)->postJson(route('eventos.checkin.scan', $evento), [
            'qr_token' => $asistente->qr_token,
        ]);

        $response->assertOk()->assertJson(['ok' => false, 'ya_asistio' => true]);
    }

    public function test_qr_de_otro_evento_no_es_valido(): void
    {
        $user = $this->usuarioConPermisos(['eventos.checkin']);
        $eventoA = $this->crearEvento();
        $eventoB = Event::create(['nombre' => 'Otro evento', 'fecha_inicio' => '2026-09-01', 'estado' => 'planificado']);

        $asistente = $eventoA->asistentes()->create([
            'nombres'  => 'Rosa Quispe',
            'codigo'   => 'EV1-000001',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'estado'   => 'registrado',
        ]);

        $response = $this->actingAs($user)->postJson(route('eventos.checkin.scan', $eventoB), [
            'qr_token' => $asistente->qr_token,
        ]);

        $response->assertStatus(404)->assertJson(['ok' => false]);
    }

    public function test_usuario_sin_permiso_de_checkin_no_puede_escanear(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver']);
        $evento = $this->crearEvento();

        $asistente = $evento->asistentes()->create([
            'nombres'  => 'Rosa Quispe',
            'codigo'   => 'EV1-000001',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(),
            'estado'   => 'registrado',
        ]);

        $this->actingAs($user)->get(route('eventos.checkin', $evento))->assertForbidden();

        $this->actingAs($user)->postJson(route('eventos.checkin.scan', $evento), [
            'qr_token' => $asistente->qr_token,
        ])->assertForbidden();
    }
}
