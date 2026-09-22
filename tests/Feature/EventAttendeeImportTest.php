<?php

namespace Tests\Feature;

use App\Models\Event;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class EventAttendeeImportTest extends TestCase
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
            'nombre'       => 'Feria del Emprendedor',
            'fecha_inicio' => '2026-09-05',
            'estado'       => 'en_curso',
        ]);
    }

    private function archivoXlsx(array $filas): UploadedFile
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->fromArray(['Nombres', 'Empresa', 'Tipo documento', 'Numero documento', 'Direccion', 'Email', 'Celular'], null, 'A1');

        $fila = 2;
        foreach ($filas as $datos) {
            $sheet->fromArray($datos, null, "A{$fila}");
            $fila++;
        }

        $ruta = tempnam(sys_get_temp_dir(), 'test_asistentes') . '.xlsx';
        (new Xlsx($spreadsheet))->save($ruta);

        return new UploadedFile($ruta, 'asistentes.xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', null, true);
    }

    public function test_descargar_la_plantilla_devuelve_un_excel(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);
        $evento = $this->crearEvento();

        $response = $this->actingAs($user)->get(route('eventos.asistentes.plantilla', $evento));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }

    public function test_importar_un_excel_valido_crea_los_asistentes(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);
        $evento = $this->crearEvento();

        $archivo = $this->archivoXlsx([
            ['Carlos Ugarte', 'CEO Ugarte', 'DNI', '11111111', 'Av. Uno', 'carlos@ugarte.pe', '911111111'],
            ['Rosa Quispe', '', 'DNI', '22222222', '', '', ''],
        ]);

        $response = $this->actingAs($user)->post(route('eventos.asistentes.importar.store', $evento), [
            'archivo' => $archivo,
        ]);

        $response->assertRedirect(route('eventos.show', $evento));
        $this->assertDatabaseHas('event_attendees', [
            'event_id' => $evento->id, 'nombres' => 'Carlos Ugarte', 'numero_documento' => '11111111', 'empresa' => 'CEO Ugarte',
        ]);
        $this->assertDatabaseHas('event_attendees', [
            'event_id' => $evento->id, 'nombres' => 'Rosa Quispe', 'numero_documento' => '22222222',
        ]);
        $this->assertEquals(2, $evento->asistentes()->count());

        $codigos = $evento->asistentes()->orderBy('id')->pluck('codigo');
        $this->assertNotEquals($codigos[0], $codigos[1]);
    }

    public function test_filas_sin_nombre_se_omiten_y_filas_con_documento_duplicado_fallan(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);
        $evento = $this->crearEvento();

        $archivo = $this->archivoXlsx([
            ['', '', '', '', '', '', ''], // fila vacía, se omite silenciosamente
            ['Carlos Ugarte', '', 'DNI', '11111111', '', '', ''],
            ['Otro Carlos', '', 'DNI', '11111111', '', '', ''], // documento duplicado en el mismo archivo
        ]);

        $this->actingAs($user)->post(route('eventos.asistentes.importar.store', $evento), [
            'archivo' => $archivo,
        ]);

        $this->assertEquals(1, $evento->asistentes()->count());
        $this->assertDatabaseMissing('event_attendees', ['nombres' => 'Otro Carlos']);
    }

    public function test_no_se_puede_importar_con_un_documento_que_ya_esta_registrado(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver', 'eventos.crear']);
        $evento = $this->crearEvento();

        $evento->asistentes()->create([
            'nombres' => 'Ya Registrado', 'numero_documento' => '11111111',
            'codigo' => 'EV1-000001', 'qr_token' => (string) \Illuminate\Support\Str::uuid(), 'estado' => 'registrado',
        ]);

        $archivo = $this->archivoXlsx([
            ['Duplicado', '', 'DNI', '11111111', '', '', ''],
        ]);

        $this->actingAs($user)->post(route('eventos.asistentes.importar.store', $evento), [
            'archivo' => $archivo,
        ]);

        $this->assertEquals(1, $evento->asistentes()->count());
    }

    public function test_usuario_sin_permiso_no_puede_importar(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver']);
        $evento = $this->crearEvento();

        $archivo = $this->archivoXlsx([['Carlos Ugarte', '', 'DNI', '11111111', '', '', '']]);

        $this->actingAs($user)->post(route('eventos.asistentes.importar.store', $evento), [
            'archivo' => $archivo,
        ])->assertForbidden();

        $this->assertEquals(0, $evento->asistentes()->count());
    }

    public function test_exportar_devuelve_un_excel_con_los_asistentes(): void
    {
        $user = $this->usuarioConPermisos(['eventos.ver']);
        $evento = $this->crearEvento();

        $evento->asistentes()->create([
            'nombres' => 'Carlos Ugarte', 'codigo' => 'EV1-000001',
            'qr_token' => (string) \Illuminate\Support\Str::uuid(), 'estado' => 'registrado',
        ]);

        $response = $this->actingAs($user)->get(route('eventos.asistentes.exportar', $evento));

        $response->assertOk();
        $this->assertStringContainsString('spreadsheet', $response->headers->get('content-type'));
    }
}
