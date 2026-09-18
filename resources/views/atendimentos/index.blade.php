@extends('layouts.app')

@section('conteudo')
<div>
    <h1 class="text-2xl font-semibold text-slate-900 dark:text-white mb-2">Atendimentos</h1>

    {{-- Abas de status --}}
    <div class="flex space-x-6 border-b border-slate-200 dark:border-slate-800 mb-4 overflow-x-auto">
        <a href="{{ route('atendimentos.index', ['status' => 'em_espera']) }}"
           class="pb-2 text-sm font-medium transition-colors {{ $statusAtivo === 'em_espera' ? 'border-b-2 border-teal-600 text-teal-600 dark:border-teal-400 dark:text-teal-400' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            Em espera ({{ $contagens['em_espera'] }})
        </a>
        <a href="{{ route('atendimentos.index', ['status' => 'em_atendimento']) }}"
           class="pb-2 text-sm font-medium transition-colors {{ $statusAtivo === 'em_atendimento' ? 'border-b-2 border-teal-600 text-teal-600 dark:border-teal-400 dark:text-teal-400' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            Em atendimento ({{ $contagens['em_atendimento'] }})
        </a>
        <a href="{{ route('atendimentos.index', ['status' => 'concluido']) }}"
           class="pb-2 text-sm font-medium transition-colors {{ $statusAtivo === 'concluido' ? 'border-b-2 border-teal-600 text-teal-600 dark:border-teal-400 dark:text-teal-400' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            Concluídos ({{ $contagens['concluido'] }})
        </a>
        <a href="{{ route('atendimentos.index', ['status' => 'cancelado']) }}"
           class="pb-2 text-sm font-medium transition-colors {{ $statusAtivo === 'cancelado' ? 'border-b-2 border-teal-600 text-teal-600 dark:border-teal-400 dark:text-teal-400' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            Cancelados ({{ $contagens['cancelado'] }})
        </a>
    </div>

    {{-- Tabela de agendamentos --}}
    <div class="overflow-hidden rounded-xl border border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-sm transition-colors">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Cliente</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Serviço</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Data</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Horário</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Status</th>
                        <th class="px-5 py-3 text-left text-sm font-medium text-slate-500 dark:text-slate-400">Ações</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse($agendamentos as $agendamento)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3 text-sm text-slate-800 dark:text-slate-200 font-medium">{{ $agendamento->cliente->nome_completo }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $agendamento->servico->nome }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ $agendamento->data->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-sm text-slate-500 dark:text-slate-400">{{ substr($agendamento->horario, 0, 5) }}</td>
                            <td class="px-5 py-3 text-sm text-slate-700 dark:text-slate-300">{{ ucfirst($agendamento->status) }}</td>
                            <td class="px-5 py-3 text-sm whitespace-nowrap">
                                @if($statusAtivo === 'em_espera')
                                    <form method="POST" action="{{ route('atendimentos.iniciar', $agendamento) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-3 py-1.5 bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 text-white rounded-lg text-xs font-medium transition">
                                            Iniciar
                                        </button>
                                    </form>
                                @elseif($statusAtivo === 'em_atendimento')
                                    <form method="POST" action="{{ route('atendimentos.finalizar', $agendamento) }}">
                                        @csrf
                                        @method('PATCH')
                                        <button class="px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-medium transition">
                                            Finalizar
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 dark:text-slate-600 text-xs">-</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-10 text-center text-sm text-slate-400 dark:text-slate-500">
                                Nenhum atendimento encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
