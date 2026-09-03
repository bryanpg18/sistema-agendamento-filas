@extends('layouts.app')

@section('titulo', 'Novo Agendamento')

@section('conteudo')
    <div class="max-w-2xl mx-auto"
         x-data="{
            data: '{{ old('data') }}',
            horarios: [],
            carregando: false,
            async buscarHorarios() {
                if (! this.data) {
                    this.horarios = [];
                    return;
                }
                this.carregando = true;
                try {
                    const resposta = await fetch(`{{ route('agendamentos.horarios-disponiveis') }}?data=${this.data}`, {
                        headers: { Accept: 'application/json' },
                    });
                    const json = await resposta.json();
                    this.horarios = json.horarios ?? [];
                } catch (erro) {
                    this.horarios = [];
                } finally {
                    this.carregando = false;
                }
            },
         }"
         x-init="buscarHorarios()">
        <div class="mb-6">
            <p class="text-sm text-gray-400">
                <a href="{{ route('agendamentos.index') }}" class="hover:underline">Agendamentos</a> &gt; Novo
            </p>
            <h1 class="text-2xl font-bold text-gray-800 mt-1">Novo Agendamento</h1>
        </div>

        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h2 class="font-semibold text-gray-700 mb-4">Dados do agendamento</h2>

            <form method="POST" action="{{ route('agendamentos.store') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Cliente</label>
                    <div class="relative">
                        <select name="cliente_id"
                                class="w-full appearance-none border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                            <option value="">Selecione o cliente</option>
                            @foreach ($clientes as $cliente)
                                <option value="{{ $cliente->id }}" @selected(old('cliente_id') == $cliente->id)>
                                    {{ $cliente->nome_completo }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    @error('cliente_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Serviço</label>
                    <div class="relative">
                        <select name="servico_id"
                                class="w-full appearance-none border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                            <option value="">Selecione o serviço</option>
                            @foreach ($servicos as $servico)
                                <option value="{{ $servico->id }}" @selected(old('servico_id') == $servico->id)>
                                    {{ $servico->nome }}
                                </option>
                            @endforeach
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                        </svg>
                    </div>
                    @error('servico_id') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Data</label>
                        <input type="date" name="data" x-model="data" @change="buscarHorarios()"
                               min="{{ now()->toDateString() }}"
                               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700">
                        @error('data') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-sm text-gray-600 mb-1">Horário</label>
                        <div class="relative">
                            <select name="horario" :disabled="! data || carregando"
                                    class="w-full appearance-none border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700 disabled:bg-gray-50 disabled:text-gray-400">
                                <template x-if="! data">
                                    <option value="">Selecione a data primeiro</option>
                                </template>
                                <template x-if="data && carregando">
                                    <option value="">Carregando...</option>
                                </template>
                                <template x-if="data && ! carregando && horarios.length === 0">
                                    <option value="">Nenhum horário disponível</option>
                                </template>
                                <template x-if="data && ! carregando && horarios.length > 0">
                                    <option value="">Selecione o horário</option>
                                </template>
                                <template x-for="hora in horarios" :key="hora">
                                    <option :value="hora" x-text="hora" :selected="hora === '{{ old('horario') }}'"></option>
                                </template>
                            </select>
                            <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                            </svg>
                        </div>
                        @error('horario') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-sm text-gray-600 mb-1">Observações</label>
                    <textarea name="observacoes" rows="3"
                              class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-700"
                              placeholder="Informações adicionais (opcional)">{{ old('observacoes') }}</textarea>
                    @error('observacoes') <p class="text-red-600 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <a href="{{ route('agendamentos.index') }}"
                       class="px-4 py-2 rounded-lg text-sm font-medium text-gray-600 border border-gray-200 hover:bg-gray-50">
                        Cancelar
                    </a>
                    <button type="submit"
                            class="bg-teal-700 hover:bg-teal-800 text-white px-5 py-2 rounded-lg text-sm font-medium">
                        Agendar
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection
