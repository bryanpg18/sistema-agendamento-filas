@extends('layouts.app')

@section('conteudo')
<div>
    <div class="flex items-start justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Relatórios</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400">Resumo de atendimentos por período</p>
        </div>
    </div>

    {{-- Filtro de período --}}
    <form method="GET" class="flex flex-wrap items-end gap-3 mb-6 bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4 shadow-sm transition-colors">
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">De</label>
            <input type="date" name="de" value="{{ request('de') }}"
                   class="border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Até</label>
            <input type="date" name="ate" value="{{ request('ate') }}"
                   class="border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-950 text-slate-900 dark:text-slate-100 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-600">
        </div>
        <button type="submit" class="bg-teal-700 hover:bg-teal-800 dark:bg-teal-600 dark:hover:bg-teal-700 text-white text-sm font-medium px-4 py-2 rounded-lg transition">
            Filtrar
        </button>
    </form>

    {{-- Cards resumo --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        @php
            $cards = [
                ['label' => 'Total de atendimentos', 'value' => $totalAtendimentos ?? 0],
                ['label' => 'Concluídos', 'value' => $totalConcluidos ?? 0],
                ['label' => 'Cancelados', 'value' => $totalCancelados ?? 0],
                ['label' => 'Novos clientes', 'value' => $novosClientes ?? 0],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 p-4 shadow-sm transition-colors">
                <p class="text-sm text-slate-500 dark:text-slate-400 mb-2">{{ $card['label'] }}</p>
                <p class="text-3xl font-semibold text-slate-900 dark:text-white">{{ $card['value'] }}</p>
            </div>
        @endforeach
    </div>

    {{-- Tabela detalhada --}}
    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-left text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-5 py-3 font-medium">Serviço</th>
                        <th class="px-5 py-3 font-medium">Atendimentos</th>
                        <th class="px-5 py-3 font-medium">Receita</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($porServico ?? [] as $linha)
                        <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition">
                            <td class="px-5 py-3 text-slate-800 dark:text-slate-200 font-medium">{{ $linha->servico }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $linha->total }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">R$ {{ number_format($linha->receita, 2, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                Sem dados para o período selecionado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
