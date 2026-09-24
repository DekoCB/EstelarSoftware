<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\EventAttendee;
use App\Models\EventTeam;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class EventTeamInscripcionTest extends TestCase
{
    use RefreshDatabase;

    private function crearEvento(): Event
    {
        return Event::create([
            'nombre'       => 'Go Left!!',
            'fecha_inicio' => '2026-09-28',
            'estado'       => 'planificado',
        ]);
    }

    private function miembro(int $n, string $rol = 'Jugador'): array
    {
        return [
            'nombres'  => "Jugador {$n}",
            'nickname' => "nick{$n}",
            'edad'     => 20 + $n,
            'telefono' => "99900000{$n}",
            'steam_id' => "steam{$n}",
            'rol'      => $rol,
        ];
    }

    public function test_inscribe_un_equipo_con_logo_y_crea_un_asistente_por_integrante(): void
    {
        Storage::fake('public');
        $evento = $this->crearEvento();

        $response = $this->postJson(route('eventos.inscripcion.equipo.store', $evento), [
            'equipo'   => 'Los Sobrevivientes',
            'logo'     => UploadedFile::fake()->image('logo.png', 200, 200),
            'miembros' => [$this->miembro(1, 'Capitán'), $this->miembro(2), $this->miembro(3)],
        ]);

        $response->assertCreated()
            ->assertJsonPath('equipo.nombre', 'Los Sobrevivientes')
            ->assertJsonCount(3, 'integrantes');

        $equipo = EventTeam::first();
        $this->assertNotNull($equipo->logo);
        Storage::disk('public')->assertExists($equipo->logo);

        $this->assertSame(3, EventAttendee::where('event_team_id', $equipo->id)->count());
        $this->assertDatabaseHas('event_attendees', [
            'event_id'      => $evento->id,
            'event_team_id' => $equipo->id,
            'nickname'      => 'nick1',
            'equipo'        => 'Los Sobrevivientes',
            'rol'           => 'Capitán',
        ]);
        // Cada integrante con su propio codigo correlativo
        $this->assertSame(3, EventAttendee::distinct('codigo')->count('codigo'));
    }

    public function test_el_logo_es_opcional(): void
    {
        $evento = $this->crearEvento();

        $this->postJson(route('eventos.inscripcion.equipo.store', $evento), [
            'equipo'   => 'Sin Logo',
            'miembros' => [$this->miembro(1, 'Capitán')],
        ])->assertCreated()->assertJsonPath('equipo.logo_url', null);
    }

    public function test_no_permite_mas_de_cuatro_integrantes(): void
    {
        $evento = $this->crearEvento();

        $this->postJson(route('eventos.inscripcion.equipo.store', $evento), [
            'equipo'   => 'Muy Grande',
            'miembros' => array_map(fn ($n) => $this->miembro($n), range(1, 5)),
        ])->assertUnprocessable()->assertJsonValidationErrors('miembros');

        $this->assertSame(0, EventTeam::count());
        $this->assertSame(0, EventAttendee::count());
    }

    public function test_no_permite_nombre_de_equipo_repetido_ni_dos_capitanes(): void
    {
        $evento = $this->crearEvento();
        EventTeam::create(['event_id' => $evento->id, 'nombre' => 'Repetido']);

        $this->postJson(route('eventos.inscripcion.equipo.store', $evento), [
            'equipo'   => 'Repetido',
            'miembros' => [$this->miembro(1, 'Capitán'), $this->miembro(2, 'Capitán')],
        ])->assertUnprocessable()->assertJsonValidationErrors(['equipo', 'miembros']);
    }
}
