<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class SunatService
{
    private string $baseUrl;
    private string $token;
    private int    $companyId;
    private int    $branchId;
    private int    $timeout;

    public function __construct()
    {
        // Normalizar: quitar /api o /api/v1 del final para que las rutas internas queden correctas
        $url = rtrim(config('services.sunat_api.url', 'http://localhost:8001'), '/');
        $url = preg_replace('#/api(/v\d+)?$#', '', $url);
        $this->baseUrl = $url;
        $this->token     = config('services.sunat_api.token', '');
        $this->companyId = (int) config('services.sunat_api.company_id', 1);
        $this->branchId  = (int) config('services.sunat_api.branch_id', 1);
        $this->timeout   = (int) config('services.sunat_api.timeout', 30);
    }

    // ── Verificar conexión ───────────────────────────────────────────
    public function estaConfigurada(): bool
    {
        return !empty($this->token) && !empty($this->baseUrl);
    }

    public function ping(): bool
    {
        try {
            $resp = Http::timeout(5)->get($this->baseUrl . '/api/health');
            return $resp->successful();
        } catch (\Exception) {
            return false;
        }
    }

    // ── Crear comprobante en la API SUNAT ────────────────────────────

    /**
     * Crea una factura en la API SUNAT y retorna [ sunat_doc_id, numero_completo ].
     */
    public function crearFactura(Invoice $invoice): array
    {
        $payload = $this->buildPayloadFactura($invoice);
        $resp    = $this->post('/api/v1/invoices', $payload);
        return [
            'sunat_doc_id'    => $resp['data']['id'],
            'numero_completo' => $resp['data']['numero_completo'],
            'correlativo'     => $resp['data']['correlativo'],
        ];
    }

    /**
     * Crea una boleta en la API SUNAT y retorna [ sunat_doc_id, numero_completo ].
     */
    public function crearBoleta(Invoice $invoice): array
    {
        $payload = $this->buildPayloadBoleta($invoice);
        $resp    = $this->post('/api/v1/boletas', $payload);
        return [
            'sunat_doc_id'    => $resp['data']['id'],
            'numero_completo' => $resp['data']['numero_completo'],
            'correlativo'     => $resp['data']['correlativo'],
        ];
    }

    // ── Enviar a SUNAT ───────────────────────────────────────────────

    public function enviarFactura(int $sunatDocId): array
    {
        return $this->post("/api/v1/invoices/{$sunatDocId}/send-sunat", []);
    }

    public function enviarBoleta(int $sunatDocId): array
    {
        return $this->post("/api/v1/boletas/{$sunatDocId}/send-sunat", []);
    }

    // ── Descargar archivos (retorna respuesta HTTP para stream) ──────

    public function descargarPdfFactura(int $sunatDocId): Response
    {
        return $this->getRaw("/api/v1/invoices/{$sunatDocId}/download-pdf");
    }

    public function descargarPdfBoleta(int $sunatDocId): Response
    {
        return $this->getRaw("/api/v1/boletas/{$sunatDocId}/download-pdf");
    }

    public function descargarXmlFactura(int $sunatDocId): Response
    {
        return $this->getRaw("/api/v1/invoices/{$sunatDocId}/download-xml");
    }

    public function descargarXmlBoleta(int $sunatDocId): Response
    {
        return $this->getRaw("/api/v1/boletas/{$sunatDocId}/download-xml");
    }

    public function descargarCdrFactura(int $sunatDocId): Response
    {
        return $this->getRaw("/api/v1/invoices/{$sunatDocId}/download-cdr");
    }

    public function descargarCdrBoleta(int $sunatDocId): Response
    {
        return $this->getRaw("/api/v1/boletas/{$sunatDocId}/download-cdr");
    }

    // ── Builders de payload ──────────────────────────────────────────

    private function buildPayloadFactura(Invoice $invoice): array
    {
        $payload = [
            'company_id'        => $this->companyId,
            'branch_id'         => $this->branchId,
            'serie'             => $invoice->serie,
            'fecha_emision'     => $invoice->fecha_emision->format('Y-m-d'),
            'moneda'            => $invoice->moneda,
            'tipo_operacion'    => '0101',
            'forma_pago_tipo'   => 'Contado',
            'usuario_creacion'  => $invoice->createdBy->email ?? auth()->user()?->email,
            'client'            => $this->buildClient($invoice->client),
            'detalles'          => $this->buildDetalles($invoice->items, $invoice),
        ];

        if ($invoice->fecha_vencimiento) {
            $payload['fecha_vencimiento'] = $invoice->fecha_vencimiento->format('Y-m-d');
        }
        if ($invoice->notas) {
            $payload['notas'] = $invoice->notas;
        }

        return $payload;
    }

    private function buildPayloadBoleta(Invoice $invoice): array
    {
        $payload = [
            'company_id'        => $this->companyId,
            'branch_id'         => $this->branchId,
            'serie'             => $invoice->serie,
            'fecha_emision'     => $invoice->fecha_emision->format('Y-m-d'),
            'moneda'            => $invoice->moneda,
            'metodo_envio'      => 'individual',
            'forma_pago_tipo'   => 'Contado',
            'usuario_creacion'  => $invoice->createdBy->email ?? auth()->user()?->email,
            'client'            => $this->buildClient($invoice->client),
            'detalles'          => $this->buildDetalles($invoice->items, $invoice),
        ];

        if ($invoice->notas) {
            $payload['notas'] = $invoice->notas;
        }

        return $payload;
    }

    private function buildClient(Client $client): array
    {
        // Solo enviar campos con valor para no disparar validaciones de la API
        $data = [
            'tipo_documento'   => Invoice::DOC_CODES[$client->tipo_documento] ?? '1',
            'numero_documento' => $client->numero_documento,
            'razon_social'     => $client->razon_social,
        ];

        if ($client->nombre_comercial) $data['nombre_comercial'] = $client->nombre_comercial;
        if ($client->direccion)        $data['direccion']        = $client->direccion;
        if ($client->ubigeo)           $data['ubigeo']           = $client->ubigeo;
        if ($client->email)            $data['email']            = $client->email;
        if ($client->telefono)         $data['telefono']         = $client->telefono;

        return $data;
    }

    private function buildDetalles($items, Invoice $invoice): array
    {
        // Calcular porcentaje IGV real desde los ítems o usar default
        $igvPct = 18;
        if ($invoice->subtotal > 0 && $invoice->igv > 0) {
            $calculated = round(($invoice->igv / $invoice->subtotal) * 100);
            if ($calculated > 0) $igvPct = $calculated;
        }

        return $items->values()->map(function (InvoiceItem $item, int $idx) use ($igvPct) {
            return [
                'codigo'             => 'SRV-' . str_pad($idx + 1, 3, '0', STR_PAD_LEFT),
                'descripcion'        => $item->descripcion,
                'unidad'             => $item->unidad_sunat,
                'cantidad'           => (float) $item->cantidad,
                'mto_valor_unitario' => (float) $item->precio_unitario,
                'porcentaje_igv'     => (float) ($item->igv_porcentaje ?? $igvPct),
                'tip_afe_igv'        => $item->tipo_afectacion,
            ];
        })->toArray();
    }

    // ── HTTP helpers ─────────────────────────────────────────────────

    private function client(): \Illuminate\Http\Client\PendingRequest
    {
        $this->assertConfigurada();
        return Http::withToken($this->token)
            ->timeout($this->timeout)
            ->acceptJson();
    }

    private function post(string $path, array $data): array
    {
        $resp = $this->client()->post($this->baseUrl . $path, $data);
        return $this->handleResponse($resp, $path);
    }

    private function getRaw(string $path): Response
    {
        $this->assertConfigurada();
        return Http::withToken($this->token)
            ->timeout($this->timeout)
            ->get($this->baseUrl . $path);
    }

    private function handleResponse(Response $resp, string $path): array
    {
        if ($resp->successful()) {
            return $resp->json();
        }

        $body = $resp->json();
        $msg  = $body['message'] ?? $body['error'] ?? $resp->body();
        throw new RuntimeException("SUNAT API error [{$path}] {$resp->status()}: {$msg}");
    }

    private function assertConfigurada(): void
    {
        if (!$this->estaConfigurada()) {
            throw new RuntimeException(
                'La API de facturación SUNAT no está configurada. ' .
                'Revisa SUNAT_API_URL y SUNAT_API_TOKEN en el .env'
            );
        }
    }
}
