@extends('layouts.app')

@section('conteudo')
<div x-data="{ busca: '' }">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-white">Histórico</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">Atendimentos e agendamentos já finalizados</p>
    </div>

    <input
        type="text"
        x-model="busca"
        placeholder="Buscar por cliente..."
        class="w-72 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 placeholder-slate-400 dark:placeholder-slate-500 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-teal-200 dark:focus:ring-teal-800"
    >

    <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm transition-colors">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 text-left text-slate-500 dark:text-slate-400 border-b border-slate-100 dark:border-slate-800">
                        <th class="px-5 py-3 font-medium">Data</th>
                        <th class="px-5 py-3 font-medium">Cliente</th>
                        <th class="px-5 py-3 font-medium">Serviço</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @forelse ($historico as $item)
                        <tr
                            x-show="busca === '' || @js(strtolower($item->cliente->nome_completo)).includes(busca.toLowerCase())"
                            class="hover:bg-slate-50/50 dark:hover:bg-slate-800/40 transition"
                        >
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $item->data->format('d/m/Y') }} às {{ substr($item->horario, 0, 5) }}</td>
                            <td class="px-5 py-3 text-slate-800 dark:text-slate-200 font-medium">{{ $item->cliente->nome_completo }}</td>
                            <td class="px-5 py-3 text-slate-500 dark:text-slate-400">{{ $item->servico->nome }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $badge = match ($item->status) {
                                        'concluido' => ['Concluído', 'bg-emerald-50 dark:bg-emerald-950/70 text-emerald-700 dark:text-emerald-300 border border-emerald-200/50 dark:border-emerald-800/50'],
                                        'cancelado' => ['Cancelado', 'bg-rose-50 dark:bg-rose-950/70 text-rose-700 dark:text-rose-300 border border-rose-200/50 dark:border-rose-800/50'],
                                        default => [ucfirst($item->status), 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300'],
                                    };
                                @endphp
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badge[1] }}">
                                    {{ $badge[0] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-400 dark:text-slate-500">
                                Nenhum registro no histórico ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4">{{ $historico->links() }}</div>
</div>
@endsection
