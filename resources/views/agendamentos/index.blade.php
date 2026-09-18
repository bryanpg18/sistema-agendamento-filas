@extends('layouts.app')

@section('titulo', 'Agendamentos')

@section('conteudo')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Agendamentos</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Gerencie os agendamentos da fila</p>
        </div>
        <a href="{{ route('agendamentos.create') }}"
           class="rounded-lg bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 px-4 py-2.5 text-sm font-medium text-white transition">
            + Novo Agendamento
        </a>
    </div>

    <form method="GET" class="mb-4 flex flex-wrap items-end gap-4">
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Cliente</label>
            <input
                type="text"
                name="busca"
                value="{{ $buscaSelecionada }}"
                placeholder="Buscar por nome do cliente..."
                class="w-64 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200 dark:focus:ring-teal-800"
            >
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Data</label>
            <input
                type="date"
                name="data"
                value="{{ $dataSelecionada }}"
                class="rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200 dark:focus:ring-teal-800"
            >
        </div>
        <div>
            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
            <div class="relative">
                <select
                    name="status"
                    class="min-w-[160px] appearance-none rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 py-2 pl-3 pr-9 text-sm focus:outline-none focus:ring-2 focus:ring-teal-200 dark:focus:ring-teal-800"
                >
                    <option value="">Todos</option>
                    <option value="confirmado" @selected($statusSelecionado === 'confirmado')>Confirmado</option>
                    <option value="em_espera" @selected($statusSelecionado === 'em_espera')>Em espera</option>
                    <option value="em_atendimento" @selected($statusSelecionado === 'em_atendimento')>Em atendimento</option>
                    <option value="concluido" @selected($statusSelecionado === 'concluido')>Concluído</option>
                    <option value="cancelado" @selected($statusSelecionado === 'cancelado')>Cancelado</option>
                </select>
                <svg class="pointer-events-none absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
                </svg>
            </div>
        </div>
        <button type="submit" class="rounded-lg bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 px-5 py-2.5 text-sm font-medium text-white transition">
            Buscar
        </button>
        @if ($buscaSelecionada || $dataSelecionada || $statusSelecionado)
            <a href="{{ route('agendamentos.index') }}" class="pb-2.5 text-sm text-slate-500 dark:text-slate-400 hover:underline">
                Limpar filtros
            </a>
        @endif
    </form>

    <div class="overflow-hidden rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/60 text-left text-slate-500 dark:text-slate-400">
                        <th class="px-5 py-3 font-medium">Cliente</th>
                        <th class="px-5 py-3 font-medium">Serviço</th>
                        <th class="px-5 py-3 font-medium">Data</th>
                        <th class="px-5 py-3 font-medium">Horário</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                        <th class="px-5 py-3 font-medium">Observações</th>
                        <th class="px-5 py-3 font-medium">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($agendamentos as $agendamento)
                        @php
                            $estiloStatus = match ($agendamento->status) {
                                'confirmado' => 'bg-teal-50 dark:bg-teal-950/70 text-teal-700 dark:text-teal-300 border border-teal-200/50 dark:border-teal-800/50',
                                'em_espera' => 'bg-amber-50 dark:bg-amber-950/70 text-amber-700 dark:text-amber-300 border border-amber-200/50 dark:border-amber-800/50',
                                'em_atendimento' => 'bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-200/50 dark:border-blue-800/50',
                                'concluido' => 'bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50',
                                'cancelado' => 'bg-red-50 dark:bg-red-950/70 text-red-700 dark:text-red-300 border border-red-200/50 dark:border-red-800/50',
                                default => 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300',
                            };
                            $rotuloStatus = match ($agendamento->status) {
                                'confirmado' => 'Confirmado',
                                'em_espera' => 'Em espera',
                                'em_atendimento' => 'Em atendimento',
                                'concluido' => 'Concluído',
                                'cancelado' => 'Cancelado',
                                default => ucfirst($agendamento->status),
                            };
                        @endphp
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3 text-slate-800 dark:text-slate-200 font-medium">{{ $agendamento->cliente->nome_completo }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $agendamento->servico->nome }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $agendamento->data->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ substr($agendamento->horario, 0, 5) }}</td>
                            <td class="px-5 py-3">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium {{ $estiloStatus }}">
                                    {{ $rotuloStatus }}
                                </span>
                            </td>
                            <td class="max-w-xs truncate px-5 py-3 text-slate-500 dark:text-slate-400" title="{{ $agendamento->observacoes }}">
                                {{ $agendamento->observacoes ?? '-' }}
                            </td>
                            <td class="whitespace-nowrap px-5 py-3">
                                @if (! in_array($agendamento->status, ['cancelado', 'concluido'], true))
                                    <a href="{{ route('agendamentos.edit', $agendamento) }}" class="mr-3 text-teal-700 dark:text-teal-400 hover:underline">Editar</a>
                                    <form action="{{ route('agendamentos.cancelar', $agendamento) }}" method="POST" class="inline"
                                          onsubmit="return confirm('Cancelar este agendamento?')">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="text-rose-500 hover:underline">Cancelar</button>
                                    </form>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                Nenhum agendamento encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">
        {{ $agendamentos->links() }}
    </div>
</div>
@endsection
