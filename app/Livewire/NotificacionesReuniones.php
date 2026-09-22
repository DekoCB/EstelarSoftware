<?php

namespace App\Livewire;

use App\Models\LeadMeeting;
use Livewire\Component;

class NotificacionesReuniones extends Component
{
    public function render()
    {
        if (! auth()->user()?->can('leads.ver')) {
            return view('livewire.notificaciones-reuniones', [
                'reuniones' => collect(),
                'total'     => 0,
            ]);
        }

        $reuniones = LeadMeeting::with('lead:id,nombre,empresa,respuesta_rapida')
            ->whereHas('lead')
            ->hoy()
            ->orderBy('fecha_hora')
            ->limit(10)
            ->get();

        return view('livewire.notificaciones-reuniones', [
            'reuniones' => $reuniones,
            'total'     => $reuniones->count(),
        ]);
    }
}
