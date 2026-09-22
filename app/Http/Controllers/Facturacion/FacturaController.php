<?php

namespace App\Http\Controllers\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Facturacion\StoreFacturaRequest;
use App\Models\Client;
use App\Models\EmpresaConfig;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use App\Models\QuotePayment;
use App\Services\SunatService;
use Illuminate\Support\Facades\DB;

class FacturaController extends Controller
{
    public function __construct(private SunatService $sunat) {}

    public function index()
    {
        $apiOk = $this->sunat->estaConfigurada();
        return view('facturacion.index', compact('apiOk'));
    }

    public function create()
    {
        $clientes = Client::activos()->orderBy('razon_social')
            ->get(['id', 'tipo_documento', 'numero_documento', 'razon_social', 'nombre_comercial', 'direccion', 'ubigeo', 'email', 'telefono']);

        // Una cotización puede tener múltiples facturas (una por cuota), no filtrar por eso
        $cotizaciones = Quote::where('status', 'aceptado')
            ->with('client')
            ->orderByDesc('created_at')
            ->get(['id', 'numero', 'client_id', 'total', 'moneda']);

        $config      = EmpresaConfig::first();
        $igvPct      = $config?->igv_porcentaje ?? 18;
        $serieFactura = $config?->serie_factura ?? 'F001';
        $serieBoleta  = $config?->serie_boleta  ?? 'B001';
        $apiOk        = $this->sunat->estaConfigurada();

        // Pre-llenar desde cotización / cuota
        $preQuote   = null;
        $prePago    = null;
        $preItems   = [];

        if ($quoteId = request()->integer('quote_id')) {
            $preQuote = Quote::with(['client', 'items'])->find($quoteId);
            if ($preQuote) {
                if ($pagoId = request()->integer('payment_id')) {
                    $prePago = $preQuote->payments()->find($pagoId);
                }
                // Construir ítems pre-llenados
                if ($prePago) {
                    $preItems = [[
                        'descripcion'    => $prePago->nombre . ' — ' . $preQuote->numero,
                        'unidad_sunat'   => 'ZZ',
                        'cantidad'       => 1,
                        'precio_unitario'=> round((float) $prePago->monto / (1 + $igvPct / 100), 2),
                        'tipo_afectacion'=> '10',
                    ]];
                } else {
                    $preItems = $preQuote->items->map(fn($i) => [
                        'descripcion'    => $i->descripcion,
                        'unidad_sunat'   => 'ZZ',
                        'cantidad'       => (float) $i->cantidad,
                        'precio_unitario'=> round((float) $i->subtotal / (float) $i->cantidad, 2),
                        'tipo_afectacion'=> '10',
                    ])->toArray();
                }
            }
        }

        return view('facturacion.create', compact(
            'clientes', 'cotizaciones', 'config', 'igvPct', 'apiOk',
            'serieFactura', 'serieBoleta',
            'preQuote', 'prePago', 'preItems'
        ));
    }

    public function store(StoreFacturaRequest $request)
    {
        $data   = $request->validated();
        $config = EmpresaConfig::first();
        $igvPct = $config?->igv_porcentaje ?? 18;
        $esFactura = $data['tipo_comprobante'] === Invoice::TIPO_FACTURA;

        // Calcular totales
        $subtotal = 0;
        $igvTotal = 0;
        $itemsCalc = [];

        foreach ($data['items'] as $i => $item) {
            $sub = round((float)$item['cantidad'] * (float)$item['precio_unitario'], 2);
            $igv = $item['tipo_afectacion'] === '10' ? round($sub * $igvPct / 100, 2) : 0;
            $subtotal += $sub;
            $igvTotal += $igv;
            $itemsCalc[] = array_merge($item, [
                'subtotal'       => $sub,
                'igv'            => $igv,
                'total'          => $sub + $igv,
                'igv_porcentaje' => $igvPct,
                'orden'          => $i,
            ]);
        }

        $invoice = DB::transaction(function () use ($data, $itemsCalc, $subtotal, $igvTotal) {
            $inv = Invoice::create([
                'tipo_comprobante' => $data['tipo_comprobante'],
                'serie'            => $data['serie'],
                'client_id'        => $data['client_id'],
                'quote_id'         => $data['quote_id'] ?? null,
                'fecha_emision'    => $data['fecha_emision'],
                'fecha_vencimiento'=> $data['fecha_vencimiento'] ?? null,
                'moneda'           => $data['moneda'],
                'subtotal'         => round($subtotal, 2),
                'igv'              => round($igvTotal, 2),
                'total'            => round($subtotal + $igvTotal, 2),
                'notas'            => $data['notas'] ?? null,
                'estado_sunat'     => 'borrador',
                'created_by'       => auth()->id(),
            ]);

            foreach ($itemsCalc as $item) {
                $inv->items()->create($item);
            }

            return $inv;
        });

        // Si la API está configurada, crear en la API SUNAT automáticamente
        if ($this->sunat->estaConfigurada()) {
            try {
                $invoice->load(['client', 'items', 'createdBy']);
                $result = $esFactura
                    ? $this->sunat->crearFactura($invoice)
                    : $this->sunat->crearBoleta($invoice);

                $invoice->update([
                    'sunat_doc_id'    => $result['sunat_doc_id'],
                    'numero_completo' => $result['numero_completo'],
                    'correlativo'     => $result['correlativo'],
                    'estado_sunat'    => 'pendiente',
                ]);
            } catch (\Exception $e) {
                $invoice->update([
                    'estado_sunat'   => 'error',
                    'sunat_mensaje'  => $e->getMessage(),
                ]);
            }
        }

        // Si viene de una cuota de plan de cobros, vincularla
        if ($paymentId = request()->integer('payment_id')) {
            QuotePayment::where('id', $paymentId)
                ->where('quote_id', $invoice->quote_id)
                ->update(['invoice_id' => $invoice->id]);
        }

        return redirect()->route('facturacion.show', $invoice)
            ->with('success', 'Comprobante creado correctamente.');
    }

    public function show(Invoice $factura)
    {
        $factura->load(['client', 'quote', 'items', 'createdBy']);
        $apiOk = $this->sunat->estaConfigurada();
        return view('facturacion.show', compact('factura', 'apiOk'));
    }

    public function destroy(Invoice $factura)
    {
        if (!$factura->puedeBorrarse()) {
            return back()->with('error', 'No se puede eliminar: el comprobante ya tiene un número asignado en SUNAT. Para anularlo usa una Nota de Crédito.');
        }
        $factura->delete();
        return redirect()->route('facturacion.index')->with('success', 'Comprobante eliminado.');
    }

    // ── Enviar a SUNAT ───────────────────────────────────────────────
    public function enviar(Invoice $factura)
    {
        if (!$factura->puedeEmitirse()) {
            return back()->with('error', 'Este comprobante no puede enviarse en su estado actual.');
        }

        $factura->update(['estado_sunat' => 'enviando']);

        try {
            $result = $factura->esFactura()
                ? $this->sunat->enviarFactura($factura->sunat_doc_id)
                : $this->sunat->enviarBoleta($factura->sunat_doc_id);

            // La API puede retornar el estado en distintas claves
            $data      = $result['data'] ?? $result;
            $estadoRaw = strtolower(
                $data['estado_sunat'] ?? $data['estado'] ?? ''
            );
            $estadoMap = [
                'aceptado'  => 'aceptado',
                'pendiente' => 'pendiente',
                'rechazado' => 'rechazado',
                'enviado'   => 'pendiente',
            ];
            $estadoFinal = $estadoMap[$estadoRaw] ?? 'pendiente';

            // Buscar mensaje de SUNAT en diferentes claves posibles
            $mensaje = $data['sunat_descripcion']
                ?? $data['sunat_mensaje']
                ?? $data['descripcion']
                ?? $data['message']
                ?? null;

            $factura->update([
                'estado_sunat'  => $estadoFinal,
                'sunat_mensaje' => $mensaje,
                'emitido_at'    => now(),
            ]);

            $msg = $estadoFinal === 'aceptado'
                ? '✓ Comprobante ACEPTADO por SUNAT.'
                : 'Comprobante enviado — estado: ' . $estadoFinal;

            return back()->with('success', $msg);
        } catch (\Exception $e) {
            // Extraer solo el mensaje útil (quitar prefijo técnico de la URL)
            $rawMsg = $e->getMessage();
            $mensaje = preg_replace('/^SUNAT API error \[.*?\] \d+:\s*/i', '', $rawMsg);

            $factura->update([
                'estado_sunat'  => 'error',
                'sunat_mensaje' => $mensaje,
            ]);
            return back()->with('error', $mensaje);
        }
    }

    // ── Descargar PDF / XML / CDR ────────────────────────────────────
    public function descargarPdf(Invoice $factura)
    {
        return $this->descargar(
            $factura->esFactura()
                ? $this->sunat->descargarPdfFactura($factura->sunat_doc_id)
                : $this->sunat->descargarPdfBoleta($factura->sunat_doc_id),
            $factura->numero_completo . '.pdf',
            'application/pdf'
        );
    }

    public function descargarXml(Invoice $factura)
    {
        return $this->descargar(
            $factura->esFactura()
                ? $this->sunat->descargarXmlFactura($factura->sunat_doc_id)
                : $this->sunat->descargarXmlBoleta($factura->sunat_doc_id),
            $factura->numero_completo . '.xml',
            'application/xml'
        );
    }

    public function descargarCdr(Invoice $factura)
    {
        return $this->descargar(
            $factura->esFactura()
                ? $this->sunat->descargarCdrFactura($factura->sunat_doc_id)
                : $this->sunat->descargarCdrBoleta($factura->sunat_doc_id),
            'CDR-' . $factura->numero_completo . '.zip',
            'application/zip'
        );
    }

    private function descargar($response, string $filename, string $mime)
    {
        if ($response->status() === 404) {
            return back()->with('error', 'El archivo no existe en la API. Envía el comprobante a SUNAT primero.');
        }
        if (!$response->successful()) {
            $msg = $response->json('message') ?? 'Error al obtener el archivo de la API SUNAT.';
            return back()->with('error', $msg);
        }
        return response($response->body(), 200, [
            'Content-Type'        => $mime,
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ]);
    }
}
