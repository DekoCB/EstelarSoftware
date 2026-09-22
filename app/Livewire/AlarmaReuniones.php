<?php

namespace App\Livewire;

use App\Models\LeadMeeting;
use Livewire\Component;

class AlarmaReuniones extends Component
{
    public function marcarVisto(int $id, string $momento): void
    {
        LeadMeeting::where('id', $id)
            ->where('created_by', auth()->id())
            ->first()
            ?->marcarAvisoVisto($momento);
    }

    public function render()
    {
        $avisos = collect();

        if (auth()->user()?->can('leads.ver')) {
            $userId = auth()->id();
            $with   = ['lead:id,nombre,empresa'];

            $antes = LeadMeeting::recordatoriosPendientes($userId)->whereHas('lead')->with($with)->get()
                ->map(fn (LeadMeeting $m) => ['meeting' => $m, 'momento' => LeadMeeting::MOMENTO_ANTES]);

            $hora = LeadMeeting::avisosHoraPendientes($userId)->whereHas('lead')->with($with)->get()
                ->map(fn (LeadMeeting $m) => ['meeting' => $m, 'momento' => LeadMeeting::MOMENTO_HORA]);

            $despues = LeadMeeting::avisosDespuesPendientes($userId)->whereHas('lead')->with($with)->get()
                ->map(fn (LeadMeeting $m) => ['meeting' => $m, 'momento' => LeadMeeting::MOMENTO_DESPUES]);

            $avisos = $antes->concat($hora)->concat($despues);
        }

        return view('livewire.alarma-reuniones', [
            'avisos' => $avisos,
        ]);
    }
}
