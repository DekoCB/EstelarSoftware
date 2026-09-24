<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventTeam;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EventTeamPagoTest extends TestCase
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

    /** @return array{0: Event, 1: EventTeam} */
    private function equipoInscrito(): array
    {
        $evento = Event::create([
            'nombre'       => 'Go Left!!',
            'fecha_inicio' => '2026-09-28',
            'estado'       => 'planificado',
        ]);

        $this->postJson(route('eventos.inscripcion.equipo.store', $evento), [
            'equipo'   => 'Los Sobrevivientes',
            'miembros' => [[
                'nombres' => 'Jugador 1', 'nickname' => 'nick1', 'edad' => 20,
                'telefono' => '999000001', 'steam_id' => 'steam1', 'rol' => 'Capitán',
            ]],
        ])->assertCreated()
          ->assertJsonPath('equipo.estado_pago', 'pendiente')
          ->assertJsonStructure(['equipo' => ['comprobante_url']]);

        return [$evento, EventTeam::first()];
    }

    public function test_el_equipo_sube_su_comprobante_sin_login_y_queda_en_revision(): void
    {
        Storage::fake('local');
        [$evento, $equipo] = $this->equipoInscrito();

        $this->postJson(route('eventos.inscripcion.equipo.comprobante', [$evento, $equipo]), [
            'comprobante' => UploadedFile::fake()->image('yape.jpg'),
        ])->assertOk()->assertJsonPath('estado_pago', 'en_revision');

        $equipo->refresh();
        $this->assertSame('en_revision', $equipo->estado_pago);
        Storage::disk('local')->assertExists($equipo->comprobante_pago);
    }

    public function test_no_se_puede_subir_comprobante_con_un_token_invalido(): void
    {
        [$evento] = $this->equipoInscrito();

        $this->postJson("/eventos/{$evento->id}/equipos/token-falso/comprobante", [
            'comprobante' => UploadedFile::fake()->image('yape.jpg'),
        ])->assertNotFound();
    }

    public function test_el_checkin_se_bloquea_hasta_que_el_staff_confirma_el_pago(): void
    {
        Storage::fake('local');
        [$evento, $equipo] = $this->equipoInscrito();
        $asistente = EventAttendee::first();
        $staff = $this->usuarioConPermisos(['eventos.checkin', 'eventos.editar', 'eventos.ver']);

        $this->actingAs($staff)
            ->postJson(route('eventos.checkin.scan', $evento), ['qr_token' => $asistente->qr_token])
            ->assertStatus(409);
        $this->assertSame('registrado', $asistente->fresh()->estado);

        $this->actingAs($staff)
            ->patch(route('eventos.equipos.pago', [$evento, $equipo]), ['estado_pago' => 'pagado'])
            ->assertRedirect();
        $this->assertTrue($equipo->fresh()->pagado());

        $this->actingAs($staff)
            ->postJson(route('eventos.checkin.scan', $evento), ['qr_token' => $asistente->qr_token])
            ->assertOk()->assertJsonPath('ok', true);
    }

    public function test_el_comprobante_solo_lo_ve_el_staff(): void
    {
        Storage::fake('local');
        [$evento, $equipo] = $this->equipoInscrito();
        $this->postJson(route('eventos.inscripcion.equipo.comprobante', [$evento, $equipo]), [
            'comprobante' => UploadedFile::fake()->image('yape.jpg'),
        ]);

        $this->get(route('eventos.equipos.comprobante', [$evento, $equipo]))->assertRedirect(route('login'));

        $sinPermiso = User::factory()->create();
        $this->actingAs($sinPermiso)
            ->get(route('eventos.equipos.comprobante', [$evento, $equipo]))
            ->assertForbidden();

        $this->actingAs($this->usuarioConPermisos(['eventos.ver']))
            ->get(route('eventos.equipos.comprobante', [$evento, $equipo]))
            ->assertOk();
    }

    public function test_no_se_puede_reemplazar_el_comprobante_de_un_pago_ya_confirmado(): void
    {
        Storage::fake('local');
        [$evento, $equipo] = $this->equipoInscrito();
        $equipo->update(['estado_pago' => 'pagado']);

        $this->postJson(route('eventos.inscripcion.equipo.comprobante', [$evento, $equipo]), [
            'comprobante' => UploadedFile::fake()->image('otro.jpg'),
        ])->assertStatus(409);
    }
}
