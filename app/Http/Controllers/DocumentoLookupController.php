<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DocumentoLookupController extends Controller
{
    public function __invoke(Request $request)
    {
        $tipo   = strtoupper($request->input('tipo', ''));
        $numero = preg_replace('/\D/', '', $request->input('numero', ''));

        if (!in_array($tipo, ['DNI', 'RUC']) || empty($numero)) {
            return response()->json(['error' => 'Parámetros inválidos'], 422);
        }

        $endpoint = $tipo === 'RUC'
            ? config('services.apis_net_pe.url') . '/ruc?numero=' . $numero
            : config('services.apis_net_pe.url') . '/dni?numero=' . $numero;

        $token = config('services.apis_net_pe.token');

        $req = Http::timeout(8)->acceptJson();
        if ($token) {
            $req = $req->withToken($token);
        }

        $response = $req->get($endpoint);

        if ($response->status() === 404) {
            return response()->json(['error' => 'No encontrado'], 404);
        }

        if ($response->failed()) {
            return response()->json(['error' => 'Error al consultar la API'], 502);
        }

        return response()->json($response->json());
    }
}
