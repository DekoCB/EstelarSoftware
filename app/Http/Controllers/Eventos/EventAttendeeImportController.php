<?php

namespace App\Http\Controllers\Eventos;

use App\Exports\EventAttendeesTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Eventos\StoreEventAttendeesImportRequest;
use App\Imports\EventAttendeesImport;
use App\Models\Event;
use Maatwebsite\Excel\Facades\Excel;

class EventAttendeeImportController extends Controller
{
    public function plantilla(Event $evento)
    {
        return Excel::download(new EventAttendeesTemplateExport, 'plantilla_asistentes.xlsx');
    }

    public function crear(Event $evento)
    {
        return view('eventos.asistentes.importar', compact('evento'));
    }

    public function importar(StoreEventAttendeesImportRequest $request, Event $evento)
    {
        $import = new EventAttendeesImport($evento);

        Excel::import($import, $request->file('archivo'));

        $failures = $import->failures();

        if ($failures->isEmpty()) {
            return redirect()
                ->route('eventos.show', $evento)
                ->with('success', 'Asistentes importados correctamente.');
        }

        return redirect()
            ->route('eventos.asistentes.importar', $evento)
            ->with('warning', 'Se importaron los asistentes válidos. Algunas filas tuvieron errores.')
            ->with('import_failures', $failures);
    }
}
