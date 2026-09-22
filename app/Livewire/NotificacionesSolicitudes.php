<?php

namespace App\Livewire;

use App\Models\Solicitud;
use Livewire\Component;

class NotificacionesSolicitudes extends Component
{
    const UMBRAL_MONTO = 500;

    public function render()
    {
        if (! auth()->user()?->can('solicitudes.gestionar')) {
            return view('livewire.notificaciones-solicitudes', [
                'solicitudes' => collect(),
                'total'       => 0,
            ]);
        }

        $solicitudes = Solicitud::with('solicitante')
            ->where('estado', 'pendiente')
            ->where('tipo', 'dinero')
            ->where('monto', '<', self::UMBRAL_MONTO)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('livewire.notificaciones-solicitudes', [
            'solicitudes' => $solicitudes,
            'total'       => $solicitudes->count(),
        ]);
    }
}
