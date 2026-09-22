<?php

namespace App\Livewire\Leads;

use App\Models\LeadResponseOption;
use App\Support\LeadResponseOptions;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class LeadResponseOptionsManager extends Component
{
    public bool $creando = false;
    public ?int $editandoId = null;
    public string $label = '';
    public string $color = '';

    public function render()
    {
        $opciones = LeadResponseOption::orderBy('orden')->get();

        return view('livewire.leads.lead-response-options-manager', compact('opciones'));
    }

    public function empezarCrear(): void
    {
        abort_unless(auth()->user()->can('leads.editar'), 403);

        $this->reset(['label', 'color', 'editandoId']);
        $this->creando = true;
    }

    public function empezarEditar(int $id): void
    {
        abort_unless(auth()->user()->can('leads.editar'), 403);

        $opcion = LeadResponseOption::findOrFail($id);
        $this->editandoId = $opcion->id;
        $this->label = $opcion->label;
        $this->color = $opcion->color;
        $this->creando = false;
    }

    public function cancelar(): void
    {
        $this->reset(['creando', 'editandoId', 'label', 'color']);
    }

    protected function rules(): array
    {
        return [
            'label' => ['required', 'string', 'max:60'],
            'color' => ['required', Rule::in(LeadResponseOptions::COLORES_DISPONIBLES)],
        ];
    }

    public function guardar(): void
    {
        abort_unless(auth()->user()->can('leads.editar'), 403);

        $data = $this->validate();

        if ($this->editandoId) {
            LeadResponseOption::findOrFail($this->editandoId)->update($data);
        } else {
            $clave = Str::slug($data['label'], '_');
            $original = $clave;
            $i = 1;
            while (LeadResponseOption::where('clave', $clave)->exists()) {
                $clave = $original . '_' . (++$i);
            }

            LeadResponseOption::create($data + [
                'clave' => $clave,
                'orden' => (LeadResponseOption::max('orden') ?? 0) + 1,
            ]);
        }

        $this->reset(['creando', 'editandoId', 'label', 'color']);

        $this->dispatch('opciones-actualizadas', opciones: LeadResponseOptions::opciones());
    }
}
