<?php

namespace App\Http\Controllers\Leads;

use App\Exports\LeadsTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Leads\StoreLeadsImportRequest;
use App\Imports\LeadsImport;
use Maatwebsite\Excel\Facades\Excel;

class LeadImportController extends Controller
{
    public function plantilla()
    {
        return Excel::download(new LeadsTemplateExport, 'plantilla_leads.xlsx');
    }

    public function crear()
    {
        return view('leads.importar');
    }

    public function importar(StoreLeadsImportRequest $request)
    {
        $import = new LeadsImport;

        Excel::import($import, $request->file('archivo'));

        $failures = $import->failures();

        if ($failures->isEmpty()) {
            return redirect()
                ->route('leads.index')
                ->with('success', 'Leads importados correctamente.');
        }

        return redirect()
            ->route('leads.importar')
            ->with('warning', 'Se importaron los leads válidos. Algunas filas tuvieron errores.')
            ->with('import_failures', $failures);
    }
}
