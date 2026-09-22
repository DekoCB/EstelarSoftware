<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-2 text-sm">
            <a href="{{ route('entregas.index') }}" class="text-slate-600 hover:text-slate-400">Entregas</a>
            <span class="text-slate-700">/</span>
            <span class="text-white font-semibold">Nueva acta</span>
        </div>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <div class="mb-6">
            <h2 class="text-xl font-bold text-white">Nueva acta de entrega</h2>
            <p class="text-sm text-slate-500 mt-0.5">Registra la entrega de un proyecto al cliente</p>
        </div>

        <form action="{{ route('entregas.store') }}" method="POST"
              x-data="entregaForm()" class="space-y-5">
            @csrf

            <div class="bg-slate-900 border border-slate-800/60 rounded-2xl p-5 space-y-5">

                {{-- Proyecto --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Proyecto <span class="text-rose-400">*</span>
                    </label>
                    <select name="project_id" x-model="proyectoId" @change="autoFillCliente"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors
                                   @error('project_id') border-rose-500/60 @enderror">
                        <option value="">-- Seleccionar proyecto --</option>
                        @foreach($proyectos as $p)
                        <option value="{{ $p->id }}"
                                data-client="{{ $p->client_id }}"
                                {{ old('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} — {{ $p->client->razon_social ?? '' }}
                        </option>
                        @endforeach
                    </select>
                    @error('project_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Cliente --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Cliente <span class="text-rose-400">*</span>
                    </label>
                    <select name="client_id" x-ref="clientSelect"
                            class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                   text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors
                                   @error('client_id') border-rose-500/60 @enderror">
                        <option value="">-- Seleccionar cliente --</option>
                        @foreach($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('client_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->razon_social }}
                        </option>
                        @endforeach
                    </select>
                    @error('client_id')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Título --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">
                        Título del acta <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="titulo" value="{{ old('titulo') }}"
                           placeholder="Ej: Entrega final Sistema de Facturación v1.0"
                           class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                  text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors
                                  @error('titulo') border-rose-500/60 @enderror">
                    @error('titulo')<p class="text-rose-400 text-xs mt-1">{{ $message }}</p>@enderror
                </div>

                {{-- Descripción --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Descripción</label>
                    <textarea name="descripcion" rows="3"
                              placeholder="Descripción general de lo que se entrega..."
                              class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                     text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60
                                     transition-colors resize-none">{{ old('descripcion') }}</textarea>
                </div>

                {{-- Fecha + Tipo + Estado --}}
                <div class="grid grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Fecha entrega <span class="text-rose-400">*</span></label>
                        <input type="date" name="fecha_entrega" value="{{ old('fecha_entrega', now()->toDateString()) }}"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                      text-sm text-white focus:outline-none focus:border-sky-500/60 transition-colors">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Tipo</label>
                        <select name="tipo"
                                class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            <option value="final" {{ old('tipo', 'final') === 'final' ? 'selected' : '' }}>Entrega final</option>
                            <option value="parcial" {{ old('tipo') === 'parcial' ? 'selected' : '' }}>Entrega parcial</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-400 mb-1.5">Estado</label>
                        <select name="estado"
                                class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                       text-sm text-slate-300 focus:outline-none focus:border-sky-500/60 transition-colors">
                            <option value="borrador" {{ old('estado', 'borrador') === 'borrador' ? 'selected' : '' }}>Borrador</option>
                            <option value="firmado" {{ old('estado') === 'firmado' ? 'selected' : '' }}>Firmado</option>
                            <option value="observado" {{ old('estado') === 'observado' ? 'selected' : '' }}>Observado</option>
                        </select>
                    </div>
                </div>

                {{-- Ítems entregados --}}
                <div>
                    <div class="flex items-center justify-between mb-2">
                        <label class="text-xs font-semibold text-slate-400">Ítems entregados</label>
                        <button type="button" @click="addItem"
                                class="text-xs text-sky-400 hover:text-sky-300 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                            </svg>
                            Agregar ítem
                        </button>
                    </div>
                    <div class="space-y-2">
                        <template x-for="(item, i) in items" :key="i">
                            <div class="flex gap-2">
                                <input type="text" :name="`items_entregados[${i}]`" x-model="items[i]"
                                       placeholder="Ej: Manual de usuario, Código fuente, Credenciales..."
                                       class="flex-1 bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2
                                              text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                                <button type="button" @click="removeItem(i)"
                                        class="p-2 text-slate-600 hover:text-rose-400 transition-colors">
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Firma del cliente --}}
                <div class="border-t border-slate-800/60 pt-5">
                    <p class="text-xs font-semibold text-slate-400 mb-3">Datos del firmante (cliente)</p>
                    <div class="grid grid-cols-3 gap-4">
                        <div class="col-span-2">
                            <label class="block text-xs text-slate-500 mb-1.5">Nombre completo</label>
                            <input type="text" name="firma_cliente" value="{{ old('firma_cliente') }}"
                                   placeholder="Nombre del representante"
                                   class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2
                                          text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                        </div>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1.5">DNI</label>
                            <input type="text" name="dni_firmante" value="{{ old('dni_firmante') }}"
                                   placeholder="12345678" maxlength="20"
                                   class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2
                                          text-sm text-white font-mono placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                        </div>
                    </div>
                    <div class="mt-3">
                        <label class="block text-xs text-slate-500 mb-1.5">Cargo</label>
                        <input type="text" name="cargo_firmante" value="{{ old('cargo_firmante') }}"
                               placeholder="Ej: Gerente General, Jefe de TI"
                               class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2
                                      text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60 transition-colors">
                    </div>
                </div>

                {{-- Observaciones --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1.5">Observaciones</label>
                    <textarea name="observaciones" rows="3"
                              placeholder="Pendientes, notas, condiciones especiales..."
                              class="w-full bg-slate-800/60 border border-slate-700/40 rounded-xl px-3 py-2.5
                                     text-sm text-white placeholder-slate-600 focus:outline-none focus:border-sky-500/60
                                     transition-colors resize-none">{{ old('observaciones') }}</textarea>
                </div>

            </div>

            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('entregas.index') }}"
                   class="px-4 py-2.5 rounded-xl text-sm font-semibold text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="px-6 py-2.5 rounded-xl text-sm font-semibold text-white
                               bg-gradient-to-r from-sky-500 to-cyan-500
                               shadow-[0_0_18px_rgba(14,165,233,0.35)] hover:shadow-[0_0_28px_rgba(14,165,233,0.55)]
                               transition-all active:scale-[0.98]">
                    Crear acta
                </button>
            </div>
        </form>
    </div>

    <script>
    function entregaForm() {
        return {
            proyectoId: '{{ old('project_id', '') }}',
            items: @json(old('items_entregados', [''])),
            addItem() { this.items.push(''); },
            removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
            autoFillCliente() {
                const sel = document.querySelector(`option[value="${this.proyectoId}"]`);
                if (sel) {
                    const clientId = sel.getAttribute('data-client');
                    const clientSel = this.$refs.clientSelect;
                    if (clientId && clientSel) clientSel.value = clientId;
                }
            }
        }
    }
    </script>
</x-app-layout>
