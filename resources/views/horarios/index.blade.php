@extends('layouts.app')
@section('titulo', 'Horários')
@section('conteudo')
<div>
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Horários Disponíveis</h1>
        <p class="text-sm text-slate-500">Horários &gt; Disponíveis</p>
    </div>
    <form method="GET" class="mb-6 flex flex-wrap items-end gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Data</label>
            <input
                type="date"
                name="data"
                value="{{ $dataSelecionada }}"
                class="rounded-lg border border-slate-200 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200"
            >
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Serviço</label>
            <div class="relative">
                <select
                    name="servico_id"
                    class="appearance-none rounded-lg border border-slate-200 pl-3 pr-9 py-2 text-sm min-w-[140px] focus:outline-none focus:ring-2 focus:ring-teal-200"
                >
                    <option value="">Todos</option>
                    @foreach ($servicos as $servico)
                        <option value="{{ $servico->id }}" @selected($servicoSelecionado == $servico->id)>
                            {{ $servico->nome }}
                        </option>
                    @endforeach
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700">Status</label>
            <div class="relative">
                <select
                    name="status"
                    class="min-w-[140px] appearance-none rounded-lg border border-slate-200 py-2 pl-3 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200"
                >
                    <option value="">Todos</option>
                    <option value="disponivel" @selected($statusSelecionado === 'disponivel')>Disponível</option>
                    <option value="agendado" @selected($statusSelecionado === 'agendado')>Agendado</option>
                    <option value="cancelado" @selected($statusSelecionado === 'cancelado')>Cancelado</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <button type="submit" class="rounded-lg bg-teal-700 px-5 py-2.5 text-sm font-medium text-white hover:bg-teal-800">
            Buscar
        </button>
    </form>

    <div class="mb-4 flex gap-4 text-xs text-slate-500">
        <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-teal-400"></span> Disponível</span>
        <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-amber-400"></span> Agendado</span>
        <span class="flex items-center gap-1"><span class="h-3 w-3 rounded-full bg-red-400"></span> Cancelado</span>
    </div>

    <div class="rounded-xl border border-slate-100 bg-white p-6">
        <h2 class="mb-4 text-sm font-medium text-slate-700">Horários</h2>
        @if ($horarios->isEmpty())
            <p class="py-10 text-center text-sm text-slate-400">
                Nenhum horário encontrado para os filtros selecionados.
            </p>
        @else
            <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                @foreach ($horarios as $horario)
                    @php
                        $estilo = match ($horario->status_exibicao) {
                            'disponivel' => 'border-teal-200 bg-teal-50 text-teal-800 hover:bg-teal-100 cursor-pointer',
                            'agendado' => 'border-amber-200 bg-amber-50 text-amber-800 cursor-not-allowed',
                            'cancelado' => 'border-red-200 bg-red-50 text-red-700 cursor-not-allowed',
                        };
                    @endphp
                    <div
                        class="rounded-lg border px-4 py-3 text-sm font-medium text-center {{ $estilo }}"
                        title="{{ ucfirst($horario->status_exibicao) }}"
                    >
                        {{ $horario->hora_formatada }}
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
