<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Config\UpdateEmpresaConfigRequest;
use App\Models\EmpresaConfig;
use App\Models\LeadMeeting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    public function index()
    {
        $config = EmpresaConfig::config();
        return view('configuracion.index', compact('config'));
    }

    public function update(UpdateEmpresaConfigRequest $request)
    {
        $config = EmpresaConfig::config();
        $data   = $request->safe()->except([
            'logo_sidebar', 'logo_login', 'logo_documentos',
            'delete_logo_sidebar', 'delete_logo_login', 'delete_logo_documentos',
        ]);

        // Procesar cada logo
        foreach (['logo_sidebar', 'logo_login', 'logo_documentos'] as $campo) {

            // 1. Eliminar si se marcó delete
            if ($request->boolean("delete_{$campo}") && $config->$campo) {
                Storage::disk('public')->delete($config->$campo);
                $data[$campo] = null;
            }

            // 2. Subir nueva imagen (reemplaza la actual)
            if ($request->hasFile($campo)) {
                if ($config->$campo) {
                    Storage::disk('public')->delete($config->$campo);
                }
                $data[$campo] = $request->file($campo)->store("logos/{$campo}", 'public');
            }
        }

        $reunionMinutosAnterior  = $config->recordatorio_reunion_minutos;
        $despuesMinutosAnterior  = $config->alerta_despues_minutos;

        $config->fill($data);
        $config->save();

        // Si cambió el aviso "antes", recalcular las reuniones que aún no
        // dispararon ese aviso (nuevas y antiguas por igual).
        if ($config->wasChanged('recordatorio_reunion_minutos') || is_null($reunionMinutosAnterior)) {
            LeadMeeting::whereNull('recordatorio_enviado_en')
                ->whereNotNull('fecha_hora')
                ->update([
                    'recordatorio_minutos' => $config->recordatorioReunionMinutos(),
                    'recordatorio_en'      => DB::raw('DATE_SUB(fecha_hora, INTERVAL ' . $config->recordatorioReunionMinutos() . ' MINUTE)'),
                ]);
        }

        // Igual para el aviso "despues".
        if ($config->wasChanged('alerta_despues_minutos') || is_null($despuesMinutosAnterior)) {
            LeadMeeting::whereNull('aviso_despues_enviado_en')
                ->whereNotNull('fecha_hora')
                ->update([
                    'aviso_despues_en' => DB::raw('DATE_ADD(fecha_hora, INTERVAL ' . $config->alertaDespuesMinutos() . ' MINUTE)'),
                ]);
        }

        // Limpiar caché de config de empresa
        Cache::forget('empresa_config');

        return redirect()
            ->route('configuracion.index')
            ->with('success', 'Configuración guardada correctamente.');
    }
}
