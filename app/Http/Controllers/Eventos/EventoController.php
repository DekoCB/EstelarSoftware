<?php

namespace App\Http\Controllers\Eventos;

use App\Http\Controllers\Controller;
use App\Http\Requests\Eventos\StoreEventRequest;
use App\Http\Requests\Eventos\UpdateEventRequest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class EventoController extends Controller
{
    public function index()
    {
        $puedeVerTodos = auth()->user()->can('eventos.ver_todos');

        $eventos = Event::withCount(['leads' => function ($q) use ($puedeVerTodos) {
                if (!$puedeVerTodos) {
                    $q->where('created_by', auth()->id());
                }
            }])
            ->with('responsable')
            ->orderByDesc('fecha_inicio')
            ->paginate(20);

        return view('eventos.index', compact('eventos', 'puedeVerTodos'));
    }

    public function create()
    {
        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('eventos.create', compact('usuarios'));
    }

    public function store(StoreEventRequest $request)
    {
        $data = $request->safe()->except('imagen');

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('eventos', 'public');
        }

        $evento = Event::create($data + ['created_by' => auth()->id()]);

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Evento registrado correctamente.');
    }

    public function show(Event $evento)
    {
        $puedeVerTodos = auth()->user()->can('eventos.ver_todos');

        $evento->load([
            'responsable',
            'createdBy',
            'leads' => function ($q) use ($puedeVerTodos) {
                $q->with(['client', 'createdBy'])->orderByDesc('created_at');
                if (!$puedeVerTodos) {
                    $q->where('created_by', auth()->id());
                }
            },
            'asistentes' => fn ($q) => $q->orderByDesc('created_at'),
        ]);

        $kpis = [
            'total_leads'  => $evento->leads->count(),
            'convertidos'  => $evento->leads->where('estado', 'convertido')->count(),
            'contactados'  => $evento->leads->where('estado', 'contactado')->count(),
            'nuevos'       => $evento->leads->where('estado', 'nuevo')->count(),
            'total_asistentes' => $evento->asistentes->where('estado', '!=', 'cancelado')->count(),
            'asistieron'       => $evento->asistentes->where('estado', 'asistio')->count(),
        ];

        return view('eventos.show', compact('evento', 'kpis', 'puedeVerTodos'));
    }

    public function edit(Event $evento)
    {
        $usuarios = User::orderBy('name')->get(['id', 'name']);

        return view('eventos.edit', compact('evento', 'usuarios'));
    }

    public function update(UpdateEventRequest $request, Event $evento)
    {
        $data = $request->safe()->except(['imagen', 'delete_imagen']);

        if ($request->boolean('delete_imagen') && $evento->imagen) {
            Storage::disk('public')->delete($evento->imagen);
            $data['imagen'] = null;
        }

        if ($request->hasFile('imagen')) {
            if ($evento->imagen) {
                Storage::disk('public')->delete($evento->imagen);
            }
            $data['imagen'] = $request->file('imagen')->store('eventos', 'public');
        }

        $evento->update($data);

        return redirect()
            ->route('eventos.show', $evento)
            ->with('success', 'Evento actualizado correctamente.');
    }

    public function destroy(Event $evento)
    {
        if ($evento->imagen) {
            Storage::disk('public')->delete($evento->imagen);
        }

        $evento->delete();

        return redirect()
            ->route('eventos.index')
            ->with('success', 'Evento eliminado correctamente.');
    }
}
