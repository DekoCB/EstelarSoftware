<?php

namespace App\Console\Commands;

use App\Models\LeadMeeting;
use Illuminate\Console\Command;

class ExpirarRecordatoriosReuniones extends Command
{
    protected $signature = 'reuniones:expirar-recordatorios';

    protected $description = 'Marca como enviados los recordatorios de reuniones que quedaron vencidos sin que nadie los recogiera (evita que se disparen de golpe reuniones viejas si el sistema estuvo caído).';

    public function handle(): void
    {
        $limite = now()->subMinutes(LeadMeeting::RECORDATORIO_GRACE_MINUTES);
        $total  = 0;

        $total += LeadMeeting::whereNull('recordatorio_enviado_en')
            ->whereNotNull('recordatorio_en')
            ->where('recordatorio_en', '<', $limite)
            ->update(['recordatorio_enviado_en' => now()]);

        $total += LeadMeeting::whereNull('aviso_hora_enviado_en')
            ->where('fecha_hora', '<', $limite)
            ->update(['aviso_hora_enviado_en' => now()]);

        $total += LeadMeeting::whereNull('aviso_despues_enviado_en')
            ->whereNotNull('aviso_despues_en')
            ->where('aviso_despues_en', '<', $limite)
            ->update(['aviso_despues_enviado_en' => now()]);

        if ($total > 0) {
            $this->info("Avisos expirados marcados: {$total}");
        }
    }
}
