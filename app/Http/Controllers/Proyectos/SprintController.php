<?php

namespace App\Http\Controllers\Proyectos;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Sprint;
use Illuminate\Http\Request;

class SprintController extends Controller
{
    public function index(Project $proyecto)
    {
        $proyecto->load('sprints');
        return view('sprints.index', compact('proyecto'));
    }

    public function store(Request $request, Project $proyecto)
    {
        $data = $request->validate([
            'name'       => ['required', 'string', 'max:150'],
            'goal'       => ['nullable', 'string'],
            'start_date' => ['nullable', 'date'],
            'end_date'   => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $proyecto->sprints()->create($data + ['created_by' => auth()->id()]);

        return redirect()
            ->route('sprints.index', $proyecto)
            ->with('success', 'Sprint creado correctamente.');
    }

    public function show(Project $proyecto, Sprint $sprint)
    {
        $sprint->load([
            'requirements.assignedTo',
            'requirements.phase',
            'dailyReports.user',
        ]);

        $backlog = $proyecto->requirements()
            ->whereNull('sprint_id')
            ->with('assignedTo')
            ->orderBy('priority')
            ->get();

        $dailyHoy = $sprint->dailyReports()
            ->where('user_id', auth()->id())
            ->whereDate('date', today())
            ->first();

        return view('sprints.show', compact('proyecto', 'sprint', 'backlog', 'dailyHoy'));
    }

    public function update(Request $request, Project $proyecto, Sprint $sprint)
    {
        $data = $request->validate([
            'status' => ['required', 'in:' . implode(',', Sprint::STATUSES)],
        ]);

        $sprint->update($data);

        return back()->with('success', 'Sprint actualizado.');
    }
}
