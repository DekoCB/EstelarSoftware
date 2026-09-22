<?php

namespace App\Http\Controllers\Proyectos;

use App\Http\Controllers\Controller;
use App\Models\DailyReport;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Http\Request;

class DailyReportController extends Controller
{
    public function store(Request $request, Project $proyecto, Sprint $sprint)
    {
        $data = $request->validate([
            'yesterday' => ['required', 'string', 'max:1000'],
            'today'     => ['required', 'string', 'max:1000'],
            'blockers'  => ['nullable', 'string', 'max:500'],
        ]);

        DailyReport::updateOrCreate(
            [
                'project_id' => $proyecto->id,
                'sprint_id'  => $sprint->id,
                'user_id'    => auth()->id(),
                'date'       => today(),
            ],
            $data
        );

        return back()->with('success', 'Daily standup registrado.');
    }
}
