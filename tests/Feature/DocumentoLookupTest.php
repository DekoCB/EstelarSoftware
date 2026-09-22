<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class DocumentoLookupTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_ruta_publica_devuelve_los_datos_del_dni_sin_login(): void
    {
        Http::fake([
            'api.apis.net.pe/v1/dni*' => Http::response([
                'nombre' => 'LLACZA ISIDRO JOSE MIGUEL',
                'numeroDocumento' => '74093841',
            ], 200),
        ]);

        $response = $this->getJson('/api/consulta-documento-publico?tipo=DNI&numero=74093841');

        $response->assertOk()->assertJson(['nombre' => 'LLACZA ISIDRO JOSE MIGUEL']);
    }

    public function test_la_ruta_publica_rechaza_parametros_invalidos_sin_llamar_a_la_api(): void
    {
        Http::fake();

        $response = $this->getJson('/api/consulta-documento-publico?tipo=PASAPORTE&numero=123');

        $response->assertStatus(422);
        Http::assertNothingSent();
    }

    public function test_la_ruta_publica_se_limita_por_ip_para_no_agotar_la_cuota_externa(): void
    {
        Http::fake([
            'api.apis.net.pe/v1/dni*' => Http::response(['nombre' => 'Alguien'], 200),
        ]);

        for ($i = 0; $i < 15; $i++) {
            $this->getJson('/api/consulta-documento-publico?tipo=DNI&numero=74093841')->assertOk();
        }

        $this->getJson('/api/consulta-documento-publico?tipo=DNI&numero=74093841')
            ->assertStatus(429);
    }
}
