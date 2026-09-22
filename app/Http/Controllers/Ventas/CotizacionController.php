<?php

namespace App\Http\Controllers\Ventas;

use App\Http\Controllers\Controller;
use App\Http\Requests\Ventas\StoreCotizacionRequest;
use App\Http\Requests\Ventas\UpdateCotizacionRequest;
use App\Models\Client;
use App\Models\EmpresaConfig;
use App\Models\Project;
use App\Models\Quote;
use App\Models\QuoteItem;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    public function index()
    {
        return view('cotizaciones.index');
    }

    public function create()
    {
        [$clientes, $proyectos, $config] = $this->formData();
        return view('cotizaciones.create', compact('clientes', 'proyectos', 'config'));
    }

    public function store(StoreCotizacionRequest $request)
    {
        $data  = $request->validated();
        $items = $data['items'];
        $config = EmpresaConfig::first();
        $igvPct = $config?->igv_porcentaje ?? 18;

        [$subtotal, $igv, $total] = $this->calcularTotales($items, $data['incluye_igv'] ?? true, $igvPct);

        $quote = DB::transaction(function () use ($data, $items, $subtotal, $igv, $total) {
            $q = Quote::create(array_merge(
                Arr::except($data, 'items'),
                [
                    'numero'     => Quote::generarNumero(),
                    'created_by' => auth()->id(),
                    'subtotal'   => $subtotal,
                    'igv'        => $igv,
                    'total'      => $total,
                ]
            ));

            foreach ($items as $orden => $item) {
                $q->items()->create([
                    'descripcion'     => $item['descripcion'],
                    'cantidad'        => $item['cantidad'],
                    'unidad'          => $item['unidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento'       => $item['descuento'] ?? 0,
                    'subtotal'        => $this->subtotalItem($item),
                    'orden'           => $orden,
                ]);
            }

            return $q;
        });

        return redirect()->route('cotizaciones.show', $quote)
            ->with('success', "Cotización {$quote->numero} creada correctamente.");
    }

    public function show(Quote $cotizacion)
    {
        $cotizacion->load(['client', 'project', 'items', 'createdBy', 'payments.invoice']);
        $config = EmpresaConfig::first();
        return view('cotizaciones.show', compact('cotizacion', 'config'));
    }

    public function edit(Quote $cotizacion)
    {
        if (!in_array($cotizacion->status, ['borrador', 'enviado'])) {
            return redirect()->route('cotizaciones.show', $cotizacion)
                ->with('error', 'Solo se pueden editar cotizaciones en borrador o enviadas.');
        }

        [$clientes, $proyectos, $config] = $this->formData();
        $cotizacion->load('items');
        return view('cotizaciones.edit', compact('cotizacion', 'clientes', 'proyectos', 'config'));
    }

    public function update(UpdateCotizacionRequest $request, Quote $cotizacion)
    {
        $data   = $request->validated();
        $items  = $data['items'];
        $config = EmpresaConfig::first();
        $igvPct = $config?->igv_porcentaje ?? 18;

        [$subtotal, $igv, $total] = $this->calcularTotales($items, $data['incluye_igv'] ?? true, $igvPct);

        DB::transaction(function () use ($cotizacion, $data, $items, $subtotal, $igv, $total) {
            $cotizacion->update(array_merge(
                Arr::except($data, 'items'),
                compact('subtotal', 'igv', 'total')
            ));

            $cotizacion->items()->delete();

            foreach ($items as $orden => $item) {
                $cotizacion->items()->create([
                    'descripcion'     => $item['descripcion'],
                    'cantidad'        => $item['cantidad'],
                    'unidad'          => $item['unidad'],
                    'precio_unitario' => $item['precio_unitario'],
                    'descuento'       => $item['descuento'] ?? 0,
                    'subtotal'        => $this->subtotalItem($item),
                    'orden'           => $orden,
                ]);
            }
        });

        return redirect()->route('cotizaciones.show', $cotizacion)
            ->with('success', 'Cotización actualizada correctamente.');
    }

    public function destroy(Quote $cotizacion)
    {
        $cotizacion->delete();
        return redirect()->route('cotizaciones.index')
            ->with('success', 'Cotización eliminada.');
    }

    public function cambiarEstado(Quote $cotizacion)
    {
        $status = request()->validate([
            'status' => ['required', 'in:' . implode(',', Quote::ESTADOS)],
        ])['status'];

        $extra = [];
        if ($status === 'enviado' && !$cotizacion->sent_at) {
            $extra['sent_at'] = now();
        }
        if ($status === 'aceptado' && !$cotizacion->accepted_at) {
            $extra['accepted_at'] = now();
        }

        $cotizacion->update(['status' => $status, ...$extra]);

        return back()->with('success', 'Estado actualizado: ' . Quote::ESTADO_LABELS[$status]);
    }

    public function pdf(Quote $cotizacion)
    {
        $cotizacion->load(['client', 'project', 'items', 'createdBy']);
        $config = EmpresaConfig::first();

        $pdf = Pdf::loadView('cotizaciones.pdf', compact('cotizacion', 'config'))
            ->setPaper('A4', 'portrait');

        return $pdf->stream('cotizacion-' . $cotizacion->numero . '.pdf');
    }

    // ── Helpers privados ─────────────────────────────────────────────
    private function subtotalItem(array $item): float
    {
        $bruto = (float) $item['cantidad'] * (float) $item['precio_unitario'];
        $desc  = (float) ($item['descuento'] ?? 0);
        return round($bruto * (1 - $desc / 100), 2);
    }

    private function calcularTotales(array $items, bool $incluyeIgv, float $igvPct): array
    {
        $subtotal = round(array_sum(array_map(fn($i) => $this->subtotalItem($i), $items)), 2);
        $igv      = $incluyeIgv ? round($subtotal * $igvPct / 100, 2) : 0;
        $total    = $subtotal + $igv;
        return [$subtotal, $igv, $total];
    }

    private function formData(): array
    {
        $clientes  = Client::activos()->orderBy('razon_social')
            ->get(['id', 'razon_social', 'nombre_comercial', 'numero_documento', 'direccion']);

        $proyectos = Project::whereNotIn('status', ['cancelado', 'entregado'])
            ->orderBy('name')
            ->get(['id', 'name', 'client_id']);

        $config = EmpresaConfig::first();

        return [$clientes, $proyectos, $config];
    }
}
