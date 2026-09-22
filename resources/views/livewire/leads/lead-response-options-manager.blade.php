<div>
    <div class="bg-slate-900 rounded-2xl overflow-hidden">
        <div class="divide-y divide-slate-800/60 max-h-72 overflow-y-auto">
            @foreach($opciones as $op)
            <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem;" class="px-4 py-3">
                <div style="display:flex; align-items:center; gap:0.75rem;">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-{{ $op->color }}-500/15 text-{{ $op->color }}-400">
                        {{ $op->label }}
                    </span>
                    @if($op->requiere_fecha || $op->requiere_hora || $op->requiere_sistema || $op->comentario_auto)
                        <span class="text-[10px] text-slate-600 font-mono">
                            @if($op->requiere_fecha) fecha @endif
                            @if($op->requiere_hora) · hora @endif
                            @if($op->requiere_sistema) · sistema @endif
                            @if($op->comentario_auto) · comentario automático @endif
                        </span>
                    @endif
                </div>
                <button type="button" wire:click="empezarEditar({{ $op->id }})"
                        class="p-1.5 rounded-lg text-slate-500 hover:text-amber-400 hover:bg-amber-500/10 transition-colors duration-150"
                        title="Editar">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125"/>
                    </svg>
                </button>
            </div>
            @endforeach
        </div>
    </div>

    @if($creando || $editandoId)
        <div class="mt-4 bg-slate-800/40 border border-slate-700/40 rounded-xl p-4 space-y-3">
            <div>
                <label class="block text-xs font-medium text-slate-400 mb-1.5">Texto de la respuesta</label>
                <input type="text" wire:model="label" class="input-dark @error('label') error @enderror"
                       placeholder="Ej: Interesado más adelante">
                @error('label')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-400 mb-2">Color</label>
                <div style="display:flex; flex-wrap:wrap; gap:0.5rem;">
                    @foreach(\App\Support\LeadResponseOptions::COLORES_DISPONIBLES as $c)
                        <label style="cursor:pointer; display:inline-block; border-radius:0.6rem; padding:2px; {{ $color === $c ? 'box-shadow: 0 0 0 2px #fff;' : '' }}">
                            <input type="radio" wire:model="color" value="{{ $c }}" style="position:absolute; opacity:0; pointer-events:none;">
                            <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-{{ $c }}-500/15 text-{{ $c }}-400"
                                  style="transition: opacity 0.15s; opacity: {{ $color === $c ? '1' : '0.5' }};">
                                {{ ucfirst($c) }}
                            </span>
                        </label>
                    @endforeach
                </div>
                @error('color')
                    <p class="text-red-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div style="display:flex; align-items:center; justify-content:flex-end; gap:0.5rem;">
                <button type="button" wire:click="cancelar"
                        class="px-4 py-2 rounded-xl text-xs text-slate-400 hover:text-white
                               border border-slate-700/60 hover:bg-slate-800 transition-colors">
                    Cancelar
                </button>
                <button type="button" wire:click="guardar"
                        class="px-4 py-2 rounded-xl text-xs font-bold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500
                               hover:from-sky-400 hover:to-cyan-400 transition-all duration-200">
                    Guardar
                </button>
            </div>
        </div>
    @else
        <button type="button" wire:click="empezarCrear"
                style="display:inline-flex; align-items:center; gap:0.5rem;"
                class="mt-4 px-4 py-2 rounded-xl text-xs font-semibold
                       bg-sky-500/10 border border-sky-500/30 text-sky-400 hover:bg-sky-500/20
                       transition-colors duration-150">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
            </svg>
            Nueva opción
        </button>
    @endif
</div>
