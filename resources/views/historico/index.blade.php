@extends('layouts.app')

@section('conteudo')
<div x-data="{ busca: '' }">
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-slate-900">Histórico</h1>
        <p class="text-sm text-slate-500">Atendimentos e agendamentos já finalizados</p>
    </div>

        <input
            type="text"
            x-model="busca"
            placeholder="Buscar por cliente..."
            class="w-72 border border-slate-200 rounded-lg px-3 py-2 text-sm mb-4 focus:outline-none focus:ring-2 focus:ring-teal-200"
        >

        <div class="bg-white rounded-xl border border-slate-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 text-left text-slate-500 border-b border-slate-100">
                        <th class="px-5 py-3 font-medium">Data</th>
                        <th class="px-5 py-3 font-medium">Cliente</th>
                        <th class="px-5 py-3 font-medium">Serviço</th>
                        <th class="px-5 py-3 font-medium">Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($historico ?? [] as $item)
                        <tr
                            x-show="busca === '' || '{{ strtolower($item->cliente) }}'.includes(busca.toLowerCase())"
                            class="border-b border-slate-50 last:border-0"
                        >
                            <td class="px-5 py-3 text-slate-500">{{ $item->data->format('d/m/Y H:i') }}</td>
                            <td class="px-5 py-3 text-slate-700">{{ $item->cliente }}</td>
                            <td class="px-5 py-3 text-slate-500">{{ $item->servico }}</td>
                            <td class="px-5 py-3">
                                @php
                                    $badge = match ($item->status) {
                                        'concluido' => ['Concluído', 'bg-emerald-50 text-emerald-600'],
                                        'cancelado' => ['Cancelado', 'bg-red-50 text-red-500'],
                                        default => [ucfirst($item->status), 'bg-slate-100 text-slate-500'],
                                    };
                                @endphp
                                <span class="text-xs font-medium px-2.5 py-1 rounded-full {{ $badge[1] }}">
                                    {{ $badge[0] }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-5 py-10 text-center text-slate-400">
                                Nenhum registro no histórico ainda.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
</div>
@endsection
